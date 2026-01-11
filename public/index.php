<?php

declare(strict_types=1);

require __DIR__ . '/../config/paths.php';
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

use App\Infrastructure\Utils\Debug;
use App\Infrastructure\Router\Router;
use App\Infrastructure\Session\Session;
use App\Interface\FrontEnd\Controller\AuthController;
use App\Interface\FrontEnd\Controller\PostController;
use App\Interface\FrontEnd\Controller\HomeController;
use App\Interface\FrontEnd\Controller\ContactController;
use App\Interface\FrontEnd\Controller\ArchiveController;
use App\Interface\Admin\Controller\BackOfficeController;
use App\Interface\Admin\Controller\PostAdminController;

Session::start();

if (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === 'true') {
    Debug::enable();
} else {
    Debug::disable();
}

$router = new Router();
$router->registerController(HomeController::class);
$router->registerController(PostController::class);
$router->registerController(ContactController::class);
$router->registerController(ArchiveController::class);
$router->registerController(AuthController::class);
$router->registerController(BackOfficeController::class);
$router->registerController(PostAdminController::class);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($uri, $method);
