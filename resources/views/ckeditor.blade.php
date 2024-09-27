@php
    $name = $getName();
@endphp

<script type="text/javascript">

    function initCKEditor() {
        return {
            init() {
                window.ClassicEditor
                    .create( document.querySelector( '#ckeditor-{{ $name }}' ) )
                    .then( editor => {
                        console.log( editor );
                    } )
                    .catch( err => {
                        console.error( err.stack );
                    } );
            }
        }
    }

</script>

<div
    x-data="initCKEditor()"
    x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptHref('filament-ckeditor-field', package: 'kahusoftware/filament-ckeditor-field'))]"
    x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('filament-ckeditor-field', package: 'kahusoftware/filament-ckeditor-field'))]"
>
    <textarea
        id="ckeditor-{{ $name }}"
        name="{{ $name }}"
    >{{ $getContent() }}</textarea>
</div>
