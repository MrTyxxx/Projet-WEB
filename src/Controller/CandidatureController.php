<?php

namespace App\Controller;

use App\Domain\Candidature;
use Doctrine\ORM\EntityManagerInterface;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CandidatureController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function gestionCandidatures(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('user');

        if (!$user) {
            return $response->withHeader('Location', '/Connexion')->withStatus(302);
        }

        // ✅ Utiliser l’entityManager du constructeur
        $candidatures = $this->entityManager
            ->getRepository(Candidature::class)
            ->findBy(['utilisateur' => $user]);

        return Twig::fromRequest($request)->render($response, 'mes-candidatures.html.twig', [
            'user'         => $user,
            'candidatures' => $candidatures
        ]);
    }
}
