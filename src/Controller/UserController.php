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
            'user'   => $_SESSION['user'],
            'active' => 'profil',
        ]);
        $response->getBody()->write($html);
        return $response;
    }

    public function gestionEtudiants(Request $request, Response $response): Response
    {
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/Connexion')->withStatus(302);
        }

        $html = $this->twig->render('gestion-etudiants.html.twig', [
            'user'   => $_SESSION['user'],
            'active' => 'etudiants',
        ]);
        $response->getBody()->write($html);
        return $response;
    }

    public function gestionPilotes(Request $request, Response $response): Response
    {
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/Connexion')->withStatus(302);
        }

        $html = $this->twig->render('gestion-pilotes.html.twig', [
            'user'   => $_SESSION['user'],
            'active' => 'pilotes',
        ]);
        $response->getBody()->write($html);
        return $response;
    }

    public function creerCompte(Request $request, Response $response): Response
    {
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/Connexion')->withStatus(302);
        }

        $uri = $request->getUri()->getPath();
        $type = str_contains($uri, 'pilotes') ? 'pilote' : 'etudiant';

        $html = $this->twig->render('creer-compte.html.twig', [
            'user'   => $_SESSION['user'],
            'active' => $type === 'pilote' ? 'pilotes' : 'etudiants',
            'type'   => $type,
        ]);
        $response->getBody()->write($html);
        return $response;
    }
}