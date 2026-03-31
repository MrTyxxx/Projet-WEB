<?php
namespace App\Controller;

use App\Domain\Campus;
use App\Domain\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class UserController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function mesInformations(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'mesinformations.html.twig', [
            'user'   => $request->getAttribute('user'),
            'active' => 'profil',
        ]);
    }

    public function gestionEtudiants(Request $request, Response $response): Response
    {
        $user   = $request->getAttribute('user');
        $params = $request->getQueryParams();

        $searchNom    = $params['nom']    ?? '';
        $searchCampus = $params['campus'] ?? '';
        $page         = max(1, (int)($params['page'] ?? 1));
        $limit        = 10;

        $repo     = $this->entityManager->getRepository(Utilisateur::class);
        $criteria = ['role' => 'etudiant'];

        // Pilote : uniquement les étudiants de son campus
        if ($user->getRole() === 'pilote') {
            $criteria['campus'] = $user->getCampus();
        }

        // Admin avec filtre campus
        if ($user->getRole() === 'admin' && $searchCampus !== '') {
            $campus = $this->entityManager->getRepository(Campus::class)
                          ->findOneBy(['ville' => $searchCampus]);
            if ($campus) {
                $criteria['campus'] = $campus;
            }
        }

        $tous = $repo->findBy($criteria);

        // Filtre par nom
        if ($searchNom !== '') {
            $tous = array_values(array_filter($tous, function($e) use ($searchNom) {
                return str_contains(strtolower($e->getNom()), strtolower($searchNom))
                    || str_contains(strtolower($e->getPrenom()), strtolower($searchNom));
            }));
        }

        $total     = count($tous);
        $pages     = max(1, (int)ceil($total / $limit));
        $page      = min($page, $pages);
        $etudiants = array_slice($tous, ($page - 1) * $limit, $limit);
        $campuses  = $this->entityManager->getRepository(Campus::class)->findAll();

        $view = Twig::fromRequest($request);
        return $view->render($response, 'gestion-etudiants.html.twig', [
            'user'         => $user,
            'active'       => 'etudiants',
            'etudiants'    => $etudiants,
            'campuses'     => $campuses,
            'searchNom'    => $searchNom,
            'searchCampus' => $searchCampus,
            'page'         => $page,
            'pages'        => $pages,
        ]);
    }

    public function gestionPilotes(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'gestion-pilotes.html.twig', [
            'user'   => $request->getAttribute('user'),
            'active' => 'pilotes',
        ]);
    }

    public function creerCompte(Request $request, Response $response): Response
    {
        $uri  = $request->getUri()->getPath();
        $type = str_contains($uri, 'pilotes') ? 'pilote' : 'etudiant';

        $view = Twig::fromRequest($request);
        return $view->render($response, 'creer-compte.html.twig', [
            'user'   => $request->getAttribute('user'),
            'active' => $type === 'pilote' ? 'pilotes' : 'etudiants',
            'type'   => $type,
        ]);
    }
}