<?php

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\View\View;
use Kahusoftware\FilamentCkeditorField\CKEditor;
use Livewire\Component;
use Livewire\Livewire;

it('can render disabled ckeditor field', function () {
    $component = new class extends Component implements HasForms
    {
        use InteractsWithForms;

        public ?string $content = '<p>Disabled content</p>';

        public function form(Form $form): Form
        {
            return $form
                ->schema([
                    CKEditor::make('content')
                        ->disabled(),
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

it('can render required ckeditor field', function () {
    $component = new class extends Component implements HasForms
    {
        use InteractsWithForms;

        public ?string $content = null;

        public function form(Form $form): Form
        {
            return $form
                ->schema([
                    CKEditor::make('content')
                        ->required(),
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

it('can render conditionally visible ckeditor field', function () {
    $component = new class extends Component implements HasForms
    {
        use InteractsWithForms;

        public ?string $content = null;
        public bool $showEditor = false;

        public function form(Form $form): Form
        {
            return $form
                ->schema([
                    CKEditor::make('content')
                        ->visible(fn () => $this->showEditor),
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
        ->assertFormFieldIsHidden('content')
        ->set('showEditor', true)
        ->assertFormFieldIsVisible('content');
});

it('can fill form with ckeditor content', function () {
    $component = new class extends Component implements HasForms
    {
        use InteractsWithForms;

        public array $data = [];

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
        ->fillForm([
            'content' => '<p>Test content</p>',
        ])
        ->assertFormSet([
            'content' => '<p>Test content</p>',
        ]);
});

it('can assert form field is filled with content', function () {
    $component = new class extends Component implements HasForms
    {
        use InteractsWithForms;

        public array $data = ['content' => '<p>Initial content</p>'];

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
        ->assertFormSet([
            'content' => '<p>Initial content</p>',
        ]);
});

it('can assert form field is empty', function () {
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
        ->assertFormSet([
            'content' => null,
        ]);
});

it('validates required ckeditor field', function () {
    $component = new class extends Component implements HasForms
    {
        use InteractsWithForms;

        public array $data = [];

        public function form(Form $form): Form
        {
            return $form
                ->schema([
                    CKEditor::make('content')
                        ->required(),
                ])
                ->statePath('data');
        }

        public function submit(): void
        {
            $this->form->getState();
        }

        public function render(): View
        {
            return view('test::test-form');
        }
    };

    Livewire::test($component::class)
        ->assertSuccessful()
        ->fillForm([
            'content' => '',
        ])
        ->call('submit')
        ->assertHasFormErrors(['content' => ['required']]);
});

it('passes validation when required ckeditor field has content', function () {
    $component = new class extends Component implements HasForms
    {
        use InteractsWithForms;

        public array $data = [];

        public function form(Form $form): Form
        {
            return $form
                ->schema([
                    CKEditor::make('content')
                        ->required(),
                ])
                ->statePath('data');
        }

        public function submit(): void
        {
            $this->form->getState();
        }

        public function render(): View
        {
            return view('test::test-form');
        }
    };

    Livewire::test($component::class)
        ->assertSuccessful()
        ->fillForm([
            'content' => '<p>Valid content</p>',
        ])
        ->call('submit')
        ->assertHasNoFormErrors();
});

it('registers only the fields declared in the schema', function () {
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

    $test = Livewire::test($component::class)
        ->assertSuccessful()
        ->assertFormFieldExists('content');

    // Filament has no assertFormFieldDoesNotExist, so inspect the schema.
    expect(array_keys($test->instance()->getForm('form')->getFlatFields()))
        ->toBe(['content']);
});
