@php
    $statePath = $getStatePath();
    // Safe identifier from statePath for use in DOM ids
    $editorId = str_replace(['.', '[', ']'], ['-', '-', ''], $statePath);
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <x-filament::input.wrapper
        :valid="! $errors->has($statePath)"
    >
        {{--
            The whole editor lives behind wire:ignore so Livewire never
            morphs inside CKEditor's DOM, and behind x-load so Alpine defers
            initialisation until the ckeditorField component module has
            loaded. All behaviour is in that module rather than an inline
            script, because inline scripts never execute in Livewire-morphed
            HTML (repeater items and the like).
        --}}
        <div
            wire:ignore
            x-load
            x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('ckeditor-field', 'kahusoftware/filament-ckeditor-field') }}"
            x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('filament-ckeditor-field', package: 'kahusoftware/filament-ckeditor-field'))]"
            x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('filament-ckeditor-field', package: 'kahusoftware/filament-ckeditor-field'))]"
            x-data="ckeditorField({
                state: $wire.$entangle('{{ $statePath }}'),
                config: {{ $getEditorOptionsJs() }},
                isDisabled: @js($isDisabled()),
            })"
        >
            {{--
                Deliberately no x-model: the editor receives its content as
                initialData from the entangled state, because directive
                processing order cannot be relied on when the bundle is
                already loaded (SPA navigation).
            --}}
            <textarea
                id="ckeditor-{{ $editorId }}"
                name="{{ $getName() }}"
                x-ref="textarea"
            ></textarea>
        </div>
    </x-filament::input.wrapper>
</x-dynamic-component>
