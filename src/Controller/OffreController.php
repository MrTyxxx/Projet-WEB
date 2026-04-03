<?php
namespace App\Controller;

use App\Domain\Campus;
use App\Domain\Entreprise;
use App\Domain\Offrestage;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use PDO;

class OffreController {

    public function __construct(
        private EntityManagerInterface $em
    ) {}

   public function pageOffres(Request $request, Response $response): Response
{
    
    $params        = $request->getQueryParams();
    $searchTitre   = $params['titre']  ?? '';  
    $searchCampus  = $params['campus'] ?? ''; 
    $page          = max(1, (int)($params['page'] ?? 1)); 
    $limit         = 6; 

    // récupère toutes les offres de la base de données
    $toutes = $this->em->getRepository(Offrestage::class)->findAll();

    // filtre par titre si l'utilisateur a tapé quelque chose
    if ($searchTitre !== '') {
        $toutes = array_values(array_filter($toutes, function($o) use ($searchTitre) {
            return str_contains(strtolower($o->getTitre()), strtolower($searchTitre));
        }));
    }

    // filtre par campus si l'utilisateur en a sélectionné un
    // puis on filtre les offres dont le campus correspond
    if ($searchCampus !== '') {
        $campus = $this->em->getRepository(Campus::class)->findOneBy(['ville' => $searchCampus]);
        if ($campus) {
            $toutes = array_values(array_filter($toutes, function($o) use ($campus) {
                return $o->getCampus() && $o->getCampus()->getIdCampus() === $campus->getIdCampus();
            }));
        }
    }

    // calcule la pagination
    $total   = count($toutes);                              
    $pages   = max(1, (int)ceil($total / $limit));          
    $page    = min($page, $pages);                          
    $offres  = array_slice($toutes, ($page - 1) * $limit, $limit); 

    // récupère tous les campus pour le select du formulaire de recherche
    $campuses = $this->em->getRepository(Campus::class)->findAll();

    // récupère les offres likées par l'étudiant connecté (pour afficher le cœur rempli)
    $mesLikes = [];
    $user = $request->getAttribute('user');
    if ($user && $user->getRole() === 'etudiant') {
        $db   = new \PDO('mysql:host=db;dbname=yourjob;charset=utf8', 'root', 'root');
        $stmt = $db->prepare("SELECT id_offre FROM Wishlist WHERE id_utilisateur = ?");
        $stmt->execute([$user->getIdUtilisateur()]);
        $mesLikes = $stmt->fetchAll(PDO::FETCH_COLUMN); // tableau des id_offre likés
    }

    // envoie toutes les données au template Twig
     return Twig::fromRequest($request)->render($response, 'page_offres.html.twig', [
        'offres'        => $offres,        
        'user'          => $user,          
        'mes_likes'     => $mesLikes,      
        'campuses'      => $campuses,      
        'searchTitre'   => $searchTitre,   
        'searchCampus'  => $searchCampus,  
        'page'          => $page,          
        'pages'         => $pages,         
    ]);
}
   public function show(Request $request, Response $response, array $args): Response
{
    $offre = $this->em->getRepository(Offrestage::class)
        ->createQueryBuilder('o')
        ->leftJoin('o.entreprise', 'e')
        ->addSelect('e')
        ->where('o.id_offre = :id')
        ->setParameter('id', $args['id'])
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
      public function gestionOffres(Request $request, Response $response): Response
    {
        $user   = $request->getAttribute('user');
        $params = $request->getQueryParams();

        $searchTitre  = $params['titre']  ?? '';
        $searchCampus = $params['campus'] ?? '';
        $page         = max(1, (int)($params['page'] ?? 1));
        $limit        = 10;

        $criteria = [];

        if ($user->getRole() === 'pilote') {
            $criteria['campus'] = $user->getCampus();
        }

        if ($user->getRole() === 'admin' && $searchCampus !== '') {
            $campus = $this->em->getRepository(Campus::class)
                               ->findOneBy(['ville' => $searchCampus]);
            if ($campus) {
                $criteria['campus'] = $campus;
            }
        }

        $toutes = $this->em->getRepository(Offrestage::class)->findBy($criteria);

        if ($searchTitre !== '') {
            $toutes = array_values(array_filter($toutes, function($o) use ($searchTitre) {
                return str_contains(strtolower($o->getTitre()), strtolower($searchTitre));
            }));
        }

        $total    = count($toutes);
        $pages    = max(1, (int)ceil($total / $limit));
        $page     = min($page, $pages);
        $offres   = array_slice($toutes, ($page - 1) * $limit, $limit);
        $campuses = $this->em->getRepository(Campus::class)->findAll();

        return Twig::fromRequest($request)->render($response, 'gestion-offres.html.twig', [
            'user'         => $user,
            'active'       => 'offres',
            'offres'       => $offres,
            'campuses'     => $campuses,
            'searchTitre'  => $searchTitre,
            'searchCampus' => $searchCampus,
            'page'         => $page,
            'pages'        => $pages,
        ]);
    }
    public function supprimerOffre(Request $request, Response $response, array $args): Response
    {
    $offre = $this->em->getRepository(Offrestage::class)->find($args['id']);

    if ($offre) {
        $candidatures = $this->em->getRepository(\App\Domain\Candidature::class)
            ->findBy(['offre' => $offre]);

        foreach ($candidatures as $candidature) {
            $this->em->remove($candidature);
        }

        $this->em->remove($offre);
        $this->em->flush();
    }

    return $response->withHeader('Location', '/espace/offres')->withStatus(302);
    }

    public function modifierOffre(Request $request, Response $response, array $args): Response
    {
        $user     = $request->getAttribute('user');
        $offre    = $this->em->getRepository(Offrestage::class)->find($args['id']);
        $campuses = $this->em->getRepository(Campus::class)->findAll();
        $entreprises = $this->em->getRepository(Entreprise::class)->findAll();

        if (!$offre) {
            return $response->withHeader('Location', '/espace/offres')->withStatus(302);
        }

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $offre->setTitre(trim($data['titre']         ?? ''));
            $offre->setDescription(trim($data['description'] ?? ''));
            $offre->setRemuneration(trim($data['remuneration'] ?? ''));

            if (!empty($data['campus'])) {
                $campus = $this->em->getRepository(Campus::class)->find($data['campus']);
                $offre->setCampus($campus);
            }

            if (!empty($data['entreprise'])) {
                $entreprise = $this->em->getRepository(Entreprise::class)->find($data['entreprise']);
                $offre->setEntreprise($entreprise);
            }

            $this->em->flush();
            return $response->withHeader('Location', '/espace/offres')->withStatus(302);
        }

        return Twig::fromRequest($request)->render($response, 'creer-offre.html.twig', [
            'user'        => $user,
            'active'      => 'offres',
            'offre'       => $offre,
            'campuses'    => $campuses,
            'entreprises' => $entreprises,
            'mode'        => 'modifier',
        ]);
    }

    public function creerOffreForm(Request $request, Response $response): Response
    {
        $user        = $request->getAttribute('user');
        $campuses    = $this->em->getRepository(Campus::class)->findAll();
        $entreprises = $this->em->getRepository(Entreprise::class)->findAll();

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $campus     = $this->em->getRepository(Campus::class)->find($data['campus'] ?? 0);
            $entreprise = $this->em->getRepository(Entreprise::class)->find($data['entreprise'] ?? 0);

            $offre = new Offrestage(
                trim($data['titre']        ?? ''),
                trim($data['description']  ?? ''),
                trim($data['remuneration'] ?? ''),
                $entreprise,
                $campus,
            );

            $this->em->persist($offre);
            $this->em->flush();

            return $response->withHeader('Location', '/espace/offres')->withStatus(302);
        }

        return Twig::fromRequest($request)->render($response, 'creer-offre.html.twig', [
            'user'        => $user,
            'active'      => 'offres',
            'campuses'    => $campuses,
            'entreprises' => $entreprises,
            'mode'        => 'creer',
        ]);
    }

}
