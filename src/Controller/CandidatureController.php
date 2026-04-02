<?php
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CandidatureController
{
    public function gestionCandidatures(Request $request, Response $response): Response
{
    $user = $request->getAttribute('user');

    if (!$user) {
        return $response->withHeader('Location', '/Connexion')->withStatus(302);
    }

    if ($user->getRole() === 'etudiant') {
        // candidatures de l'étudiant connecté
    }

    if ($user->getRole() === 'pilote' || $user->getRole() === 'admin') {
        // vue pilote/admin
    }

    return Twig::fromRequest($request)->render($response, 'mes-candidatures.html.twig', [
        'user'   => $user,
        'active' => 'candidatures',
    ]);
}
    
    public function formulaire(Request $request, Response $response, array $args): Response
    {
        $idOffre = $args['id'];

        return Twig::fromRequest($request)->render(
            $response,
            'candidature/formulaire.twig',
            [
                'offreId' => $idOffre,
            ]
        );
    }

}