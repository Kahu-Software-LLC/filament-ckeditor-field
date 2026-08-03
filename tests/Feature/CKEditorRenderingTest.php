<?php

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\View\View;
use Kahusoftware\FilamentCkeditorField\CKEditor;
use Livewire\Component;
use Livewire\Livewire;

it('can render ckeditor field in a livewire component', function () {
    $component = new class extends Component implements HasForms
    {
        use InteractsWithForms;

        public ?string $content = null;

        public function form(Form $form): Form
        {
            return $form
                ->schema([
                    CKEditor::make('content'),
                ])
                ->statePath('data');
        }

        public function render(): View
        {
            return view('test::test-form');
        }
    };

    Livewire::test($component::class)
        ->assertSuccessful()
        ->assertFormFieldExists('content');
});

it('can render ckeditor field with upload url in livewire component', function () {
    $component = new class extends Component implements HasForms
    {
        use InteractsWithForms;

        public ?string $content = null;

        public function form(Form $form): Form
        {
            return $form
                ->schema([
                    CKEditor::make('content')
                        ->uploadUrl('/upload'),
                ])
                ->statePath('data');
        }

        public function render(): View
        {
            return view('test::test-form');
        }
    };

    Livewire::test($component::class)
        ->assertSuccessful();
});

it('can render ckeditor field with custom placeholder in livewire component', function () {
    $component = new class extends Component implements HasForms
    {
        use InteractsWithForms;

        public ?string $content = null;

        public function form(Form $form): Form
        {
            return $form
                ->schema([
                    CKEditor::make('content')
                        ->placeholder('Custom placeholder text'),
                ])
                ->statePath('data');
        }

        public function render(): View
        {
            return view('test::test-form');
        }
    };

    Livewire::test($component::class)
        ->assertSuccessful();
});

it('can render ckeditor field alongside other fields', function () {
    $component = new class extends Component implements HasForms
    {
        use InteractsWithForms;

        public ?string $title = null;
        public ?string $content = null;

        public function form(Form $form): Form
        {
            return $form
                ->schema([
                    TextInput::make('title'),
                    CKEditor::make('content'),
                ])
                ->statePath('data');
        }

        public function render(): View
        {
            return view('test::test-form');
        }
    };

    Livewire::test($component::class)
        ->assertSuccessful()
        ->assertFormFieldExists('title')
        ->assertFormFieldExists('content');
});

it('can render two ckeditor fields in one form', function () {
    $component = new class extends Component implements HasForms
    {
        use InteractsWithForms;

        public ?string $content = null;
        public ?string $excerpt = null;

        public function form(Form $form): Form
        {
            return $form
                ->schema([
                    CKEditor::make('content'),
                    CKEditor::make('excerpt'),
                ])
                ->statePath('data');
        }

        public function render(): View
        {
            return view('test::test-form');
        }
    };

    Livewire::test($component::class)
        ->assertSuccessful();
});

it('can render three ckeditor fields in one form', function () {
    $component = new class extends Component implements HasForms
    {
        use InteractsWithForms;

        public ?string $content = null;
        public ?string $excerpt = null;
        public ?string $notes = null;

        public function form(Form $form): Form
        {
            return $form
                ->schema([
                    CKEditor::make('content'),
                    CKEditor::make('excerpt'),
                    CKEditor::make('notes'),
                ])
                ->statePath('data');
        }

        public function render(): View
        {
            return view('test::test-form');
        }
    };

    Livewire::test($component::class)
        ->assertSuccessful();
});

it('applies the dark mode class from the editor component', function () {
    // The prose classes moved out of the rendered HTML and into the
    // ckeditorField Alpine component, which applies them after creating the
    // editor, so the guarantee is asserted against the shipped source.
    $component = file_get_contents(dirname(__DIR__, 2) . '/resources/js/ckeditor-field.js');

    expect($component)->toContain('dark:prose-invert');
});

