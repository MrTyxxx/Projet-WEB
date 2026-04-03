<?php

namespace App\Controller;

use App\Domain\Evaluation;
use App\Domain\Entreprise;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EvaluationController
{

    public function __construct(
        private EntityManagerInterface $entityManager
     ) {}

    public function noterEntreprise(Request $request, Response $response, array $args): Response
    {
        $user = $request->getAttribute('user');
        $idUser = $user->getIdUtilisateur();
        
        $idEntreprise = (int) $args['id'];
        $entreprise = $this->entityManager->getRepository(Entreprise::class)->find($idEntreprise);

        $body = $request->getParsedBody();
        $note = (int) ($body['note'] ?? 0);

        $evaluationExistante = $this->entityManager->getRepository(Evaluation::class)->findOneBy([
            'utilisateur' => $user,
            'entreprise'  => $entreprise
        ]);

    if ($evaluationExistante) {
        $evaluationExistante->setNote($note);
        $this->entityManager->flush();
    } else {
        $evaluation = new Evaluation($note, $user, $entreprise);
        $this->entityManager->persist($evaluation);
        $this->entityManager->flush();
    }


        return $response->withHeader('Location', "/entreprise/$idEntreprise")->withStatus(302);
    }
}