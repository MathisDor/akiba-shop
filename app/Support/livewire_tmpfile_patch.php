<?php

namespace Livewire\Features\SupportFileUploads;

/**
 * InfinityFree blocks writes to the OS temp dir (returned by
 * sys_get_temp_dir()) via filesystem permissions, even though
 * open_basedir allows it — so PHP's native tmpfile() always returns
 * false there. Neither ini_set('sys_temp_dir', ...) nor putenv('TMPDIR=...')
 * can override it: the directive is locked at PHP_INI_SYSTEM level.
 *
 * Livewire's TemporaryUploadedFile constructor calls tmpfile() only to
 * obtain a resource pointing at *some* existing file, to satisfy
 * Symfony's UploadedFile validation — the real uploaded content lives
 * elsewhere, on the configured disk. Because this file declares a
 * function with the same name in the same namespace as the code that
 * calls tmpfile() unqualified, PHP resolves that call to this function
 * instead of the global built-in (PHP's namespaced-function-fallback
 * rule), without touching vendor code.
 */
function tmpfile()
{
    $dir = storage_path('app/tmp');

    if (! is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    $path = @tempnam($dir, 'lw');

    if ($path === false) {
        return false;
    }

    $resource = @fopen($path, 'r+b');

    if ($resource === false) {
        @unlink($path);

        return false;
    }

    register_shutdown_function(static function () use ($path) {
        @unlink($path);
    });

    return $resource;
}
