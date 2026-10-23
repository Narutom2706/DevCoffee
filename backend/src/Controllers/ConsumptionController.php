<?php

namespace App\Controllers;

use App\Models\Consumption;
use App\Services\CoffeeService;
use App\Responses\ProblemDetailsResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ConsumptionController {
    public function __construct(
        private CoffeeService $coffeeService
    ) {}
    
    public function index(Request $request, Response $response): Response {
        $consumptions = $this->coffeeService->getAllConsumptions();
        $baseUrl = (string)$request->getUri()->getScheme() . "://" . (string)$request->getUri()->getAuthority();
         $data = array_map(function ($c) use ($baseUrl) {
            $drink = $this->coffeeService->getDrinkById($c->drinkId);

            return [
                'id' => $c->id,
                'drinkName' => $drink->name,
                'caffeineMg' => $drink->caffeineMg,
                'developerName' => $c->developerName,
                'consumedAt' => $c->consumedAt,
                '_links' => [
                    'delete' => [
                        'href' => "{$baseUrl}/api/consumptions/{$c->id}",
                        'method' => 'DELETE'
                    ]
                ]
            ];
        }, $consumptions);
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function getConsumption(Request $request, Response $response, $args): Response {
        $id = $id = (int)$args['id'];
        if (empty($id)) {
            return ProblemDetailsResponse::create(
                $response, 400,
                'Validation Error', 'Un id est requis.'
            );
        }

        $consumption = $this->coffeeService->getConsumptionById($id);
        
        if (!$consumption) {
            return ProblemDetailsResponse::create(
                $response, 404,
                'Drink Not Found', 'La boisson demandée n\'existe pas.'
            );
        }

        $drink = $this->coffeeService->getDrinkById($consumption->drinkId);

        $payload = [
            'id' => $consumption->id,
            'drinkName' => $drink->name,
            'caffeineMg' => $drink->caffeineMg,
            'developerName' => $consumption->developerName,
            'consumedAt' => $consumption->consumedAt
        ];
        $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_SLASHES));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function storeConsumption(Request $request, Response $response): Response {
        $body = $request->getParsedBody() ?? json_decode($request->getBody()->getContents(), true);
        if (empty($body['drinkId']) || empty($body['developerName'])) {
            return ProblemDetailsResponse::create(
                $response, 400,
                'Validation Error', 'Les champs drinkId et developerName sont requis.'
            );
        }

        $drink = $this->coffeeService->getDrinkById((int)$body['drinkId']);
        
        if (!$drink) {
            return ProblemDetailsResponse::create(
                $response, 404,
                'Drink Not Found', 'La boisson demandée n\'existe pas.'
            );
        }

        $consumption = $this->coffeeService->createConsumption(
            (int)$body['drinkId'],
            $body['developerName']
        );

        if (is_null($consumption)) {
            return ProblemDetailsResponse::create(
                $response,
                429,
                'Caffeine Limit Exceeded',
                "La consomation de caffeine dépasse la limite max de caffeine par jour (" . (string)Consumption::$MAX_CAFFEINE_PER_DAY_MG . " mg)."
            );
        }

        $baseUrl = (string)$request->getUri()->getScheme() . "://" . (string)$request->getUri()->getAuthority();
        $payload = [
            'id' => $consumption->id,
            'drinkId' => $consumption->drinkId,
            'developerName' => $consumption->developerName,
            'consumedAt' => $consumption->consumedAt,
            '_links' => [
                'consumption' => ['href' => "{$baseUrl}/api/consumptions/{$consumption->id}", 'method' => 'GET']
            ]
        ];
        $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_SLASHES));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function deleteConsumption(Request $request, Response $response, $args): Response {
       $id = (int)$args['id'];

        if (empty($id)) {
            return ProblemDetailsResponse::create(
                $response, 400,
                'Validation Error', 'Un id est requis.'
            );
        }

        if (!$this->coffeeService->deleteConsumptionById($id)) {
            return ProblemDetailsResponse::create(
                $response, 404,
                'Consumption Not Found', 'La consomation demandée n\'existe pas.'
            );
        }

        return $response->withStatus(204);
    }
}