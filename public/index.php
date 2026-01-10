<?php

declare(strict_types=1);

require __DIR__ . '/../config/paths.php';
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

use App\Infrastructure\Utils\Debug;
use App\Infrastructure\Session\Session;
use App\Infrastructure\Router\Router;
use App\Presentation\Controller\PostController;
use App\Presentation\Controller\AuthController;
use App\Presentation\Controller\BackOfficeController;

Session::start();

if (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === 'true') {
    Debug::enable();
} else {
    Debug::disable();
}

$router = new Router();
$router->registerController(PostController::class);
$router->registerController(AuthController::class);
$router->registerController(BackOfficeController::class);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($uri, $method);
