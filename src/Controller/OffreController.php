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
        $page = $request->getQueryParams()['page'] ?? 1;
        $depart = ($page - 1) * 6;

        $offres = $this->em->getRepository(Offrestage::class)
                           ->findBy([], ['id_offre' => 'ASC'], 6, $depart);

        $mesLikes = [];
        $user = $request->getAttribute('user');

        if ($user && $user->getRole() === 'etudiant') {
            $db = new \PDO('mysql:host=db;dbname=yourjob;charset=utf8', 'root', 'root');
            $stmt = $db->prepare("SELECT id_offre FROM WISHLIST WHERE id_utilisateur = ?");
            $stmt->execute([$user->getIdUtilisateur()]);
            $mesLikes = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        return Twig::fromRequest($request)->render($response, 'page_offres.html.twig', [
            'offres'    => $offres,
            'page'      => $page,
            'user'      => $user,
            'mes_likes' => $mesLikes
        ]);
    }  
    public function show(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];

        $offre = $this->em->getRepository(Offrestage::class)
            ->createQueryBuilder('o')
            ->leftJoin('o.entreprise', 'e')
            ->addSelect('e')
            ->where('o.id_offre = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$offre) {
            $response->getBody()->write("Offre introuvable");
            return $response->withStatus(404);
        }

        return Twig::fromRequest($request)->render($response, 'offre1.html.twig', [
            'offre'      => $offre,
            'entreprise' => $offre->getEntreprise(),
            'user'       => $request->getAttribute('user'),
        ]);
    }

}