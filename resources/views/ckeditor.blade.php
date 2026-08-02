@php
    $name = $getName();
    $isConcealed = $isConcealed();
    $statePath = $getStatePath();
    $isDisabled = $isDisabled();
    // Create a safe identifier from statePath for use in JavaScript
    $editorId = str_replace(['.', '[', ']'], ['-', '-', ''], $statePath);
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <x-filament::input.wrapper
        :valid="! $errors->has($statePath)"
    >
        <div wire:ignore>
            <script type="text/javascript">
                // Initialize the instance and event listener flags if not already set
                if (!window.ckeditorInstances["ckeditor-{{ $editorId }}"]) {
                    window.ckeditorInstances["ckeditor-{{ $editorId }}"] = {
                        instance: null,
                        eventListenerAdded: false,
                        createHandler: null,
                        destroyHandler: null
                    };
                }

                // The editor configuration is resolved in PHP so it can be changed
                // from the config file, a service provider, or the field itself. It
                // is stored per editor rather than inside createCKEditor below,
                // because that function is shared by every field on the page.
                window.ckeditorInstances["ckeditor-{{ $editorId }}"].config = {!! $getEditorOptionsJs() !!};

                window.createCKEditor = function(editorId, statePath, alpineComponent) {
                    const instanceKey = "ckeditor-" + editorId;
                    
                    // To prevent duplicates, halt here if an editor already exists
                    if (window.ckeditorInstances[instanceKey]?.instance) {
                        return;
                    }

                    // Check if the textarea element exists
                    const textarea = document.querySelector('#' + instanceKey);
                    if (!textarea) {
                        console.warn('CKEditor textarea not found for: ' + instanceKey);
                        return;
                    }

                    // Store statePath and Alpine component for this editor instance
                    window.ckeditorInstances[instanceKey].statePath = statePath;
                    window.ckeditorInstances[instanceKey].alpineComponent = alpineComponent;

                    // Plugin constructors cannot be serialised, so this editor's
                    // configuration carries plugin names that are looked up in the
                    // window scope here. Names that are not bundled are skipped.
                    const editorConfig = { ...(window.ckeditorInstances[instanceKey].config ?? {}) };

                    editorConfig.plugins = (editorConfig.plugins ?? [])
                        .map((name) => window[name])
                        .filter(Boolean);

                    // Create new editor instance
                    ClassicEditor
                        .create(textarea, editorConfig)
                        .then(editor => {
                            const instanceKey = "ckeditor-" + editorId;
                            window.ckeditorInstances[instanceKey].instance = editor;
                            let instance = window.ckeditorInstances[instanceKey].instance;

                            // Find the main ckeditor class and add some helpful class names to it
                            const editorMain = document.querySelector('#' + instanceKey + ' + .ck-editor .ck-editor__main');
                            if (editorMain) {
                                editorMain.classList.add('prose', 'max-w-none', 'dark:prose-invert');
                            }

                            const sync = () => {
                                const inst = window.ckeditorInstances[instanceKey];
                                if (!inst?.alpineComponent) return;

                                inst.__fromEditor = true;
                                inst.alpineComponent.state = editor.getData();
                                inst.__fromEditor = false;
                            };

                            // Listen to changes (only if not disabled)
                            @if(!$isDisabled)

                                // Update Alpine state immediately on every change (no network calls)
                                editor.model.document.on('change:data', sync);

                                // Flush on Ctrl+S BEFORE Filament triggers save
                                instance.onKeyDown = (e) => {
                                    const isSave = (e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S');
                                    if (!isSave) return;
                                    sync();
                                };

                                window.addEventListener('keydown', instance.onKeyDown, true);

                            @else

                                editor.enableReadOnlyMode('{{ $editorId }}');

                            @endif

                        })
                        .catch(err => {
                            console.error('Error creating CKEditor:', err);
                            // Clear instance on error to allow retry
                            const instanceKey = "ckeditor-" + editorId;
                            if (window.ckeditorInstances[instanceKey]) {
                                window.ckeditorInstances[instanceKey].instance = null;
                            }
                        });
                }

                window.destroyCKEditor = function(editorId) {
                    const instanceData = window.ckeditorInstances["ckeditor-" + editorId];
                    if (!instanceData?.instance) return;

                    const instance = instanceData.instance;

                    if (instance.onKeyDown) {
                        window.removeEventListener('keydown', instance.onKeyDown, true);
                        instance.onKeyDown = null;
                    }

                    instance.destroy().then(() => {
                        // Clear the instance reference after destruction
                        window.ckeditorInstances["ckeditor-" + editorId].instance = null;
                        window.ckeditorInstances["ckeditor-" + editorId].alpineComponent = null;
                        window.ckeditorInstances["ckeditor-" + editorId].__fromEditor = false;
                    }).catch(err => {
                        console.error('Error destroying CKEditor:', err);
                        // Clear reference even on error to allow re-initialization
                        window.ckeditorInstances["ckeditor-" + editorId].instance = null;
                    });
                }

                // Create bound wrapper functions for event listeners (will be set with Alpine component in init)
                window.ckeditorInstances["ckeditor-{{ $editorId }}"].createHandler = null;
                window.ckeditorInstances["ckeditor-{{ $editorId }}"].destroyHandler = () => destroyCKEditor('{{ $editorId }}');
            </script>
            <div
                x-data="{
                    state: $wire.$entangle('{{ $getStatePath() }}'),
                    init() {
                        const key = 'ckeditor-{{ $editorId }}';

                        window.ckeditorInstances = window.ckeditorInstances || {};
                        const instance = window.ckeditorInstances[key] = window.ckeditorInstances['ckeditor-{{ $editorId }}'] || {};

                        const waitFor = (fnName, cb) => {
                            const t = setInterval(() => {
                                if (typeof window[fnName] === 'function') {
                                    clearInterval(t);
                                    cb();
                                }
                            }, 25);
                        };

                        // Remove existing event listeners to prevent duplicates
                        if (instance?.createHandler) {
                            document.removeEventListener('livewire:navigated', instance.createHandler);
                        }
                        if (instance?.destroyHandler) {
                            document.removeEventListener('livewire:navigate', instance.destroyHandler);
                        }

                        // Create handler with Alpine component context
                        instance.createHandler = () => waitFor('createCKEditor', () => window.createCKEditor('{{ $editorId }}', '{{ $statePath }}', this));

                        instance.destroyHandler = () => waitFor('destroyCKEditor', () => window.destroyCKEditor('{{ $editorId }}'));

                        // Add event listeners if not already added
                        document.addEventListener('livewire:navigated', instance.createHandler);
                        document.addEventListener('livewire:navigate', instance.destroyHandler);

                        // Initialize editor immediately if ClassicEditor is available
                        this.$nextTick(() => {
                            if (!instance.instance) {
                                instance.createHandler();
                            }
                        });

                        // Watch for state changes and update editor content
                        this.$watch('state', (value) => {
                            const editor = instance.instance;

                            if (!editor) return;
                            if (instance.__fromEditor) return;

                            if (value !== null && value !== undefined) {
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

                        // Remove event listeners
                        if (instance?.createHandler) {
                            document.removeEventListener('livewire:navigated', instance.createHandler);
                        }
                        if (instance?.destroyHandler) {
                            document.removeEventListener('livewire:navigate', instance.destroyHandler);
                        }

                        // Destroy the editor instance
                        if (instance?.instance) {
                            window.destroyCKEditor('{{ $editorId }}');
                        }
                    }
                }"
                x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('filament-ckeditor-field', package: 'kahusoftware/filament-ckeditor-field'))]"
                x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('filament-ckeditor-field', package: 'kahusoftware/filament-ckeditor-field'))]"
            >
                <textarea
                    id="ckeditor-{{ $editorId }}"
                    name="{{ $name }}"
                    x-model="state"
                ></textarea>
            </div>
        </div>
    </x-filament::input.wrapper>
</x-dynamic-component>
