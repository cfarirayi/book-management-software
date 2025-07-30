<?php

namespace Api\Books;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class BooksController
{
    public function index(Request $request, Response $response)
    {
        $db = new \PDO('mysql:host=database;dbname=assess_db', 'root', 'secret');
        $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        $books = $db->query('SELECT * FROM books')->fetchAll();

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($books));
    }

    public function create(Request $request, Response $response)
    {
        $db = new \PDO('mysql:host=database;dbname=assess_db', 'root', 'secret');
        $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        $params = $request->getQueryParams(); 

   
        if (empty($params['title']) || empty($params['author_id']) || empty($params['currency_code']) || empty($params['price'])) {
            return $response->withStatus(400)
                            ->withHeader('Content-Type', 'application/json')
                            ->write(json_encode(['error' => 'Missing required fields']));
        }

    
        $stmt = $db->prepare('INSERT INTO books (title, author_id) VALUES (:title, :author_id)');
        $stmt->execute([
            ':title' => $params['title'],
            ':author_id' => $params['author_id']
        ]);
        $book_id = $db->lastInsertId();

        $currencyCode = strtoupper(trim($params['currency_code']));
        $stmt = $db->prepare('SELECT * FROM currencies WHERE iso = :iso');
        $stmt->execute([':iso' => $currencyCode]);
        $currency = $stmt->fetch();

        if (!$currency) {
            return $response->withStatus(400)
                            ->withHeader('Content-Type', 'application/json')
                            ->write(json_encode(['error' => 'Invalid currency code']));
        }

      
        $stmt = $db->prepare('INSERT INTO book_pricing (book_id, currency_id, price) VALUES (:book_id, :currency_id, :price)');
        $stmt->execute([
            ':book_id' => $book_id,
            ':currency_id' => $currency['id'],
            ':price' => $params['price']
        ]);

        
        $book = $db->query('SELECT * FROM books WHERE id = ' . (int)$book_id)->fetch();

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($book));
    }
}
