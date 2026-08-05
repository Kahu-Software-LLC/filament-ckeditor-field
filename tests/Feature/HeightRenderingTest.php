<?php

use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\View\View;
use Kahusoftware\FilamentCkeditorField\CKEditor;
use Livewire\Component;
use Livewire\Livewire;

function renderSizedCKEditorField(): string
{
    $component = new class extends Component implements HasSchemas
    {
        use InteractsWithSchemas;

        public ?string $content = null;

        public function form(Schema $schema): Schema
        {
            return $schema
                ->components([
                    CKEditor::make('content')
                        ->height('400px')
                        ->minHeight('10rem')
                        ->autofocus(),
                ])
                ->statePath('data');
        }

        public function render(): View
        {
            return view('test::test-form');
        }
    };

    return Livewire::test($component::class)->assertSuccessful()->html();
}

it('renders the height custom properties on the field wrapper', function () {
    expect(renderSizedCKEditorField())
        ->toContain('ckeditor-fixed-height')
        ->toContain('--ckeditor-height: 400px')
        ->toContain('--ckeditor-min-height: 10rem');
});

it('renders the autofocus flag into the component payload', function () {
    expect(renderSizedCKEditorField())->toContain('isAutofocused: true');
});

it('renders no height styling by default', function () {
    $component = new class extends Component implements HasSchemas
    {
        use InteractsWithSchemas;

        public ?string $content = null;

        public function form(Schema $schema): Schema
        {
            return $schema
                ->components([CKEditor::make('content')])
                ->statePath('data');
        }

        public function render(): View
        {
            return view('test::test-form');
        }
    };

    $html = Livewire::test($component::class)->assertSuccessful()->html();

    expect($html)
        ->not->toContain('ckeditor-fixed-height')
        ->not->toContain('--ckeditor-height')
        ->toContain('isAutofocused: false');
});
