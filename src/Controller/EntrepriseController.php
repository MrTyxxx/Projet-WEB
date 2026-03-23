<?php
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class EntrepriseController
{
    public function gestionEntreprises(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'gestion-entreprises.html.twig', [
            'user'   => $_SESSION['user'],
            'active' => 'entreprises',
        ]);
    }

    public function creerEntrepriseForm(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'creer-entreprises.html.twig', [
            'user'   => $_SESSION['user'],
            'active' => 'entreprises',
        ]);
    }
}