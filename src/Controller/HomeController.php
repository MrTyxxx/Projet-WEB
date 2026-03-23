<?php
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class HomeController
{
    public function index(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'acceuil.html.twig', [
            'user' => $_SESSION['user'] ?? null,
        ]);
    }

    public function pageOffres(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'page_offres.html.twig', [
            'user' => $_SESSION['user'] ?? null,
        ]);
    }

    public function pageEntreprise(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'page_entreprise.html.twig', [
            'user' => $_SESSION['user'] ?? null,
        ]);
    }

    public function mentions(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'Mentions.html.twig', [
            'user' => $_SESSION['user'] ?? null,
        ]);
    }

    public function contact(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'Contact.html.twig', [
            'user' => $_SESSION['user'] ?? null,
        ]);
    }

    public function monEspace(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'monespace.html.twig', [
            'user' => $_SESSION['user'] ?? null,
        ]);
    }
}