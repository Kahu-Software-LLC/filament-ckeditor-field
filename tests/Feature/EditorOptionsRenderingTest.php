<?php

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\View\View;
use Kahusoftware\FilamentCkeditorField\CKEditor;
use Livewire\Component;
use Livewire\Livewire;

/**
 * Render a single CKEditor field configured from plain data and return the
 * resulting HTML, so tests can assert on the editor configuration that actually
 * reaches the browser.
 *
 * Livewire re-instantiates the component class, so the configuration travels as
 * mount data rather than as constructor arguments or a closure.
 *
 * @param  array<string, mixed>  $config
 */
function renderCKEditorField(array $config = []): string
{
    $component = new class extends Component implements HasForms
    {
        use InteractsWithForms;

        public ?string $content = null;

        /** @var array<string, mixed> */
        public array $fieldConfig = [];

        /** @param array<string, mixed> $fieldConfig */
        public function mount(array $fieldConfig = []): void
        {
            $this->fieldConfig = $fieldConfig;
        }

        public function form(Form $form): Form
        {
            $field = CKEditor::make('content');

            foreach ($this->fieldConfig as $method => $argument) {
                $field = $field->{$method}($argument);
            }

            return $form->schema([$field])->statePath('data');
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

it('renders the resolved editor options into the create call', function () {
    $html = renderCKEditorField();

    expect($html)
        ->toContain('const editorConfig = {')
        ->toContain('"plugins":[')
        ->toContain('AccessibilityHelp')
        ->toContain('"toolbar":{')
        ->toContain('"htmlSupport":{')
        ->toContain('.create(textarea, editorConfig)');
});

it('resolves plugin names from the window scope at runtime', function () {
    // Plugin constructors cannot be serialised, so the view receives names and
    // maps them against the window scope, skipping any that are not bundled.
    expect(renderCKEditorField())
        ->toContain('.map((name) => window[name])')
        ->toContain('.filter(Boolean)');
});

it('renders the html support regular expression as a bare expression', function () {
    expect(renderCKEditorField())->toContain('"name":/^.*$/');
});

it('renders the default configuration with the colour features intact', function () {
    expect(renderCKEditorField())
        ->toContain('"FontColor"')
        ->toContain('"FontBackgroundColor"')
        ->toContain('"Highlight"')
        ->toContain('"fontColor"')
        ->toContain('"fontBackgroundColor"')
        ->toContain('"highlight"');
});

it('omits disabled plugins and toolbar items from the rendered config', function () {
    $html = renderCKEditorField([
        'disablePlugins' => ['FontColor', 'FontBackgroundColor', 'Highlight'],
        'disableToolbarItems' => ['fontColor', 'fontBackgroundColor', 'highlight'],
    ]);

    expect($html)
        ->not->toContain('"FontColor"')
        ->not->toContain('"FontBackgroundColor"')
        ->not->toContain('"Highlight"')
        ->not->toContain('"fontColor"')
        ->not->toContain('"fontBackgroundColor"')
        ->not->toContain('"highlight"')
        ->toContain('"Bold"');
});

it('renders option overrides from the published config file', function () {
    config()->set('filament-ckeditor-field.editor.disabled_plugins', ['Highlight']);
    config()->set('filament-ckeditor-field.editor.disabled_toolbar_items', ['highlight']);

    expect(renderCKEditorField())
        ->not->toContain('"Highlight"')
        ->not->toContain('"highlight"')
        ->toContain('"FontColor"');
});

it('renders the upload adapter only when an upload url is configured', function () {
    expect(renderCKEditorField())
        ->not->toContain('"simpleUpload"')
        ->not->toContain('"SimpleUploadAdapter"');

    expect(renderCKEditorField(['uploadUrl' => '/upload']))
        ->toContain('"simpleUpload"')
        ->toContain('"SimpleUploadAdapter"');
});

it('keeps per-field options separate when several editors share a page', function () {
    $component = new class extends Component implements HasForms
    {
        use InteractsWithForms;

        public function form(Form $form): Form
        {
            return $form
                ->schema([
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

    // createCKEditor is shared by every field on the page, so each editor's
    // configuration has to be stored against its own instance key.
    expect($html)
        ->toContain('window.ckeditorInstances["ckeditor-data-plain"].config =')
        ->toContain('window.ckeditorInstances["ckeditor-data-restricted"].config =')
        ->toContain('window.ckeditorInstances[instanceKey].config');

    [, $plain, $restricted] = explode('.config = ', $html);

    expect($plain)->toContain('"Highlight"')
        ->and($restricted)->not->toContain('"Highlight"');
});
