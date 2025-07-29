<?php

namespace App\Books;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;

class BooksController
{
    private function fetchFromApi(string $url)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FAILONERROR => true,
        ]);

        $result = curl_exec($ch);

        if ($result === false) {
            error_log('API fetch failed: ' . curl_error($ch));
            curl_close($ch);
            return [];
        }

        curl_close($ch);
        return json_decode($result);
    }

    public function index(Request $request, Response $response)
    {
        $books = $this->fetchFromApi('http://api.localtest.me/books');
        $authors = $this->fetchFromApi('http://api.localtest.me/authors');

        // Attach author details to each book
        foreach ($books as $key => $book) {
            foreach ($authors as $author) {
                if ($book->author_id == $author->id) {
                    $books[$key]->author = $author;
                    break;
                }
            }
        }

        $renderer = new PhpRenderer('../src/Books/templates/');
        return $renderer->render($response, 'list.php', [
            'books' => $books,
        ]);
    }

    public function create(Request $request, Response $response)
    {
        // If form was submitted via GET
        $params = $request->getQueryParams();
        if (!empty($params)) {
            $ch = curl_init('http://api.localtest.me/books/create?' . http_build_query($params));
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
            ]);

            $apiResponse = curl_exec($ch);

            if ($apiResponse === false) {
                error_log('Book creation failed: ' . curl_error($ch));
                curl_close($ch);
                return $response->withStatus(500)->write('Error creating book.');
            }

            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($statusCode >= 400) {
                return $response->withStatus(400)->write('Invalid book data.');
            }

            return $response->withStatus(302)->withHeader('Location', '/books');
        }

        // Show the form
        $authors = $this->fetchFromApi('http://api.localtest.me/authors');
        $currencies = $this->fetchFromApi('http://api.localtest.me/currencies');

        $renderer = new PhpRenderer('../src/Books/templates/');
        return $renderer->render($response, 'create.php', [
            'authors' => $authors,
            'currencies' => $currencies,
        ]);
    }
}
