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
    // récupère le chiffre de l'URL
    $params = $request->getQueryParams();
    $debut = $params['offset'] ?? 0;

    // on prend 6 offres en commençant au chiffre du  debut
    $offres = $this->em->getRepository(Offrestage::class)->findBy([], null, 6, $debut);

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
        'user'      => $user,
        'mes_likes' => $mesLikes
    ]);
}
}