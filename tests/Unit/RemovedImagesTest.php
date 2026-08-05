<?php

use Kahusoftware\FilamentCkeditorField\CKEditor;

it('returns urls of images removed between two documents', function () {
    $old = '<p>Intro</p><figure class="image"><img src="/storage/uploads/a.jpg"></figure><p><img src="/storage/uploads/b.jpg"></p>';
    $new = '<p>Intro</p><p><img src="/storage/uploads/b.jpg"></p>';

    expect(CKEditor::findRemovedImages($old, $new))->toBe(['/storage/uploads/a.jpg']);
});

it('returns an empty array when no images were removed', function () {
    $html = '<p><img src="/storage/uploads/a.jpg"></p>';

    expect(CKEditor::findRemovedImages($html, $html))->toBe([]);
});

it('returns all image urls when the new document is empty', function () {
    $old = '<p><img src="/storage/uploads/a.jpg"><img src="/storage/uploads/b.jpg"></p>';

    expect(CKEditor::findRemovedImages($old, ''))
        ->toBe(['/storage/uploads/a.jpg', '/storage/uploads/b.jpg'])
        ->and(CKEditor::findRemovedImages($old, null))
        ->toBe(['/storage/uploads/a.jpg', '/storage/uploads/b.jpg']);
});

it('returns an empty array when the old document is empty', function () {
    // The create flow has no previous state, so nothing can have been removed.
    $new = '<p><img src="/storage/uploads/a.jpg"></p>';

    expect(CKEditor::findRemovedImages(null, $new))->toBe([])
        ->and(CKEditor::findRemovedImages('', $new))->toBe([]);
});

it('ignores images that only exist in the new document', function () {
    $old = '<p><img src="/storage/uploads/a.jpg"></p>';
    $new = '<p><img src="/storage/uploads/a.jpg"><img src="/storage/uploads/added.jpg"></p>';

    expect(CKEditor::findRemovedImages($old, $new))->toBe([]);
});

it('reports a repeated removed url once', function () {
    $old = '<p><img src="/storage/uploads/a.jpg"></p><p><img src="/storage/uploads/a.jpg"></p>';

    expect(CKEditor::findRemovedImages($old, '<p>text</p>'))->toBe(['/storage/uploads/a.jpg']);
});

it('keeps a url that still appears elsewhere in the new document', function () {
    $old = '<p><img src="/storage/uploads/a.jpg"></p><p><img src="/storage/uploads/a.jpg"></p>';
    $new = '<p><img src="/storage/uploads/a.jpg"></p>';

    expect(CKEditor::findRemovedImages($old, $new))->toBe([]);
});

it('filters removed urls to the given prefix', function () {
    $old = '<p><img src="/storage/uploads/a.jpg"><img src="https://external.example.com/hotlinked.png"></p>';

    expect(CKEditor::findRemovedImages($old, '<p>text</p>', '/storage/uploads/'))
        ->toBe(['/storage/uploads/a.jpg']);
});

it('extracts image urls from malformed html', function () {
    $old = '<p><img src="/storage/uploads/a.jpg"><div><span>unclosed';

    expect(CKEditor::findRemovedImages($old, ''))->toBe(['/storage/uploads/a.jpg']);
});

it('ignores img tags without a src attribute', function () {
    $old = '<p><img alt="decorative"><img src="/storage/uploads/a.jpg"></p>';

    expect(CKEditor::findRemovedImages($old, ''))->toBe(['/storage/uploads/a.jpg']);
});

it('handles multibyte content around images', function () {
    $old = '<p>日本語のテキスト</p><p><img src="/storage/uploads/寿司.jpg"></p>';

    expect(CKEditor::findRemovedImages($old, '<p>日本語のテキスト</p>'))->toBe(['/storage/uploads/寿司.jpg']);
});
