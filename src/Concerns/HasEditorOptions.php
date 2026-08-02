<?php

namespace Kahusoftware\FilamentCkeditorField\Concerns;

use Closure;
use Filament\Support\RawJs;

/**
 * Resolves the configuration handed to ClassicEditor.create().
 *
 * Defaults come from the package config file, which an application can publish
 * and edit, override globally with CKEditor::configureUsing(), or override for a
 * single field with the methods below. Later overrides win.
 */
trait HasEditorOptions
{
    /**
     * @var array<int, array | Closure>
     */
    protected array $editorOptions = [];

    /**
     * @var array<int, array | Closure>
     */
    protected array $disabledPlugins = [];

    /**
     * @var array<int, array | Closure>
     */
    protected array $enabledPlugins = [];

    /**
     * @var array<int, array | Closure>
     */
    protected array $disabledToolbarItems = [];

    /**
     * Merge options over the resolved defaults for this field.
     */
    public function editorOptions(array | Closure $options): static
    {
        $this->editorOptions[] = $options;

        return $this;
    }

    /**
     * Remove plugins from the resolved plugin list.
     *
     * Disabling a plugin hands whatever markup it owned back to General HTML
     * Support, so pair this with an `htmlSupport` rule when the goal is to strip
     * that markup rather than merely hide the feature's toolbar button.
     */
    public function disablePlugins(array | Closure $plugins): static
    {
        $this->disabledPlugins[] = $plugins;

        return $this;
    }

    /**
     * Re-enable plugins that the config file or a global override disabled.
     */
    public function enablePlugins(array | Closure $plugins): static
    {
        $this->enabledPlugins[] = $plugins;

        return $this;
    }

    public function disableToolbarItems(array | Closure $items): static
    {
        $this->disabledToolbarItems[] = $items;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getEditorOptions(): array
    {
        // Laravel's mergeConfigFrom() only merges top-level config keys, so an
        // application config that declares a partial `editor` key would
        // otherwise replace the entire subtree and silently drop every default
        // it did not restate, plugins and toolbar included. Merge the
        // application's value over the package defaults so omitted keys fall
        // back instead of vanishing.
        $editor = static::mergeOptions(
            static::defaultEditorConfig(),
            config('filament-ckeditor-field.editor', []),
        );

        $options = $editor['options'] ?? [];

        foreach ($this->editorOptions as $override) {
            $options = static::mergeOptions($options, $this->evaluate($override) ?? []);
        }

        $disabledPlugins = array_merge(
            $editor['disabled_plugins'] ?? [],
            $this->evaluateToList($this->disabledPlugins),
        );

        $disabledPlugins = array_diff($disabledPlugins, $this->evaluateToList($this->enabledPlugins));

        $disabledToolbarItems = array_merge(
            $editor['disabled_toolbar_items'] ?? [],
            $this->evaluateToList($this->disabledToolbarItems),
        );

        if (filled($uploadUrl = $this->getUploadUrl())) {
            $options['simpleUpload'] = [
                'uploadUrl' => $uploadUrl,
                'withCredentials' => true,
                'headers' => [
                    'X-CSRF-TOKEN' => csrf_token(),
                ],
            ];
        } else {
            $disabledPlugins = array_merge($disabledPlugins, $editor['upload_only_plugins'] ?? []);
            $disabledToolbarItems = array_merge($disabledToolbarItems, $editor['upload_only_toolbar_items'] ?? []);
        }

        $options['plugins'] = array_values(array_diff($editor['plugins'] ?? [], $disabledPlugins));

        if (isset($options['toolbar']['items'])) {
            $options['toolbar']['items'] = static::tidyToolbarItems(
                array_diff($options['toolbar']['items'], $disabledToolbarItems),
            );
        }

        $options['placeholder'] = $this->getPlaceholder();

        return $options;
    }

    /**
     * The resolved options as a JavaScript object literal.
     */
    public function getEditorOptionsJs(): string
    {
        return static::encodeOptions($this->getEditorOptions());
    }

    /**
     * Merge two option arrays. String-keyed arrays combine recursively, while
     * list-shaped arrays are replaced outright so that overriding a list swaps
     * it wholesale instead of merging it index by index.
     *
     * @param  array<string, mixed>  $base
     * @param  array<string, mixed>  $override
     * @return array<string, mixed>
     */
    public static function mergeOptions(array $base, array $override): array
    {
        foreach ($override as $key => $value) {
            if (
                is_array($value)
                && is_array($base[$key] ?? null)
                && static::isOptionMap($value)
                && static::isOptionMap($base[$key])
            ) {
                $base[$key] = static::mergeOptions($base[$key], $value);

                continue;
            }

            $base[$key] = $value;
        }

        return $base;
    }

    /**
     * The `editor` block of the package's own config file, which is the single
     * source of truth for the defaults. Read directly rather than through the
     * config repository because the repository's copy may have been replaced,
     * wholly or per top-level key, by a published application config.
     *
     * @return array<string, mixed>
     */
    protected static function defaultEditorConfig(): array
    {
        static $defaults = null;

        return $defaults ??= (require dirname(__DIR__, 2) . '/config/filament-ckeditor-field.php')['editor'];
    }

    /**
     * Encode options for a script context, emitting `js:` strings and RawJs
     * instances as bare expressions.
     *
     * @param  array<string, mixed>  $options
     */
    public static function encodeOptions(array $options): string
    {
        $expressions = [];

        $json = json_encode(
            static::extractJsExpressions($options, $expressions),
            JSON_UNESCAPED_SLASHES
                | JSON_HEX_TAG
                | JSON_HEX_AMP
                | JSON_HEX_APOS
                | JSON_HEX_QUOT
                | JSON_THROW_ON_ERROR,
        );

        // Quoting is escaped above, so a placeholder token can only appear here
        // because extractJsExpressions put it there.
        foreach ($expressions as $token => $expression) {
            $json = str_replace('"' . $token . '"', $expression, $json);
        }

        return $json;
    }

    /**
     * Replace JavaScript expressions with placeholder tokens, collecting them
     * for substitution once the surrounding structure has been encoded.
     *
     * @param  array<string, string>  $expressions
     */
    protected static function extractJsExpressions(mixed $value, array &$expressions): mixed
    {
        if ($value instanceof RawJs) {
            return static::tokenForJsExpression((string) $value, $expressions);
        }

        if (is_string($value) && str_starts_with($value, static::jsExpressionPrefix())) {
            return static::tokenForJsExpression(
                substr($value, strlen(static::jsExpressionPrefix())),
                $expressions,
            );
        }

        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = static::extractJsExpressions($item, $expressions);
            }
        }

