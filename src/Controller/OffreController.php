<?php
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class OffreController
{
    public function show(Request $request, Response $response, array $args): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'offre1.html.twig', [
            'id'   => $args['id'],
            'user' => $_SESSION['user'] ?? null,
        ]);
    }
     public function gestionOffres(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'gestion-offres.html.twig', [
            'user'   => $_SESSION['user'],
            'active' => 'offres',
        ]);
    }

    public function creerOffreForm(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'creer-offre.html.twig', [
            'user'   => $_SESSION['user'],
            'active' => 'offres',
        ]);
    }
}