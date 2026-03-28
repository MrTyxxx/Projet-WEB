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
            'user' => $request->getAttribute('user'),
        ]);
    }

    public function pageOffres(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'page_offres.html.twig', [
             'user' => $request->getAttribute('user'),
        ]);
    }

    public function pageEntreprise(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'page_entreprise.html.twig', [
             'user' => $request->getAttribute('user'),
        ]);
    }

    public function mentions(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'Mentions.html.twig', [
            'user' => $request->getAttribute('user'),
        ]);
    }

    public function contact(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'Contact.html.twig', [
             'user' => $request->getAttribute('user'),
        ]);
    }

    public function monEspace(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'monespace.html.twig', [
             'user' => $request->getAttribute('user'),
        ]);
    }
}