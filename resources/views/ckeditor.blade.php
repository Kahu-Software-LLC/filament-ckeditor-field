@php
    $name = $getName();
@endphp

<script type="text/javascript">

    function initEditor() {
        window.ClassicEditor
            .create( document.querySelector( '#ckeditor-{{ $name }}' ) )
            .then( editor => {
                console.log( editor );
            } )
            .catch( err => {
                console.error( err.stack );
            } );
    }

    function initCKEditor() {
        return {
            init() {
                document.addEventListener('livewire:navigated', () => {
                    console.log('Livewire navigated');
                    console.log(window.ClassicEditor, 'ClassicEditor', typeof ClassicEditor, ClassicEditor, ClassicEditor?.version);
                    // setInterval waiting for ClassicEditor to be defined
                    const interval = setInterval(() => {
                        console.log('Waiting for CKEditor to be ready');
                        if (typeof window.ClassicEditor !== 'undefined') {
                            clearInterval(interval);

                            console.log('CKEditor is ready');

                            initEditor();
                        }
                    }, 100);
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
