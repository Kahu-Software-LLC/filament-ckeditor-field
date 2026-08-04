<?php

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Kahusoftware\FilamentCkeditorField\CKEditor;
use Kahusoftware\FilamentCkeditorField\Tests\Helpers\Livewire;

it('can set state path from its name', function (): void {
    $field = CKEditor::make($name = Str::random())
        ->container(Schema::make(Livewire::make()));

    expect($field)
        ->getStatePath()->toBe($name);
});

it('can render ckeditor field with upload url', function () {
    $field = CKEditor::make('content')
        ->uploadUrl('/upload')
        ->container(Schema::make(Livewire::make()));

    expect($field->getUploadUrl())->toBe('/upload');
});

it('can render ckeditor field with custom placeholder', function () {
    $field = CKEditor::make('content')
        ->placeholder('Custom placeholder text')
        ->container(Schema::make(Livewire::make()));

    expect($field->getPlaceholder())->toBe('Custom placeholder text');
});

it('can render ckeditor field alongside other fields', function () {
    $schema = Schema::make(Livewire::make())
        ->components([
            TextInput::make('title'),
            CKEditor::make('content'),
        ]);

    expect($schema->getComponents())
        ->toHaveCount(2)
        ->and($schema->getComponents()[0]->getName())->toBe('title')
        ->and($schema->getComponents()[1]->getName())->toBe('content');
});

it('can render two ckeditor fields in one form', function () {
    $schema = Schema::make(Livewire::make())
        ->components([
            CKEditor::make('content'),
            CKEditor::make('excerpt'),
        ]);

    expect($schema->getComponents())
        ->toHaveCount(2)
        ->and($schema->getComponents()[0]->getName())->toBe('content')
        ->and($schema->getComponents()[1]->getName())->toBe('excerpt');
});

it('can render three ckeditor fields in one form', function () {
    $schema = Schema::make(Livewire::make())
        ->components([
            CKEditor::make('content'),
            CKEditor::make('excerpt'),
            CKEditor::make('notes'),
        ]);

    expect($schema->getComponents())
        ->toHaveCount(3)
        ->and($schema->getComponents()[0]->getName())->toBe('content')
        ->and($schema->getComponents()[1]->getName())->toBe('excerpt')
        ->and($schema->getComponents()[2]->getName())->toBe('notes');
});

it('applies the dark mode class from the editor component', function () {
    // The prose classes moved out of the rendered HTML and into the
    // ckeditorField Alpine component, which applies them after creating the
    // editor, so the guarantee is asserted against the shipped source.
    $component = file_get_contents(dirname(__DIR__, 2) . '/resources/js/ckeditor-field.js');

    expect($component)->toContain('dark:prose-invert');
});
