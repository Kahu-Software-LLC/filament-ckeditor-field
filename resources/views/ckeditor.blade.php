<div x-data {{--x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('filament-ckeditor-field', package: 'kahusoftware/filament-ckeditor-field'))]"--}}>
    <textarea id="ckeditor" name="ckeditor">{{ $getContent() }}</textarea>
</div>
