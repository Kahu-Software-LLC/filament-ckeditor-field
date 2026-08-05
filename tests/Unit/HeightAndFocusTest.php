<?php

use Kahusoftware\FilamentCkeditorField\CKEditor;

it('has no height constraints by default', function () {
    $field = CKEditor::make('content');

    expect($field->getHeight())->toBeNull()
        ->and($field->getMinHeight())->toBeNull();
});

it('sets a fixed height', function () {
    expect(CKEditor::make('content')->height('400px')->getHeight())->toBe('400px');
});

it('sets a minimum height', function () {
    expect(CKEditor::make('content')->minHeight('12rem')->getMinHeight())->toBe('12rem');
});

it('evaluates closures for height values', function () {
    $field = CKEditor::make('content')
        ->height(fn (): string => '360px')
        ->minHeight(fn (): string => '10rem');

    expect($field->getHeight())->toBe('360px')
        ->and($field->getMinHeight())->toBe('10rem');
});

it('clears a height with null', function () {
    expect(CKEditor::make('content')->height('400px')->height(null)->getHeight())->toBeNull();
});

it('supports autofocus from the base field', function () {
    expect(CKEditor::make('content')->autofocus()->isAutofocused())->toBeTrue()
        ->and(CKEditor::make('content')->isAutofocused())->toBeFalse();
});

it('focuses the editor from the component when autofocused', function () {
    // The focus call lives in the ckeditorField Alpine component, so the
    // guarantee is asserted against the shipped source.
    $component = file_get_contents(dirname(__DIR__, 2) . '/resources/js/ckeditor-field.js');

    expect($component)->toContain('isAutofocused')
        ->toContain('editor.editing.view.focus()');
});
