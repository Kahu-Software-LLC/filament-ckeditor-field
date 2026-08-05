<?php

use Kahusoftware\FilamentCkeditorField\CKEditor;

// CKEditor logs a `toolbarview-item-unavailable` warning for every toolbar
// item whose plugin is not loaded, once per editor creation. These tests pin
// the resolution-side guarantee: items whose plugin is absent from the
// resolved plugin list never reach the browser.

it('drops toolbar items whose plugin is missing from a published plugin list', function () {
    config()->set('filament-ckeditor-field', [
        'upload_enabled' => true,
        'upload_url' => null,
        'editor' => [
            'plugins' => ['Essentials', 'Paragraph', 'Bold', 'Heading', 'Undo'],
        ],
    ]);

    $items = CKEditor::make('content')->getEditorOptions()['toolbar']['items'];

    expect($items)
        ->toContain('bold')
        ->toContain('heading')
        ->toContain('undo')
        ->not->toContain('fontColor')
        ->not->toContain('fontBackgroundColor')
        ->not->toContain('highlight')
        ->not->toContain('insertTable')
        ->not->toContain('sourceEditing');
});

it('drops the toolbar items of a disabled plugin automatically', function () {
    $items = CKEditor::make('content')
        ->disablePlugins(['FontColor', 'Highlight'])
        ->getEditorOptions()['toolbar']['items'];

    expect($items)
        ->not->toContain('fontColor')
        ->not->toContain('highlight')
        ->toContain('fontBackgroundColor');
});

it('drops every toolbar item a multi-item plugin provides', function () {
    $items = CKEditor::make('content')
        ->disablePlugins(['List', 'Undo', 'Indent'])
        ->getEditorOptions()['toolbar']['items'];

    expect($items)
        ->not->toContain('bulletedList')
        ->not->toContain('numberedList')
        ->not->toContain('undo')
        ->not->toContain('redo')
        ->not->toContain('outdent')
        ->not->toContain('indent');
});

it('keeps toolbar items that no bundled plugin claims', function () {
    $items = CKEditor::make('content')
        ->editorOptions(['toolbar' => ['items' => ['bold', 'myCustomButton']]])
        ->getEditorOptions()['toolbar']['items'];

    expect($items)->toContain('myCustomButton')->toContain('bold');
});

it('restores the toolbar item of a re-enabled plugin', function () {
    config()->set('filament-ckeditor-field.editor.disabled_plugins', ['Highlight']);

    $items = CKEditor::make('content')
        ->enablePlugins(['Highlight'])
        ->getEditorOptions()['toolbar']['items'];

    expect($items)->toContain('highlight');
});

it('tidies separators orphaned by dropped toolbar items', function () {
    $items = CKEditor::make('content')
        ->disablePlugins(['Heading', 'Style', 'FontSize', 'FontFamily', 'FontColor', 'FontBackgroundColor'])
        ->getEditorOptions()['toolbar']['items'];

    expect($items[0])->not->toBe('|')
        ->and(end($items))->not->toBe('|');

    foreach (array_keys($items, '|', true) as $index) {
        expect($items[$index + 1] ?? null)->not->toBe('|');
    }
});

it('still removes upload toolbar items through plugin filtering alone', function () {
    // With no upload URL the upload-only plugins are stripped; insertImage
    // must disappear even without the explicit upload_only_toolbar_items
    // pairing.
    config()->set('filament-ckeditor-field', [
        'upload_enabled' => true,
        'upload_url' => null,
        'editor' => [
            'upload_only_toolbar_items' => [],
        ],
    ]);

    $items = CKEditor::make('content')->getEditorOptions()['toolbar']['items'];

    expect($items)->not->toContain('insertImage');
});
