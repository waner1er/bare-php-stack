
<?php

use App\Infrastructure\Blade\Blade;


$views = __DIR__ . '/../../Presentation/View';
$cache = __DIR__ . '/../../../storage/cache';

$blade = new Blade($views, $cache);
