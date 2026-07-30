<?php

use Filament\Support\RawJs;
use Kahusoftware\FilamentCkeditorField\CKEditor;

it('encodes plain options as json', function () {
    $js = CKEditor::encodeOptions(['menuBar' => ['isVisible' => true]]);

    expect($js)->toBe('{"menuBar":{"isVisible":true}}');
});

it('emits a js: string as a bare javascript expression', function () {
    $js = CKEditor::encodeOptions(['name' => 'js:/^.*$/']);

    expect($js)->toBe('{"name":/^.*$/}');
});

it('emits a RawJs instance as a bare javascript expression', function () {
    $js = CKEditor::encodeOptions(['name' => RawJs::make('/^p$/')]);

    expect($js)->toBe('{"name":/^p$/}');
});

it('emits js expressions nested inside lists and maps', function () {
    $js = CKEditor::encodeOptions([
        'htmlSupport' => [
            'allow' => [
                ['name' => 'js:/^.*$/', 'styles' => true],
            ],
        ],
    ]);

    expect($js)->toBe('{"htmlSupport":{"allow":[{"name":/^.*$/,"styles":true}]}}');
});

it('leaves strings that merely contain js: untouched', function () {
    $js = CKEditor::encodeOptions(['placeholder' => 'Paste js: code here']);

    expect($js)->toBe('{"placeholder":"Paste js: code here"}');
});

it('leaves forward slashes readable', function () {
    $js = CKEditor::encodeOptions(['link' => ['defaultProtocol' => 'https://']]);

    expect($js)->toBe('{"link":{"defaultProtocol":"https://"}}');
});

it('escapes characters that would break out of a script context', function () {
    $js = CKEditor::encodeOptions(['placeholder' => '</script><img src=x onerror=alert(1)>']);

    expect($js)
        ->not->toContain('</script>')
        ->not->toContain('<img')
        ->toContain('u003C');
});

it('escapes ampersands, quotes and apostrophes in option values', function () {
    $value = 'Tom & "Jerry" \'quoted\'';

    $js = CKEditor::encodeOptions(['placeholder' => $value]);

    // Nothing that could close an attribute or open a tag survives literally,
    // and the value still round-trips through a JSON parse.
    expect(substr_count($js, '&'))->toBe(0)
        ->and(substr_count($js, "'"))->toBe(0)
        ->and(json_decode($js, true)['placeholder'])->toBe($value);
});

it('serialises a field to javascript with the plugin list as plain names', function () {
    $js = CKEditor::make('content')->getEditorOptionsJs();

    expect($js)
        ->toContain('"plugins":["AccessibilityHelp"')
        ->toContain('"name":/^.*$/');
});
