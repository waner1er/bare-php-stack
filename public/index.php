<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

use App\Tools\Debug;
use App\Tools\Session;
use App\Router\Router;
use App\Controller\PostController;
use App\Controller\AuthController;

Session::start();

if (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === 'true') {
    Debug::enable();
} else {
    Debug::disable();
}

$router = new Router();
$router->registerController(PostController::class);
$router->registerController(AuthController::class);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($uri, $method);
