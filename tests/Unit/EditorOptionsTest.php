<?php

use Kahusoftware\FilamentCkeditorField\CKEditor;

/*
|--------------------------------------------------------------------------
| Parity with the previously hard-coded editor configuration
|--------------------------------------------------------------------------
|
| The editor configuration used to live inline in the Blade view. These tests
| pin the resolved defaults to the exact values that view produced, so the
| extraction into config cannot silently change any consumer's editor.
|
*/

it('resolves the full default plugin list in order', function () {
    $plugins = CKEditor::make('content')->getEditorOptions()['plugins'];

    expect($plugins)->toBe([
        'AccessibilityHelp',
        'Alignment',
        'Autoformat',
        'AutoImage',
        'AutoLink',
        'Autosave',
        'BlockQuote',
        'Bold',
        'Code',
        'CodeBlock',
        'Essentials',
        'FindAndReplace',
        'FontBackgroundColor',
        'FontColor',
        'FontFamily',
        'FontSize',
        'GeneralHtmlSupport',
        'Heading',
        'Highlight',
        'HorizontalLine',
        'HtmlComment',
        'HtmlEmbed',
        'ImageBlock',
        'ImageCaption',
        'ImageInline',
        'ImageInsertViaUrl',
        'ImageResize',
        'ImageStyle',
        'ImageTextAlternative',
        'ImageToolbar',
        'Indent',
        'IndentBlock',
        'Italic',
        'Link',
        'LinkImage',
        'List',
        'ListProperties',
        'MediaEmbed',
        'PageBreak',
        'Paragraph',
        'PasteFromOffice',
        'RemoveFormat',
        'SelectAll',
        'ShowBlocks',
        'SourceEditing',
        'SpecialCharacters',
        'SpecialCharactersArrows',
        'SpecialCharactersCurrency',
        'SpecialCharactersEssentials',
        'SpecialCharactersLatin',
        'SpecialCharactersMathematical',
        'SpecialCharactersText',
        'Strikethrough',
        'Style',
        'Subscript',
        'Superscript',
        'Table',
        'TableCaption',
        'TableCellProperties',
        'TableColumnResize',
        'TableProperties',
        'TableToolbar',
        'TextTransformation',
        'TodoList',
        'Underline',
        'Undo',
    ]);
});

it('resolves the default toolbar items in order', function () {
    $options = CKEditor::make('content')->getEditorOptions();

    expect($options['toolbar']['items'])->toBe([
        'undo',
        'redo',
        '|',
        'sourceEditing',
        'showBlocks',
        '|',
        'heading',
        'style',
        '|',
        'fontSize',
        'fontFamily',
        'fontColor',
        'fontBackgroundColor',
        '|',
        'bold',
        'italic',
        'underline',
        '|',
        'link',
        'insertTable',
        'highlight',
        'blockQuote',
        'codeBlock',
        '|',
        'alignment',
        '|',
        'bulletedList',
        'numberedList',
        'todoList',
        'outdent',
        'indent',
    ])->and($options['toolbar']['shouldNotGroupWhenFull'])->toBeFalse();
});

it('keeps the default html support rules unchanged', function () {
    $htmlSupport = CKEditor::make('content')->getEditorOptions()['htmlSupport'];

    expect($htmlSupport['allow'])->toBe([
        [
            'name' => 'js:/^.*$/',
            'styles' => true,
            'attributes' => true,
            'classes' => true,
        ],
    ])->and($htmlSupport['disallow'])->toBe([
        [
            'styles' => [
                'background-color' => true,
                'color' => true,
            ],
        ],
    ]);
});

it('keeps the remaining default option groups unchanged', function () {
    $options = CKEditor::make('content')->getEditorOptions();

    expect($options['fontSize']['options'])->toBe([10, 12, 14, 'default', 18, 20, 22])
        ->and($options['fontSize']['supportAllValues'])->toBeTrue()
        ->and($options['fontFamily']['supportAllValues'])->toBeTrue()
        ->and($options['heading']['options'])->toHaveCount(7)
        ->and($options['style']['definitions'])->toHaveCount(9)
        ->and($options['menuBar']['isVisible'])->toBeTrue()
        ->and($options['list']['properties'])->toBe([
            'styles' => true,
            'startIndex' => true,
            'reversed' => true,
        ])
        ->and($options['link']['defaultProtocol'])->toBe('https://')
        ->and($options['link']['addTargetToExternalLinks'])->toBeTrue()
        ->and($options['table']['contentToolbar'])->toBe([
            'tableColumn',
            'tableRow',
            'mergeTableCells',
            'tableProperties',
            'tableCellProperties',
        ]);
});

