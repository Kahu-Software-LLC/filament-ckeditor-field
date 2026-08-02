<?php

return [

    /**
     * Image upload enabled
     *
     * WARNING: Setting this to false will use CKEditor's default Base64 upload method which is HIGHLY INEFFICIENT.
     * https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html#base64-adapter
     */
    'upload_enabled' => true,

    /**
     * Image URL to upload to if one is not specified on the form field's ->uploadUrl() method
     */
    'upload_url' => null,

    /**
     * Editor configuration
     *
     * Everything the CKEditor instance is built from. Change it here to affect
     * every field in the application, in a service provider with
     * CKEditor::configureUsing(), or on a single field with ->editorOptions().
     *
     * Any key omitted from this file falls back to the package default, so the
     * whole block below may be trimmed to just the values you change. The
     * defaults shown are exactly what the package ships with.
     *
     * References:
     * - Editor options under `options`:
     *   https://ckeditor.com/docs/ckeditor5/latest/api/module_core_editor_editorconfig-EditorConfig.html
     * - Toolbar item names:
     *   https://ckeditor.com/docs/ckeditor5/latest/getting-started/setup/toolbar.html
     * - Feature documentation per plugin:
     *   https://ckeditor.com/docs/ckeditor5/latest/features/index.html
     *
     * Values are merged so that string-keyed arrays combine recursively while
     * list-shaped arrays are replaced outright. Overriding a list such as
     * `fontSize.options` therefore swaps it wholesale rather than appending to
     * it, and passing an empty array clears it.
     *
     * A string prefixed with `js:` is emitted as a bare JavaScript expression
     * rather than a quoted string, which is how regular expressions and other
     * non-JSON values reach the editor. Only use it for values you control:
     * the expression is written into the page as-is. Objects are deliberately
     * unsupported here because they do not survive `config:cache`.
     */
    'editor' => [

        /**
         * Plugins to activate, resolved by name against the JavaScript window
         * scope at runtime. Names that are not bundled are skipped, so trimming
         * this list is safe while adding to it requires a matching bundle.
         *
         * This list enumerates exactly what the package's bundled build ships.
         */
        'plugins' => [
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
            'ImageInsert',
            'ImageInsertViaUrl',
            'ImageUpload',
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
            'SimpleUploadAdapter',
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
        ],

        /**
         * Dropped automatically when the field has no upload URL.
         */
        'upload_only_plugins' => [
            'ImageInsert',
            'ImageUpload',
            'SimpleUploadAdapter',
        ],

        'upload_only_toolbar_items' => [
            'insertImage',
        ],

        /**
         * Removed from the resolved plugin list and toolbar. Use these to switch
         * features off without restating the lists above. Individual fields can
         * add to them with ->disablePlugins() / ->disableToolbarItems(), or opt
         * back in with ->enablePlugins().
         */
        'disabled_plugins' => [],

        'disabled_toolbar_items' => [],

        /**
         * Passed to ClassicEditor.create() after the merge described above.
         */
        'options' => [

            'toolbar' => [
                'items' => [
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
                    'insertImage',
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
                ],
                'shouldNotGroupWhenFull' => false,
            ],

            'fontFamily' => [
                'supportAllValues' => true,
            ],

            'fontSize' => [
                'options' => [10, 12, 14, 'default', 18, 20, 22],
                'supportAllValues' => true,
            ],

            'heading' => [
                'options' => [
                    ['model' => 'paragraph', 'title' => 'Paragraph', 'class' => 'ck-heading_paragraph'],
                    ['model' => 'heading1', 'view' => 'h1', 'title' => 'Heading 1', 'class' => 'ck-heading_heading1'],
                    ['model' => 'heading2', 'view' => 'h2', 'title' => 'Heading 2', 'class' => 'ck-heading_heading2'],
                    ['model' => 'heading3', 'view' => 'h3', 'title' => 'Heading 3', 'class' => 'ck-heading_heading3'],
                    ['model' => 'heading4', 'view' => 'h4', 'title' => 'Heading 4', 'class' => 'ck-heading_heading4'],
                    ['model' => 'heading5', 'view' => 'h5', 'title' => 'Heading 5', 'class' => 'ck-heading_heading5'],
                    ['model' => 'heading6', 'view' => 'h6', 'title' => 'Heading 6', 'class' => 'ck-heading_heading6'],
                ],
            ],

            /**
             * General HTML Support governs markup that no dedicated plugin owns.
             * A plugin that claims a style or element handles it first, so
             * disallowing something here has no effect while the plugin owning
             * it is still active. To drop a feature entirely, remove its plugin
             * from `plugins` as well as disallowing the markup here.
             */
            'htmlSupport' => [
                'allow' => [
                    [
                        'name' => 'js:/^.*$/',
                        'styles' => true,
                        'attributes' => true,
                        'classes' => true,
                    ],
                ],
                'disallow' => [
                    [
                        'styles' => [
                            'background-color' => true,
                            'color' => true,
                        ],
                    ],
                ],
            ],

            'image' => [
                'toolbar' => [
                    'toggleImageCaption',
                    'imageTextAlternative',
                    '|',
                    'imageStyle:inline',
                    'imageStyle:wrapText',
                    'imageStyle:breakText',
                    '|',
                    'resizeImage',
                ],
            ],

            'link' => [
                'addTargetToExternalLinks' => true,
                'defaultProtocol' => 'https://',
                'decorators' => [
                    'toggleDownloadable' => [
                        'mode' => 'manual',
                        'label' => 'Downloadable',
                        'attributes' => [
                            'download' => 'file',
                        ],
                    ],
                ],
            ],

            'list' => [
                'properties' => [
                    'styles' => true,
                    'startIndex' => true,
                    'reversed' => true,
                ],
            ],

            'menuBar' => [
                'isVisible' => true,
            ],

            'style' => [
                'definitions' => [
                    ['name' => 'Article category', 'element' => 'h3', 'classes' => ['category']],
                    ['name' => 'Title', 'element' => 'h2', 'classes' => ['document-title']],
                    ['name' => 'Subtitle', 'element' => 'h3', 'classes' => ['document-subtitle']],
                    ['name' => 'Info box', 'element' => 'p', 'classes' => ['info-box']],
                    ['name' => 'Side quote', 'element' => 'blockquote', 'classes' => ['side-quote']],
                    ['name' => 'Marker', 'element' => 'span', 'classes' => ['marker']],
                    ['name' => 'Spoiler', 'element' => 'span', 'classes' => ['spoiler']],
                    ['name' => 'Code (dark)', 'element' => 'pre', 'classes' => ['fancy-code', 'fancy-code-dark']],
                    ['name' => 'Code (bright)', 'element' => 'pre', 'classes' => ['fancy-code', 'fancy-code-bright']],
                ],
            ],

            'table' => [
                'contentToolbar' => [
                    'tableColumn',
                    'tableRow',
                    'mergeTableCells',
                    'tableProperties',
                    'tableCellProperties',
                ],
            ],

        ],

    ],

];
