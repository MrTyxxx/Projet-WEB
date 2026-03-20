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
    public function gestionCandidatures(Request $request, Response $response): Response
{
    if (!isset($_SESSION['user'])) {
        return $response->withHeader('Location', '/Connexion')->withStatus(302);
    }

    $roles = $_SESSION['user']['roles'];
    $template = in_array('ROLE_ETUDIANT', $roles) 
        ? 'mes-candidatures.html.twig' 
        : 'gestion-candidatures.html.twig';

    $html = $this->twig->render($template, [
        'user'   => $_SESSION['user'],
        'active' => 'candidatures',
    ]);
    $response->getBody()->write($html);
    return $response;
}
public function gestionEntreprises(Request $request, Response $response): Response
{
    if (!isset($_SESSION['user'])) {
        return $response->withHeader('Location', '/Connexion')->withStatus(302);
    }

    $html = $this->twig->render('gestion-entreprises.html.twig', [
        'user'   => $_SESSION['user'],
        'active' => 'entreprises',
    ]);
    $response->getBody()->write($html);
    return $response;
}
public function gestionOffres(Request $request, Response $response): Response
{
    if (!isset($_SESSION['user'])) {
        return $response->withHeader('Location', '/Connexion')->withStatus(302);
    }

    $html = $this->twig->render('gestion-offres.html.twig', [
        'user'   => $_SESSION['user'],
        'active' => 'offres',
    ]);
    $response->getBody()->write($html);
    return $response;
}
public function creerEntrepriseForm(Request $request, Response $response): Response
{
    if (!isset($_SESSION['user'])) {
        return $response->withHeader('Location', '/Connexion')->withStatus(302);
    }

    $html = $this->twig->render('creer-entreprises.html.twig', [
        'user'   => $_SESSION['user'],
        'active' => 'entreprises',
    ]);
    $response->getBody()->write($html);
    return $response;
}

public function creerOffreForm(Request $request, Response $response): Response
{
    if (!isset($_SESSION['user'])) {
        return $response->withHeader('Location', '/Connexion')->withStatus(302);
    }

    $html = $this->twig->render('creer-offre.html.twig', [
        'user'   => $_SESSION['user'],
        'active' => 'offres',
    ]);
    $response->getBody()->write($html);
    return $response;
}
}