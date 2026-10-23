<?php

use App\Controllers\DrinkController;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use App\Core\Database;
use App\Controllers\ConsumptionController;
use App\Middlewares\CorsMiddleware;
use App\Middlewares\EcoMetricsMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);
$containerBuilder->addDefinitions([
    PDO::class => function () {
    return Database::getInstance();
    }
]);
$container = $containerBuilder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->add(new EcoMetricsMiddleware);
$app->add(new CorsMiddleware);

$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});

$app->get('/api/consumptions', [ConsumptionController::class, 'index']);
$app->get('/api/consumptions/{id}', [ConsumptionController::class, 'getConsumption']);
$app->post('/api/consumptions', [ConsumptionController::class, 'storeConsumption']);
$app->delete('/api/consumptions/{id}', [ConsumptionController::class, 'deleteConsumption']);

$app->get('/api/drinks', [DrinkController::class, 'index']);
$app->get('/api/drinks/{id}', [DrinkController::class, 'getDrink']);
$app->post('/api/drinks', [DrinkController::class, 'storeDrink']);

$app->run();