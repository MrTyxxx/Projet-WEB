<?php
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class UserController
{
    public function mesInformations(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'mesinformations.html.twig', [
            'user'   => $request->getAttribute('user'),
            'active' => 'profil',
        ]);
    }

    public function gestionEtudiants(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'gestion-etudiants.html.twig', [
            'user'   => $request->getAttribute('user'),
            'active' => 'etudiants',
        ]);
    }

    public function gestionPilotes(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'gestion-pilotes.html.twig', [
            'user'   => $request->getAttribute('user'),
            'active' => 'pilotes',
        ]);
    }

    public function creerCompte(Request $request, Response $response): Response
    {
        $uri  = $request->getUri()->getPath();
        $type = str_contains($uri, 'pilotes') ? 'pilote' : 'etudiant';

        $view = Twig::fromRequest($request);
        return $view->render($response, 'creer-compte.html.twig', [
            'user'   => $request->getAttribute('user'),
            'active' => $type === 'pilote' ? 'pilotes' : 'etudiants',
            'type'   => $type,
        ]);
    }
}