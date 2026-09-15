<?php

use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\View\View;
use Kahusoftware\FilamentCkeditorField\CKEditor;
use Livewire\Component;
use Livewire\Livewire;

// Filament reduces an $entangle(...) expression to its live flag alone and
// drops the blur and debounce modifiers, so ->lazy() and ->debounce() cannot
// be satisfied by the binding expression. These tests pin what reaches the
// browser for each modifier: the flag inside the entangle call, and the two
// props the component uses to implement the rest itself.

function renderLiveCKEditorField(Closure $configureSchema): string
{
    $component = new class extends Component implements HasSchemas
    {
        use InteractsWithSchemas;

        public static Closure $configureSchema;

        public ?string $content = null;

        public function form(Schema $schema): Schema
        {
            return (static::$configureSchema)($schema)->statePath('data');
        }

        public function render(): View
        {
            return view('test::test-form');
        }
    };

    $component::$configureSchema = $configureSchema;

    return Livewire::test($component::class)->assertSuccessful()->html();
}

it('binds state without the live flag by default', function () {
    $html = renderLiveCKEditorField(
        fn (Schema $schema) => $schema->components([CKEditor::make('content')]),
    );

    expect($html)
        ->toContain("\$entangle('data.content', false)")
        ->toContain('isLiveOnBlur: false')
        ->toContain('liveDebounce: null');
});

it('sets the live flag on the entangle call for a live field', function () {
    $html = renderLiveCKEditorField(
        fn (Schema $schema) => $schema->components([CKEditor::make('content')->live()]),
    );

    // ->live() needs nothing from the component: Livewire sends every write
    // because the flag is inside the entangle call itself.
    expect($html)
        ->toContain("\$entangle('data.content', true)")
        ->toContain('isLiveOnBlur: false')
        ->toContain('liveDebounce: null');
});

it('asks the component to commit on blur for a field made live on blur', function () {
    $html = renderLiveCKEditorField(
        fn (Schema $schema) => $schema->components([CKEditor::make('content')->live(onBlur: true)]),
    );

    // The entangle call is deliberately not live here. Committing on every
    // write is exactly what live-on-blur is asking not to do.
    expect($html)
        ->toContain("\$entangle('data.content', false)")
        ->toContain('isLiveOnBlur: true')
        ->toContain('liveDebounce: null');
});

it('treats lazy the same as live on blur', function () {
    $html = renderLiveCKEditorField(
        fn (Schema $schema) => $schema->components([CKEditor::make('content')->lazy()]),
    );

    expect($html)
        ->toContain("\$entangle('data.content', false)")
        ->toContain('isLiveOnBlur: true')
        ->toContain('liveDebounce: null');
});

it('passes an explicit debounce through as milliseconds', function () {
    $html = renderLiveCKEditorField(
        fn (Schema $schema) => $schema->components([CKEditor::make('content')->debounce(750)]),
    );

    expect($html)
        ->toContain("\$entangle('data.content', false)")
        ->toContain('isLiveOnBlur: false')
        ->toContain('liveDebounce: 750');
});

it('normalises a debounce written with a unit', function () {
    $html = renderLiveCKEditorField(
        fn (Schema $schema) => $schema->components([CKEditor::make('content')->debounce('2s')]),
    );

    expect($html)->toContain('liveDebounce: 2000');
});

it('inherits a debounce from the parent container', function () {
    $html = renderLiveCKEditorField(
        fn (Schema $schema) => $schema
            ->components([CKEditor::make('content')])
            ->debounce(400),
    );

    expect($html)
        ->toContain('liveDebounce: 400')
        ->toContain('isLiveOnBlur: false');
});

it('inherits live on blur from the parent container', function () {
    $html = renderLiveCKEditorField(
        fn (Schema $schema) => $schema
            ->components([CKEditor::make('content')])
            ->live(onBlur: true),
    );

    expect($html)
        ->toContain("\$entangle('data.content', false)")
        ->toContain('isLiveOnBlur: true');
});

it('lets a field override an inherited debounce with live on blur', function () {
    $html = renderLiveCKEditorField(
        fn (Schema $schema) => $schema
            ->components([CKEditor::make('content')->lazy()])
            ->debounce(400),
    );

    // isLiveDebounced() short-circuits on isLiveOnBlur, so the component is
    // told to commit on blur and nothing schedules a timer.
    expect($html)
        ->toContain('isLiveOnBlur: true')
        ->toContain('liveDebounce: null');
});
