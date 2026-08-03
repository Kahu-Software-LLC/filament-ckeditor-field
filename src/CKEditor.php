<?php

namespace Kahusoftware\FilamentCkeditorField;

use Closure;
use DOMDocument;
use Filament\Forms\Components\Field;
use Kahusoftware\FilamentCkeditorField\Concerns\HasEditorOptions;

class CKEditor extends Field
{
    use HasEditorOptions;

    protected string | Closure $content = '';

    protected string $name = 'ckeditor';

    protected int $minLength = 0;

    protected string | Closure | null $uploadUrl = null;

    protected bool $uploadUrlExplicitlySet = false;

    protected string $placeholder = 'Type or paste your content here...';

    protected string $view = 'filament-ckeditor-field::ckeditor';

    public static function make(?string $name = null): static
    {
        $field = app(static::class, [
            'name' => $name ?? 'ckeditor',
        ]);

        // Filament's own Field::make() calls this, and skipping it meant
        // CKEditor::configureUsing() callbacks were silently never applied.
        $field->configure();

        return $field;
    }

    public function uploadUrl(string | Closure | null $uploadUrl): self
    {
        $this->uploadUrl = $uploadUrl;
        $this->uploadUrlExplicitlySet = true;

        return $this;
    }

    public function content(string | Closure $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function placeholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function getContent(): string
    {
        return $this->evaluate($this->content);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    public function getUploadUrl(): ?string
    {
        if ($this->uploadUrlExplicitlySet) {
            return $this->evaluate($this->uploadUrl);
        }

        // If not explicitly set, use config value as default
        return config('filament-ckeditor-field.upload_url');
    }

    /**
     * The image URLs present in the old document but absent from the new one,
     * so an application can clean up storage after a save. The package never
     * deletes anything itself; files must survive while editing so undo and
     * redo keep working, which makes save time the earliest safe moment to
     * reconcile.
     *
     * Comparison is by exact `src` value. Pass `$urlPrefix` to restrict the
     * result to the application's own uploads, so external or hotlinked
     * images can never end up on a deletion list.
     *
     * @return array<int, string>
     */
    public static function findRemovedImages(?string $oldHtml, ?string $newHtml, ?string $urlPrefix = null): array
    {
        $removed = array_diff(
            static::extractImageUrls($oldHtml),
            static::extractImageUrls($newHtml),
        );

        if ($urlPrefix !== null) {
            $removed = array_filter(
                $removed,
                fn (string $url): bool => str_starts_with($url, $urlPrefix),
            );
        }

        return array_values($removed);
    }

    /**
     * @return array<int, string>
     */
    protected static function extractImageUrls(?string $html): array
    {
        if ($html === null || trim($html) === '') {
            return [];
        }

        $document = new DOMDocument();

        // The editor emits HTML fragments rather than full documents, so the
        // parser is told the encoding up front and recovers from anything
        // malformed instead of reporting it.
        $usedInternalErrors = libxml_use_internal_errors(true);

        try {
            $document->loadHTML(
                '<?xml encoding="utf-8"?>' . $html,
                LIBXML_NOERROR | LIBXML_NOWARNING,
            );
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($usedInternalErrors);
        }

        $urls = [];

        foreach ($document->getElementsByTagName('img') as $image) {
            $src = $image->getAttribute('src');

            if ($src === '') {
                continue;
            }

            $urls[] = $src;
        }

        return array_values(array_unique($urls));
    }
}
