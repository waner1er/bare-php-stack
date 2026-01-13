<?php

declare(strict_types=1);

require __DIR__ . '/paths.php';
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Infrastructure/Utils/helpers.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

use Tracy\Debugger;
use App\Infrastructure\Router\Router;
use App\Infrastructure\Session\Session;
use App\Infrastructure\Router\ControllerLoader;

Session::start();

if ($_ENV['APP_DEBUG'] === 'true') {
    Debugger::enable(Debugger::Development, LOGS_PATH);
    Debugger::$strictMode = true; // Mode strict pour le développement
    Debugger::$showBar = true; // Forcer l'affichage de la barre


} else {
    Debugger::enable(Debugger::Production, LOGS_PATH);
}

$router = new Router();

$loader = new ControllerLoader($router);
$loader->addPath(INTERFACE_PATH . '/FrontEnd/Controller');
$loader->addPath(INTERFACE_PATH . '/Admin/Controller');
$loader->addPath(INTERFACE_PATH . '/API');
$loader->loadControllers();



$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$router->dispatch($uri, $method);
