@php
    $name = $getName();
@endphp

<script type="text/javascript">
    // Use a unique key based on the editor name to avoid conflicts
    const editorEventListenerKey = 'ckeditor-{{ $name }}-event-listener';
    const editorInstanceKey = 'ckeditor-{{ $name }}-instance';

    // Initialize flags and references in a scoped object to avoid polluting the global namespace
    window.CKEditorHelpers = window.CKEditorHelpers || {};
    window.CKEditorHelpers[editorEventListenerKey] = window.CKEditorHelpers[editorEventListenerKey] || false;
    window.CKEditorHelpers[editorInstanceKey] = window.CKEditorHelpers[editorInstanceKey] || null;

    function createCKEditor() {
        // Destroy existing editor if any to prevent duplicates
        if (window.CKEditorHelpers[editorInstanceKey]) {
            destroyCKEditor();
        }

        // Create new editor instance
        ClassicEditor
            .create(document.querySelector('#ckeditor-{{ $name }}'), {
                // ... (your CKEditor configuration)
            })
            .then(editor => {
                window.CKEditorHelpers[editorInstanceKey] = editor;
            })
            .catch(err => {
                console.error(err);
            });
    }

    function destroyCKEditor() {
        if (window.CKEditorHelpers[editorInstanceKey]) {
            window.CKEditorHelpers[editorInstanceKey].destroy()
                .then(() => {
                    window.CKEditorHelpers[editorInstanceKey] = null;
                })
                .catch(err => {
                    console.error('Failed to destroy editor:', err);
                });
        }

        // Clear out the wrapper's HTML to reset the editor
        const wrapper = document.getElementById('ckeditor-{{ $name }}-wrapper');
        if (wrapper) {
            wrapper.innerHTML = `<textarea id="ckeditor-{{ $name }}" name="{{ $name }}">{{ $getContent() }}</textarea>`;
        }
    }

    function editorComponent() {
        return {
            init() {
                // Remove existing event listeners to prevent duplicates
                document.removeEventListener('livewire:navigated', createCKEditor);
                document.removeEventListener('livewire:navigating', destroyCKEditor);

                // Add event listeners if not already added
                if (!window.CKEditorHelpers[editorEventListenerKey]) {
                    document.addEventListener('livewire:navigated', createCKEditor);
                    document.addEventListener('livewire:navigating', destroyCKEditor);
                    window.CKEditorHelpers[editorEventListenerKey] = true;
                }

                // Initialize the editor when the component is loaded
                createCKEditor();
            }
        }
    }
</script>

<div
    x-data="editorComponent()"
    x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('filament-ckeditor-field', package: 'kahusoftware/filament-ckeditor-field'))]"
    x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('filament-ckeditor-field', package: 'kahusoftware/filament-ckeditor-field'))]"
>
    <div id="ckeditor-{{ $name }}-wrapper">
        <textarea
            id="ckeditor-{{ $name }}"
            name="{{ $name }}"
        >{{ $getContent() }}</textarea>
    </div>
</div>