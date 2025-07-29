<?php

namespace Api\Currencies;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class CurrenciesController
{
    public function index(Request $request, Response $response)
    {
        // Connect to the database
        $db = new \PDO('mysql:host=database;dbname=assess_db', 'root', 'secret');
        $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ); // <-- CHANGED from FETCH_ASSOC

        // Fetch all currencies
        $currencies = $db->query('SELECT * FROM currencies')->fetchAll();

        // Return as JSON
        $response->getBody()->write(json_encode($currencies));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
