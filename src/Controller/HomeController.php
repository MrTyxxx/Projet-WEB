<?php
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class HomeController
{
    private function render(Request $request, Response $response, string $template): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, $template, [
            'user' => $request->getAttribute('user'),
        ]);
    }

    public function index(Request $request, Response $response): Response
    {
        return $this->render($request, $response, 'acceuil.html.twig');
    }

    public function pageOffres(Request $request, Response $response): Response
    {
        return $this->render($request, $response, 'page_offres.html.twig');
    }

    public function pageEntreprise(Request $request, Response $response): Response
    {
        return $this->render($request, $response, 'page_entreprise.html.twig');
    }

    public function mentions(Request $request, Response $response): Response
    {
        return $this->render($request, $response, 'Mentions.html.twig');
    }

    public function contact(Request $request, Response $response): Response
    {
        return $this->render($request, $response, 'Contact.html.twig');
    }

    public function monEspace(Request $request, Response $response): Response
    {
        return $this->render($request, $response, 'monespace.html.twig');
    }
}