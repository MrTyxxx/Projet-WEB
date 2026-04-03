<?php

namespace App\Controller;

use App\Domain\Candidature;
use App\Domain\Offrestage;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CandidatureController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function postuler(Request $request, Response $response, array $args): Response
    {
        $user = $request->getAttribute('user');
        $offre = $this->entityManager->getRepository(Offrestage::class)->find((int) $args['id']);

        $dejaPostule = $this->entityManager->getRepository(Candidature::class)->findOneBy([
            'utilisateur' => $user,
            'offre'       => $offre,
        ]);

        if (!$dejaPostule) {
            $candidature = new Candidature($user, $offre);
            $this->entityManager->persist($candidature);
            $this->entityManager->flush();
        }

        return $response->withHeader('Location', '/offre/' . $args['id'])->withStatus(302);
    }

    public function gestionCandidatures(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('user');

        $candidatures = $this->entityManager->getRepository(Candidature::class)
            ->findBy(['utilisateur' => $user]);

        return Twig::fromRequest($request)->render($response, 'mes-candidatures.html.twig', [
            'user'         => $user,
            'candidatures' => $candidatures,
        ]);
    }
}