it('ships with no plugins or toolbar items disabled so behaviour is unchanged by default', function () {
    $options = CKEditor::make('content')->getEditorOptions();

    expect(config('filament-ckeditor-field.editor.disabled_plugins'))->toBe([])
        ->and(config('filament-ckeditor-field.editor.disabled_toolbar_items'))->toBe([])
        ->and($options['plugins'])->toContain('FontColor', 'FontBackgroundColor', 'Highlight')
        ->and($options['toolbar']['items'])->toContain('fontColor', 'fontBackgroundColor', 'highlight');
});

it('keeps the field dehydrated so form state still reaches the model', function () {
    // setUp() used to call ->dehydrated(false), but make() never invoked
    // configure(), so the call never ran. Now that configure() does run, that
    // line would stop the editor's content from ever being saved.
    expect(CKEditor::make('content')->isDehydrated())->toBeTrue();
});

it('passes the field placeholder through to the editor options', function () {
    $options = CKEditor::make('content')
        ->placeholder('Write something')
        ->getEditorOptions();

    expect($options['placeholder'])->toBe('Write something');
});

/*
|--------------------------------------------------------------------------
| Upload-dependent plugins and toolbar items
|--------------------------------------------------------------------------
*/

it('omits upload-only plugins and toolbar items when no upload url is set', function () {
    $options = CKEditor::make('content')->getEditorOptions();

    expect($options['plugins'])
        ->not->toContain('ImageInsert')
        ->not->toContain('ImageUpload')
        ->not->toContain('SimpleUploadAdapter')
        ->toContain('ImageInsertViaUrl')
        ->and($options['toolbar']['items'])->not->toContain('insertImage')
        ->and($options)->not->toHaveKey('simpleUpload');
});

it('includes upload-only plugins and toolbar items when an upload url is set', function () {
    $options = CKEditor::make('content')
        ->uploadUrl('/upload')
        ->getEditorOptions();

    expect($options['plugins'])
        ->toContain('ImageInsert')
        ->toContain('ImageInsertViaUrl')
        ->toContain('ImageUpload')
        ->toContain('SimpleUploadAdapter')
        ->and($options['toolbar']['items'])->toContain('insertImage')
        ->and($options['simpleUpload']['uploadUrl'])->toBe('/upload')
        ->and($options['simpleUpload']['withCredentials'])->toBeTrue()
        ->and($options['simpleUpload']['headers'])->toHaveKey('X-CSRF-TOKEN');
});

/*
|--------------------------------------------------------------------------
| Merge semantics
|--------------------------------------------------------------------------
*/

it('merges string-keyed arrays recursively', function () {
    $merged = CKEditor::mergeOptions(
        ['link' => ['defaultProtocol' => 'https://', 'addTargetToExternalLinks' => true]],
        ['link' => ['defaultProtocol' => 'http://']],
    );

    expect($merged['link'])->toBe([
        'defaultProtocol' => 'http://',
        'addTargetToExternalLinks' => true,
    ]);
});

it('replaces list-shaped arrays wholesale instead of merging by index', function () {
    $merged = CKEditor::mergeOptions(
        ['fontSize' => ['options' => [10, 12, 14, 'default', 18, 20, 22]]],
        ['fontSize' => ['options' => [12, 16]]],
    );

    expect($merged['fontSize']['options'])->toBe([12, 16]);
});

it('clears a list when overridden with an empty array', function () {
    $merged = CKEditor::mergeOptions(
        ['table' => ['contentToolbar' => ['tableColumn', 'tableRow']]],
        ['table' => ['contentToolbar' => []]],
    );

    expect($merged['table']['contentToolbar'])->toBe([]);
});

it('adds keys that are absent from the base', function () {
    $merged = CKEditor::mergeOptions(['a' => 1], ['b' => ['c' => 2]]);

    expect($merged)->toBe(['a' => 1, 'b' => ['c' => 2]]);
});

/*
|--------------------------------------------------------------------------
| Override levels: config file, configureUsing, per field
|--------------------------------------------------------------------------
*/

it('applies per-field option overrides', function () {
    $options = CKEditor::make('content')
        ->editorOptions(['menuBar' => ['isVisible' => false]])
        ->getEditorOptions();

    expect($options['menuBar']['isVisible'])->toBeFalse()
        ->and($options['plugins'])->toContain('Bold');
});

it('applies option overrides from the published config file', function () {
    config()->set('filament-ckeditor-field.editor.options.menuBar.isVisible', false);

    expect(CKEditor::make('content')->getEditorOptions()['menuBar']['isVisible'])->toBeFalse();
});

it('applies disabled plugins from the config file', function () {
    config()->set('filament-ckeditor-field.editor.disabled_plugins', ['FontColor', 'Highlight']);

    $plugins = CKEditor::make('content')->getEditorOptions()['plugins'];

    expect($plugins)
        ->not->toContain('FontColor')
        ->not->toContain('Highlight')
        ->toContain('FontBackgroundColor');
});

