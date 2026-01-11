int
<?php

use App\Infrastructure\Blade\Blade;


$views = INTERFACE_PATH . '/View';
$cache = ROOT_PATH . '/storage/cache';

$blade = new Blade($views, $cache);
