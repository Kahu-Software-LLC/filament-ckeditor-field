<div
    x-data
    x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptHref('filament-ckeditor-field', package: 'kahusoftware/filament-ckeditor-field'))]"
>
    <textarea id="ckeditor" name="ckeditor">{{ $getContent() }}</textarea>
</div>
