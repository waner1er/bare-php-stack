
<?php

use App\Tools\Blade;

$views = __DIR__ . '/../../resources/views';
$cache = __DIR__ . '/../../storage/cache';

$blade = new Blade($views, $cache);
