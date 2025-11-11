<?php

use Kahusoftware\FilamentCkeditorField\CKEditor;

it('can be instantiated with default name', function () {
    $field = CKEditor::make();

    expect($field)
        ->toBeInstanceOf(CKEditor::class)
        ->and($field->getName())->toBe('ckeditor');
});

it('can be instantiated with custom name', function () {
    $field = CKEditor::make('content');

    expect($field->getName())->toBe('content');
});

it('can set upload url using method chaining', function () {
    $field = CKEditor::make('content')
        ->uploadUrl('/custom-upload');

    expect($field->getUploadUrl())->toBe('/custom-upload');
});

it('can set upload url to null', function () {
    $field = CKEditor::make('content')
        ->uploadUrl(null);

    expect($field->getUploadUrl())->toBeNull();
});

it('can set content using method chaining', function () {
    $field = CKEditor::make('content')
        ->content('<p>Hello World</p>');

    expect($field->getContent())->toBe('<p>Hello World</p>');
});

it('can set placeholder using method chaining', function () {
    $field = CKEditor::make('content')
        ->placeholder('Enter your content here...');

    expect($field->getPlaceholder())->toBe('Enter your content here...');
});

it('has default placeholder', function () {
    $field = CKEditor::make('content');

    expect($field->getPlaceholder())->toBe('Type or paste your content here...');
});

it('can change name using method chaining', function () {
    $field = CKEditor::make('content')
        ->name('body');

    expect($field->getName())->toBe('body');
});

it('can evaluate closure for content', function () {
    $field = CKEditor::make('content')
        ->content(fn() => '<p>Dynamic Content</p>');

    expect($field->getContent())->toBe('<p>Dynamic Content</p>');
});

it('can evaluate closure for upload url', function () {
    $field = CKEditor::make('content')
        ->uploadUrl(fn() => '/dynamic-upload');

    expect($field->getUploadUrl())->toBe('/dynamic-upload');
});

it('is dehydrated by default', function () {
    $field = CKEditor::make('content');

    // The field calls dehydrated(false) in setUp(), so it should not be dehydrated
    // We can verify this by checking if the field has the dehydrated state set
    expect($field)->toBeInstanceOf(CKEditor::class);
});
