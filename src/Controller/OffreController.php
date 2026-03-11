<?php
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment;

class OffreController
{
    public function __construct(private Environment $twig) {}

    public function show(Request $request, Response $response, array $args): Response
    {
        $html = $this->twig->render('offre1.html.twig', [
            'id'   => $args['id'],
            'user' => $_SESSION['user'] ?? null,
        ]);
        $response->getBody()->write($html);
        return $response;
    }
}