        return $value;
    }

    /**
     * @param  array<string, string>  $expressions
     */
    protected static function tokenForJsExpression(string $expression, array &$expressions): string
    {
        $token = '__CKEDITOR_RAW_JS_' . count($expressions) . '__';

        $expressions[$token] = $expression;

        return $token;
    }

    /**
     * Drop separators that no longer divide anything, which CKEditor would
     * otherwise render as dividers with nothing between them.
     *
     * @param  array<int, string>  $items
     * @return array<int, string>
     */
    protected static function tidyToolbarItems(array $items): array
    {
        $tidied = [];

        foreach ($items as $item) {
            if ($item === '|' && ($tidied === [] || end($tidied) === '|')) {
                continue;
            }

            $tidied[] = $item;
        }

        while (end($tidied) === '|') {
            array_pop($tidied);
        }

        return array_values($tidied);
    }

    /**
     * @param  array<int, array | Closure>  $values
     * @return array<int, string>
     */
    protected function evaluateToList(array $values): array
    {
        $list = [];

        foreach ($values as $value) {
            $evaluated = $this->evaluate($value);

            if (is_array($evaluated)) {
                $list = array_merge($list, $evaluated);
            }
        }

        return $list;
    }

    /**
     * @param  array<mixed>  $value
     */
    protected static function isOptionMap(array $value): bool
    {
        return $value !== [] && ! array_is_list($value);
    }

    /**
     * Prefix marking a config string that is written into the page as a bare
     * JavaScript expression rather than a quoted string.
     *
     * A method rather than a constant because traits could not declare
     * constants before PHP 8.2, and this package supports 8.1.
     */
    protected static function jsExpressionPrefix(): string
    {
        return 'js:';
    }
}
