<?php
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment;

class UserController
{
    public function __construct(private Environment $twig) {}

    public function mesInformations(Request $request, Response $response): Response
    {
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/Connexion')->withStatus(302);
        }

        $html = $this->twig->render('mesinformations.html.twig', [
            'user' => $_SESSION['user'],
        ]);
        $response->getBody()->write($html);
        return $response;
    }
}