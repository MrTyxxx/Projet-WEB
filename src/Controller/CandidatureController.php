<?php
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CandidatureController
{
    public function gestionCandidatures(Request $request, Response $response): Response
    {
        $roles    = $_SESSION['user']['roles'];
        $template = in_array('ROLE_ETUDIANT', $roles)
            ? 'mes-candidatures.html.twig'
            : 'gestion-candidatures.html.twig';

        $view = Twig::fromRequest($request);
        return $view->render($response, $template, [
            'user'   => $_SESSION['user'],
            'active' => 'candidatures',
        ]);
    }
}