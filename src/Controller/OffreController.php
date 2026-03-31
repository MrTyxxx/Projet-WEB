<?php
namespace App\Controller;

use App\Domain\Offrestage;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use PDO;

class OffreController {
    private EntityManager $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

   public function pageOffres(Request $request, Response $response): Response
{
    // 1. On récupère le numéro de page (1 par défaut)
    $page = $request->getQueryParams()['page'] ?? 1;
    
    // 2. On définit manuellement le point de départ (0, 6, 12, 18...)
    // Si page 1 -> départ 0 | Si page 2 -> départ 6 | Si page 3 -> départ 12
    $depart = ($page - 1) * 6; 

    // 3. On demande à l'assistant ($em) de prendre 6 offres à partir du départ
    $offres = $this->em->getRepository(Offrestage::class)->findBy([], ['id_offre' => 'ASC'], 6, $depart);

    // 4. Bloc des cœurs (obligatoire pour que ça reste rouge)
    $mesLikes = [];
    $user = $request->getAttribute('user');
    if ($user && $user->getRole() === 'etudiant') {
        $db = new \PDO('mysql:host=db;dbname=yourjob;charset=utf8', 'root', 'root');
        $stmt = $db->prepare("SELECT id_offre FROM WISHLIST WHERE id_utilisateur = ?");
        $stmt->execute([$user->getIdUtilisateur()]);
        $mesLikes = $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    return Twig::fromRequest($request)->render($response, 'page_offres.html.twig', [
        'offres'    => $offres,
        'page'      => $page,
        'user'      => $user,
        'mes_likes' => $mesLikes
    ]);
}
}