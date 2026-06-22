<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $name = $getName();
        $uploadUrl = $getUploadUrl();
        $placeholder = $getPlaceholder();
        $isConcealed = $isConcealed();
        $statePath = $getStatePath();
        $isDisabled = $isDisabled();
        $height = $getHeight();
        $showPreview = $getShowPreview();
        // Safe identifier from statePath for use in JavaScript
        $editorId = str_replace(['.', '[', ']'], ['-', '-', ''], $statePath);
    @endphp
    <x-filament::input.wrapper
        :valid="! $errors->has($statePath)"
    >
        <div wire:ignore>
            <script type="text/javascript">
                // Ensure the global registry exists. The head render-hook initializer
                // can lose the race with this inline script under some panel render orders.
                window.ckeditorInstances ??= {};

                // Initialize the instance and event listener flags if not already set
                if (!window.ckeditorInstances["ckeditor-{{ $editorId }}"]) {
                    window.ckeditorInstances["ckeditor-{{ $editorId }}"] = {
                        instance: null,
                        eventListenerAdded: false,
                        createHandler: null,
                        destroyHandler: null
                    };
                }

                function createCKEditor(editorId, statePath, alpineComponent) {
                    // To prevent duplicates, halt here if an editor already exists
                    if (window.ckeditorInstances["ckeditor-" + editorId].instance) {
                        return;
                    }

                    // Check if the textarea element exists
                    const textarea = document.querySelector('#ckeditor-' + editorId);
                    if (!textarea) {
                        return;
                    }

                    // Store statePath and Alpine component for this editor instance
                    window.ckeditorInstances["ckeditor-" + editorId].statePath = statePath;
                    window.ckeditorInstances["ckeditor-" + editorId].alpineComponent = alpineComponent;

                    // Create new editor instance
                    ClassicEditor
                        .create(textarea, {
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

                                @if($uploadUrl)

                                    ImageInsert,
                                    ImageInsertViaUrl,
                                    ImageUpload,

                                @else

                                    ImageInsertViaUrl,

                                @endif

                                ImageResize,
                                ImageStyle,
                                ImageTextAlternative,
                                ImageToolbar,
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

                                @if($uploadUrl)

                                    SimpleUploadAdapter,
                                
                                @endif

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
                                    
                                    @if($uploadUrl)

                                        'insertImage',
                                    
                                    @endif
                                    
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
                                ],
                                disallow: [
                                    {
                                        styles: {
                                            'background-color': true,
                                            'color': true
                                        }
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
                            placeholder: '{{ $placeholder }}',
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
                            },

                            @isset($uploadUrl)

                                simpleUpload: {
                                    uploadUrl: '{{ $uploadUrl }}',
                                    withCredentials: true,
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                }

                            @endisset

                        })
                        .then(editor => {
                            window.ckeditorInstances["ckeditor-" + editorId].instance = editor;

                            // Scope to this editor's own container to avoid targeting other instances on the page
                            const ckMain = editor.ui.view.element?.querySelector('.ck-editor__main');
                            ckMain?.classList.add('prose', 'max-w-none', 'dark:prose-invert');

                            // Listen to changes (only if not disabled)
                            @if(!$isDisabled)

                                // Update Alpine state immediately on every change (no network calls)
                                editor.model.document.on('change:data', () => {
                                    const instance = window.ckeditorInstances["ckeditor-" + editorId];
                                    if (instance.alpineComponent) {
                                        // Update Alpine state directly (will sync via $entangle on form submit)
                                        instance.alpineComponent.state = editor.getData();
                                    }
                                });

                            @else

                                editor.enableReadOnlyMode('{{ $editorId }}');

                            @endif
                            
                        })
                        .catch(err => {
                            console.error(err);
                        });
                }

                function destroyCKEditor(editorId) {
                    const key = 'ckeditor-' + editorId;
                    const instance = window.ckeditorInstances?.[key];
                    if (!instance?.instance) return;

                    const editor = instance.instance;
                    instance.instance = null; // synchronous clear prevents createCKEditor race on next init()
                    editor.destroy().catch(err => console.error('CKEditor destroy failed:', err));
                }

                // Handlers are bound inside Alpine init() so they are also set up for
                // repeater items morphed in by Livewire (where this <script> tag does not execute).
            </script>
            <div
                x-data="{
                    state: $wire.$entangle('{{ $getStatePath() }}'),
                    init() {
                        // Ensure the global registry and this instance's entry both exist.
                        // Required for repeater items added via Livewire morph: their inline
                        // <script> tag is inserted via innerHTML and therefore never executes,
                        // so the top-of-file initializer does not run for those editor IDs.
                        window.ckeditorInstances ??= {};
                        if (!window.ckeditorInstances['ckeditor-{{ $editorId }}']) {
                            window.ckeditorInstances['ckeditor-{{ $editorId }}'] = {
                                instance: null,
                                eventListenerAdded: false,
                                createHandler: null,
                                destroyHandler: null,
                            };
                        }
                        const instance = window.ckeditorInstances['ckeditor-{{ $editorId }}'];

                        instance.createHandler = () => createCKEditor('{{ $editorId }}', '{{ $statePath }}', this);
                        instance.destroyHandler = () => destroyCKEditor('{{ $editorId }}');
                        
                        // Remove existing event listeners to prevent duplicates
                        if (instance.createHandler) {
                            document.removeEventListener('livewire:navigated', instance.createHandler);
                        }
                        if (instance.destroyHandler) {
                            document.removeEventListener('livewire:navigate', instance.destroyHandler);
                        }

                        // Add event listeners if not already added
                        if (!instance.eventListenerAdded && instance.createHandler && instance.destroyHandler) {
                            document.addEventListener('livewire:navigated', instance.createHandler);
                            document.addEventListener('livewire:navigate', instance.destroyHandler);
                            instance.eventListenerAdded = true;
                        }

                        // Initialize editor immediately if ClassicEditor is available
                        this.$nextTick(() => {
                            if (typeof ClassicEditor !== 'undefined' && !instance.instance) {
                                const textarea = document.querySelector('#ckeditor-{{ $editorId }}');
                                if (textarea) {
                                    createCKEditor('{{ $editorId }}', '{{ $statePath }}', this);
                                }
                            }
                        });

                        // Watch for state changes and update editor content
                        this.$watch('state', (value) => {
                            const editor = instance.instance;
                            if (editor && value !== null && value !== undefined) {
                                const currentContent = editor.getData();
                                // Only update if content actually changed to prevent loops
                                if (currentContent !== value) {
                                    editor.setData(value);
                                }
                            }
                        });
                    },
                    destroy() {
                        const key = 'ckeditor-{{ $editorId }}';
                        const instance = window.ckeditorInstances?.[key];
                        if (!instance) return;

                        if (instance.createHandler) {
                            document.removeEventListener('livewire:navigated', instance.createHandler);
                        }
                        if (instance.destroyHandler) {
                            document.removeEventListener('livewire:navigate', instance.destroyHandler);
                        }

                        if (instance.instance) {
                            const editor = instance.instance;
                            instance.instance = null; // synchronous clear before async destroy
                            editor.destroy().catch(console.error);
                        }

                        delete window.ckeditorInstances[key];
                    }
                }"
                x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('filament-ckeditor-field', package: 'wrteam/filament-ckeditor-field'))]"
                x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('filament-ckeditor-field', package: 'wrteam/filament-ckeditor-field'))]"
            >
                @if($height)
                    <style>
                        #ckeditor-{{ $editorId }}-wrapper .ck-editor__editable {
                            min-height: {{ $height }};
                            max-height: {{ $height }};
                            overflow-y: auto;
                        }
                    </style>
                @endif
                <div id="ckeditor-{{ $editorId }}-wrapper">
                    <textarea
                        id="ckeditor-{{ $editorId }}"
                        name="{{ $name }}"
                        x-model="state"
                    ></textarea>
                </div>

                @if($showPreview)
                    <div x-data="{ showPreview: false }" class="mt-1">
                        <button
                            type="button"
                            x-on:click="showPreview = !showPreview"
                            class="text-xs text-gray-500 hover:text-gray-700 underline dark:text-gray-400 dark:hover:text-gray-200"
                            x-text="showPreview ? 'Hide Preview' : 'Show Preview'"
                        ></button>
                        <div
                            x-show="showPreview"
                            x-transition
                            class="mt-1 rounded-lg border border-gray-200 bg-white p-4 prose max-w-none dark:border-gray-700 dark:bg-gray-900 dark:prose-invert"
                            x-html="state || '<p class=\'text-gray-400\'>No content to preview</p>'"
                        ></div>
                    </div>
                @endif
            </div>
        </div>
    </x-filament::input.wrapper>
</x-dynamic-component>
