<?php

use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\View\View;
use Kahusoftware\FilamentCkeditorField\CKEditor;
use Livewire\Component;
use Livewire\Livewire;

/**
 * Render a single CKEditor field configured from plain data and return the
 * resulting HTML, so tests can assert on the editor configuration that actually
 * reaches the browser.
 *
 * The configuration is delivered through the ckeditorField Alpine component's
 * x-data payload, an HTML attribute, so quoted JSON fragments appear
 * entity-escaped in the rendered output and expectations pass through e().
 *
 * Livewire re-instantiates the component class, so the configuration travels as
 * mount data rather than as constructor arguments or a closure.
 *
 * @param  array<string, mixed>  $config
 */
function renderCKEditorField(array $config = []): string
{
    $component = new class extends Component implements HasSchemas
    {
        use InteractsWithSchemas;

        public ?string $content = null;

        /** @var array<string, mixed> */
        public array $fieldConfig = [];

        /** @param array<string, mixed> $fieldConfig */
        public function mount(array $fieldConfig = []): void
        {
            $this->fieldConfig = $fieldConfig;
        }

        public function form(Schema $schema): Schema
        {
            $field = CKEditor::make('content');

            foreach ($this->fieldConfig as $method => $argument) {
                $field = $field->{$method}($argument);
            }

            return $schema->components([$field])->statePath('data');
        }

        public function render(): View
        {
            return view('test::test-form');
        }
    };

    return Livewire::test($component::class, ['fieldConfig' => $config])
        ->assertSuccessful()
        ->html();
}

/**
 * The ckeditorField x-data payload of the rendered field, isolated so that
 * expectations cannot accidentally match the Livewire snapshot attribute,
 * which repeats the mount data in the same entity-escaped form.
 *
 * @param  array<string, mixed>  $config
 */
function renderCKEditorFieldPayload(array $config = []): string
{
    $html = renderCKEditorField($config);

    $start = strpos($html, 'ckeditorField({');

    // The payload is entity-escaped, so a literal `})"` can only be the end
    // of the x-data attribute.
    $end = strpos($html, '})"', $start);

    expect($start)->not->toBeFalse()
        ->and($end)->not->toBeFalse();

    return substr($html, $start, $end - $start);
}

it('renders the resolved editor options into the component payload', function () {
    $html = renderCKEditorField();

    expect($html)
        ->toContain('ckeditorField({')
        ->toContain(e('"plugins":['))
        ->toContain('AccessibilityHelp')
        ->toContain(e('"toolbar":{'))
        ->toContain(e('"htmlSupport":{'));
});

it('serialises plugin names rather than constructors', function () {
    // Plugin constructors cannot be serialised, so the config carries plugin
    // names that the ckeditorField component resolves against the window
    // scope at runtime, skipping any that are not bundled.
    expect(renderCKEditorField())->toContain(e('"Bold"'));
});

it('renders the html support regular expression as a bare expression', function () {
    expect(renderCKEditorField())->toContain(e('"name":') . '/^.*$/');
});

it('renders the default configuration with the colour features intact', function () {
    expect(renderCKEditorFieldPayload())
        ->toContain(e('"FontColor"'))
        ->toContain(e('"FontBackgroundColor"'))
        ->toContain(e('"Highlight"'))
        ->toContain(e('"fontColor"'))
        ->toContain(e('"fontBackgroundColor"'))
        ->toContain(e('"highlight"'));
});

it('omits disabled plugins and toolbar items from the rendered config', function () {
    $payload = renderCKEditorFieldPayload([
        'disablePlugins' => ['FontColor', 'FontBackgroundColor', 'Highlight'],
        'disableToolbarItems' => ['fontColor', 'fontBackgroundColor', 'highlight'],
    ]);

    expect($payload)
        ->not->toContain(e('"FontColor"'))
        ->not->toContain(e('"FontBackgroundColor"'))
        ->not->toContain(e('"Highlight"'))
        ->not->toContain(e('"fontColor"'))
        ->not->toContain(e('"fontBackgroundColor"'))
        ->not->toContain(e('"highlight"'))
        ->toContain(e('"Bold"'));
});

it('renders option overrides from the published config file', function () {
    config()->set('filament-ckeditor-field.editor.disabled_plugins', ['Highlight']);
    config()->set('filament-ckeditor-field.editor.disabled_toolbar_items', ['highlight']);

    expect(renderCKEditorFieldPayload())
        ->not->toContain(e('"Highlight"'))
        ->not->toContain(e('"highlight"'))
        ->toContain(e('"FontColor"'));
});

it('renders the upload adapter only when an upload url is configured', function () {
    expect(renderCKEditorFieldPayload())
        ->not->toContain(e('"simpleUpload"'))
        ->not->toContain(e('"SimpleUploadAdapter"'));

    expect(renderCKEditorFieldPayload(['uploadUrl' => '/upload']))
        ->toContain(e('"simpleUpload"'))
        ->toContain(e('"SimpleUploadAdapter"'));
});

it('keeps per-field options separate when several editors share a page', function () {
    $component = new class extends Component implements HasSchemas
    {
        use InteractsWithSchemas;

        public function form(Schema $schema): Schema
        {
            return $schema
                ->components([
                    CKEditor::make('plain'),
                    CKEditor::make('restricted')->disablePlugins(['Highlight']),
                ])
                ->statePath('data');
        }

        public function render(): View
        {
            return view('test::test-form');
        }
    };

    $html = Livewire::test($component::class)->assertSuccessful()->html();

    expect($html)
        ->toContain('ckeditor-data-plain')
        ->toContain('ckeditor-data-restricted');

    // Each field carries its own configuration in its own x-data payload.
    [, $plain, $restricted] = explode('ckeditorField({', $html);

    expect($plain)->toContain(e('"Highlight"'))
        ->and($restricted)->not->toContain(e('"Highlight"'));
});