it('applies disabled toolbar items from the config file', function () {
    config()->set('filament-ckeditor-field.editor.disabled_toolbar_items', ['fontColor', 'highlight']);

    $items = CKEditor::make('content')->getEditorOptions()['toolbar']['items'];

    expect($items)
        ->not->toContain('fontColor')
        ->not->toContain('highlight')
        ->toContain('fontBackgroundColor');
});

it('lets a later override win over an earlier one', function () {
    $options = CKEditor::make('content')
        ->editorOptions(['menuBar' => ['isVisible' => false]])
        ->editorOptions(['menuBar' => ['isVisible' => true]])
        ->getEditorOptions();

    expect($options['menuBar']['isVisible'])->toBeTrue();
});

it('lets a per-field override win over a global configureUsing override', function () {
    $options = CKEditor::configureUsing(
        fn (CKEditor $field) => $field->editorOptions([
            'menuBar' => ['isVisible' => false],
            'fontSize' => ['options' => [12]],
        ]),
        during: fn () => CKEditor::make('content')
            ->editorOptions(['menuBar' => ['isVisible' => true]])
            ->getEditorOptions(),
    );

    expect($options['menuBar']['isVisible'])->toBeTrue()
        ->and($options['fontSize']['options'])->toBe([12]);
});

it('applies a global configureUsing override when the field does not override it', function () {
    $options = CKEditor::configureUsing(
        fn (CKEditor $field) => $field->disablePlugins(['Highlight']),
        during: fn () => CKEditor::make('content')->getEditorOptions(),
    );

    expect($options['plugins'])->not->toContain('Highlight');
});

it('disables plugins per field', function () {
    $plugins = CKEditor::make('content')
        ->disablePlugins(['FontColor', 'FontBackgroundColor', 'Highlight'])
        ->getEditorOptions()['plugins'];

    expect($plugins)
        ->not->toContain('FontColor')
        ->not->toContain('FontBackgroundColor')
        ->not->toContain('Highlight')
        ->toContain('Bold');
});

it('disables toolbar items per field', function () {
    $items = CKEditor::make('content')
        ->disableToolbarItems(['fontColor', 'fontBackgroundColor', 'highlight'])
        ->getEditorOptions()['toolbar']['items'];

    expect($items)
        ->not->toContain('fontColor')
        ->not->toContain('fontBackgroundColor')
        ->not->toContain('highlight')
        ->toContain('bold');
});

it('re-enables a plugin that the config file disabled', function () {
    config()->set('filament-ckeditor-field.editor.disabled_plugins', ['FontColor', 'Highlight']);

    $plugins = CKEditor::make('content')
        ->enablePlugins(['FontColor'])
        ->getEditorOptions()['plugins'];

    expect($plugins)
        ->toContain('FontColor')
        ->not->toContain('Highlight');
});

it('evaluates closures passed to the override methods', function () {
    $options = CKEditor::make('content')
        ->editorOptions(fn () => ['menuBar' => ['isVisible' => false]])
        ->disablePlugins(fn () => ['Highlight'])
        ->disableToolbarItems(fn () => ['highlight'])
        ->getEditorOptions();

    expect($options['menuBar']['isVisible'])->toBeFalse()
        ->and($options['plugins'])->not->toContain('Highlight')
        ->and($options['toolbar']['items'])->not->toContain('highlight');
});

it('ignores unknown plugin names in the disable list', function () {
    $plugins = CKEditor::make('content')
        ->disablePlugins(['NotARealPlugin'])
        ->getEditorOptions()['plugins'];

    expect($plugins)->toBe(CKEditor::make('content')->getEditorOptions()['plugins']);
});

/*
|--------------------------------------------------------------------------
| Toolbar separator hygiene
|--------------------------------------------------------------------------
|
| Removing named items can leave orphaned separators behind, which CKEditor
| renders as visible dividers with nothing between them.
|
*/

it('collapses separators left orphaned by removing toolbar items', function () {
    $items = CKEditor::make('content')
        ->editorOptions(['toolbar' => ['items' => ['bold', '|', 'fontColor', '|', 'italic']]])
        ->disableToolbarItems(['fontColor'])
        ->getEditorOptions()['toolbar']['items'];

    expect($items)->toBe(['bold', '|', 'italic']);
});

it('trims separators from the start and end of the toolbar', function () {
    $items = CKEditor::make('content')
        ->editorOptions(['toolbar' => ['items' => ['|', 'bold', 'italic', '|']]])
        ->getEditorOptions()['toolbar']['items'];

    expect($items)->toBe(['bold', 'italic']);
});

it('keeps a toolbar that is nothing but separators from rendering dividers', function () {
    $items = CKEditor::make('content')
        ->editorOptions(['toolbar' => ['items' => ['|', 'bold', '|']]])
        ->disableToolbarItems(['bold'])
        ->getEditorOptions()['toolbar']['items'];

    expect($items)->toBe([]);
});
