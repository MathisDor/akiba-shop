<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// InfinityFree blocks writes to PHP's OS temp dir via filesystem
// permissions (open_basedir allows it, but the actual write fails), and
// the sys_temp_dir directive is locked so it can't be redirected via ini.
// This patches around Livewire's use of tmpfile() for file uploads —
// see the file for details.
require __DIR__.'/../app/Support/livewire_tmpfile_patch.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
