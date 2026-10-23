<?php

namespace App\Controllers;

use App\Models\Consumption;
use App\Services\CoffeeService;
use App\Responses\ProblemDetailsResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DrinkController {
    public function __construct(
        private CoffeeService $coffeeService
    ) {}
    
    public function index(Request $request, Response $response): Response {
        $drinks = $this->coffeeService->getAllDrinks();
        $baseUrl = (string)$request->getUri()->getScheme() . "://" . (string)$request->getUri()->getAuthority();
        $data = array_map(fn($d) => [
            'id' => $d->id,
            'name' => $d->name,
            'caffeineMg' => $d->caffeineMg,
            'volumeMl' => $d->volumeMl,
            '_links' => [
                'self' => ['href' => "{$baseUrl}/api/drinks/{$d->id}", 'method' => 'GET']
            ]
        ], $drinks);
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function getDrink(Request $request, Response $response, $args): Response {
        $id = (int)$args['id'];
        if (empty($id)) {
            return ProblemDetailsResponse::create(
                $response, 400,
                'Validation Error', 'Un id est requis.'
            );
        }

        $drink = $this->coffeeService->getDrinkById($id);
        
        if (!$drink) {
            return ProblemDetailsResponse::create(
                $response, 404,
                'Drink Not Found', 'La boisson demandée n\'existe pas.'
            );
        }

        $payload = [
            'id' => $drink->id,
            'name' => $drink->name,
            'caffeineMg' => $drink->caffeineMg,
            'volumeMl' => $drink->volumeMl
        ];
        $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_SLASHES));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function storeDrink(Request $request, Response $response): Response {
        $body = $request->getParsedBody() ?? json_decode($request->getBody()->getContents(), true);
        if (empty($body['name']) || empty($body['caffeineMg']) || empty($body['volumeMl'])) {
            return ProblemDetailsResponse::create(
                $response, 400,
                'Validation Error', 'Les champs name, caffeineMg et volumeMl sont requis.'
            );
        }

        $drink = $this->coffeeService->createDrink(
            $body['name'],
            (int)$body['caffeineMg'],
            (int)$body['volumeMl']
        );

        $baseUrl = (string)$request->getUri()->getScheme() . "://" . (string)$request->getUri()->getAuthority();
        $payload = [
            'id' => $drink->id,
            'name' => $drink->name,
            'caffeineMg' => $drink->caffeineMg,
            'volumeMl' => $drink->volumeMl,
            '_links' => [
                'drink' => ['href' => "{$baseUrl}/api/drinks/{$drink->id}", 'method' => 'GET']
            ]
        ];
        $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_SLASHES));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}