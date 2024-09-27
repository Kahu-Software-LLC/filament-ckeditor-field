@php
    $name = $getName();
@endphp

<script type="text/javascript">

    function initCKEditor() {
        return {
            init() {
                document.addEventListener('livewire:navigated', () => {
                    ClassicEditor
                        .create(document.querySelector('#editor-{{ $field->getName() }}'), {
                            plugins: [
                                AccessibilityHelp,
                                Alignment,
                                Autoformat,
                                AutoImage,
                                AutoLink,
                                Autosave,
                                BlockQuote,
                                Bold,
                                Code,
                                CodeBlock,
                                Essentials,
                                FindAndReplace,
                                FontBackgroundColor,
                                FontColor,
                                FontFamily,
                                FontSize,
                                GeneralHtmlSupport,
                                Heading,
                                Highlight,
                                HorizontalLine,
                                HtmlComment,
                                HtmlEmbed,
                                ImageBlock,
                                ImageCaption,
                                ImageInline,
                                ImageInsert,
                                ImageInsertViaUrl,
                                ImageResize,
                                ImageStyle,
                                ImageTextAlternative,
                                ImageToolbar,
                                ImageUpload,
                                Indent,
                                IndentBlock,
                                Italic,
                                Link,
                                LinkImage,
                                List,
                                ListProperties,
                                MediaEmbed,
                                PageBreak,
                                Paragraph,
                                PasteFromOffice,
                                RemoveFormat,
                                SelectAll,
                                ShowBlocks,
                                SimpleUploadAdapter,
                                SourceEditing,
                                SpecialCharacters,
                                SpecialCharactersArrows,
                                SpecialCharactersCurrency,
                                SpecialCharactersEssentials,
                                SpecialCharactersLatin,
                                SpecialCharactersMathematical,
                                SpecialCharactersText,
                                Strikethrough,
                                Style,
                                Subscript,
                                Superscript,
                                Table,
                                TableCaption,
                                TableCellProperties,
                                TableColumnResize,
                                TableProperties,
                                TableToolbar,
                                TextTransformation,
                                TodoList,
                                Underline,
                                Undo
                            ],
                            toolbar: {
                                items: [
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
                                    'indent'
                                ],
                                shouldNotGroupWhenFull: false
                            },
                            autosave: {
                                save( editor ) {
                                    Livewire.dispatch('contentUpdated', { content: editor.getData(), editor: 'ckeditor' })
                                }
                            },
                            fontFamily: {
                                supportAllValues: true
                            },
                            fontSize: {
                                options: [10, 12, 14, 'default', 18, 20, 22],
                                supportAllValues: true
                            },
                            heading: {
                                options: [
                                    {
                                        model: 'paragraph',
                                        title: 'Paragraph',
                                        class: 'ck-heading_paragraph'
                                    },
                                    {
                                        model: 'heading1',
                                        view: 'h1',
                                        title: 'Heading 1',
                                        class: 'ck-heading_heading1'
                                    },
                                    {
                                        model: 'heading2',
                                        view: 'h2',
                                        title: 'Heading 2',
                                        class: 'ck-heading_heading2'
                                    },
                                    {
                                        model: 'heading3',
                                        view: 'h3',
                                        title: 'Heading 3',
                                        class: 'ck-heading_heading3'
                                    },
                                    {
                                        model: 'heading4',
                                        view: 'h4',
                                        title: 'Heading 4',
                                        class: 'ck-heading_heading4'
                                    },
                                    {
                                        model: 'heading5',
                                        view: 'h5',
                                        title: 'Heading 5',
                                        class: 'ck-heading_heading5'
                                    },
                                    {
                                        model: 'heading6',
                                        view: 'h6',
                                        title: 'Heading 6',
                                        class: 'ck-heading_heading6'
                                    }
                                ]
                            },
                            htmlSupport: {
                                allow: [
                                    {
                                        name: /^.*$/,
                                        styles: true,
                                        attributes: true,
                                        classes: true
                                    }
                                ]
                            },
                            image: {
                                toolbar: [
                                    'toggleImageCaption',
                                    'imageTextAlternative',
                                    '|',
                                    'imageStyle:inline',
                                    'imageStyle:wrapText',
                                    'imageStyle:breakText',
                                    '|',
                                    'resizeImage'
                                ]
                            },
                            link: {
                                addTargetToExternalLinks: true,
                                defaultProtocol: 'https://',
                                decorators: {
                                    toggleDownloadable: {
                                        mode: 'manual',
                                        label: 'Downloadable',
                                        attributes: {
                                            download: 'file'
                                        }
                                    }
                                }
                            },
                            list: {
                                properties: {
                                    styles: true,
                                    startIndex: true,
                                    reversed: true
                                }
                            },
                            menuBar: {
                                isVisible: true
                            },
                            placeholder: 'Type or paste your content here!',
                            style: {
                                definitions: [
                                    {
                                        name: 'Article category',
                                        element: 'h3',
                                        classes: ['category']
                                    },
                                    {
                                        name: 'Title',
                                        element: 'h2',
                                        classes: ['document-title']
                                    },
                                    {
                                        name: 'Subtitle',
                                        element: 'h3',
                                        classes: ['document-subtitle']
                                    },
                                    {
                                        name: 'Info box',
                                        element: 'p',
                                        classes: ['info-box']
                                    },
                                    {
                                        name: 'Side quote',
                                        element: 'blockquote',
                                        classes: ['side-quote']
                                    },
                                    {
                                        name: 'Marker',
                                        element: 'span',
                                        classes: ['marker']
                                    },
                                    {
                                        name: 'Spoiler',
                                        element: 'span',
                                        classes: ['spoiler']
                                    },
                                    {
                                        name: 'Code (dark)',
                                        element: 'pre',
                                        classes: ['fancy-code', 'fancy-code-dark']
                                    },
                                    {
                                        name: 'Code (bright)',
                                        element: 'pre',
                                        classes: ['fancy-code', 'fancy-code-bright']
                                    }
                                ]
                            },
                            table: {
                                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties']
                            }
                        })
                        .then(editor => {
                            Livewire.on('newContent', (content, ed) => {
                                if(ed === 'monaco')
                                    editor.destroy();
                            });

                            window.editor = editor;
                        })
                        .catch(err => {
                            console.error(err);
                        });
                });
            }
        }
    }

</script>

<div
    x-data="initCKEditor()"
    x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('filament-ckeditor-field', package: 'kahusoftware/filament-ckeditor-field'))]"
    x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('filament-ckeditor-field', package: 'kahusoftware/filament-ckeditor-field'))]"
>
    <textarea
        id="ckeditor-{{ $name }}"
        name="{{ $name }}"
    >{{ $getContent() }}</textarea>
</div>
