<?php

/**
 * `php artisan config:cache` writes the merged configuration out with
 * var_export(), which cannot represent objects or closures. Keeping the editor
 * defaults to arrays, scalars and `js:` strings is what makes the config file
 * safe to cache, so guard it.
 */
it('survives the var_export round trip that config caching performs', function () {
    $config = config('filament-ckeditor-field');

    $exported = var_export($config, true);

    expect(eval("return {$exported};"))->toBe($config);
});

it('contains no objects or closures anywhere in the editor defaults', function () {
    $walk = function (array $values) use (&$walk): void {
        foreach ($values as $value) {
            expect($value)->not->toBeObject();

            if (is_array($value)) {
                $walk($value);
            }
        }
    };

    $walk(config('filament-ckeditor-field.editor'));
});
