<?php

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\View\View;
use Kahusoftware\FilamentCkeditorField\CKEditor;
use Livewire\Component;
use Livewire\Livewire;

function renderSizedCKEditorField(): string
{
    $component = new class extends Component implements HasForms
    {
        use InteractsWithForms;

        public ?string $content = null;

        public function form(Form $form): Form
        {
            return $form
                ->schema([
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
    $component = new class extends Component implements HasForms
    {
        use InteractsWithForms;

        public ?string $content = null;

        public function form(Form $form): Form
        {
            return $form
                ->schema([CKEditor::make('content')])
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
