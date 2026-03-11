<?php
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment;

class HomeController
{
    public function __construct(private Environment $twig) {}

    public function index(Request $request, Response $response): Response
    {
        $html = $this->twig->render('acceuil.html.twig', [
            'user' => $_SESSION['user'] ?? null,
        ]);
        $response->getBody()->write($html);
        return $response;
    }

    public function pageOffres(Request $request, Response $response): Response
    {
        $html = $this->twig->render('page_offres.html.twig', [
            'user' => $_SESSION['user'] ?? null,
        ]);
        $response->getBody()->write($html);
        return $response;
    }

    public function mentions(Request $request, Response $response): Response
    {
        $html = $this->twig->render('Mentions.html.twig', [
            'user' => $_SESSION['user'] ?? null,
        ]);
        $response->getBody()->write($html);
        return $response;
    }

    public function contact(Request $request, Response $response): Response
    {
        $html = $this->twig->render('Contact.html.twig', [
            'user' => $_SESSION['user'] ?? null,
        ]);
        $response->getBody()->write($html);
        return $response;
    }
}