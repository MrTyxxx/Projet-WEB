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

        if ($user->getRole() === 'pilote') {
            $criteria['campus'] = $user->getCampus();
        }

        if ($user->getRole() === 'admin' && $searchCampus !== '') {
            $campus = $this->entityManager->getRepository(Campus::class)
                          ->findOneBy(['ville' => $searchCampus]);
            if ($campus) {
                $criteria['campus'] = $campus;
            }
        }

        $tous = $repo->findBy($criteria);

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
    $user   = $request->getAttribute('user');
    $params = $request->getQueryParams();

    $searchNom    = $params['nom']    ?? '';
    $searchCampus = $params['campus'] ?? '';
    $page         = max(1, (int)($params['page'] ?? 1));
    $limit        = 10;

    $repo     = $this->entityManager->getRepository(Utilisateur::class);
    $criteria = ['role' => 'pilote'];

    if ($user->getRole() === 'admin' && $searchCampus !== '') {
        $campus = $this->entityManager->getRepository(Campus::class)
                      ->findOneBy(['ville' => $searchCampus]);
        if ($campus) {
            $criteria['campus'] = $campus;
        }
    }

    $tous = $repo->findBy($criteria);

    if ($searchNom !== '') {
        $tous = array_values(array_filter($tous, function($p) use ($searchNom) {
            return str_contains(strtolower($p->getNom()), strtolower($searchNom))
                || str_contains(strtolower($p->getPrenom()), strtolower($searchNom));
        }));
    }

    $total    = count($tous);
    $pages    = max(1, (int)ceil($total / $limit));
    $page     = min($page, $pages);
    $pilotes  = array_slice($tous, ($page - 1) * $limit, $limit);
    $campuses = $this->entityManager->getRepository(Campus::class)->findAll();

    $view = Twig::fromRequest($request);
    return $view->render($response, 'gestion-pilotes.html.twig', [
        'user'         => $user,
        'active'       => 'pilotes',
        'pilotes'      => $pilotes,
        'campuses'     => $campuses,
        'searchNom'    => $searchNom,
        'searchCampus' => $searchCampus,
        'page'         => $page,
        'pages'        => $pages,
    ]);
}

    public function creerCompte(Request $request, Response $response): Response
    {
        $user     = $request->getAttribute('user');
        $uri      = $request->getUri()->getPath();
        $type     = str_contains($uri, 'pilotes') ? 'pilote' : 'etudiant';
        $campuses = $this->entityManager->getRepository(Campus::class)->findAll();
        $errors   = [];

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $nom        = strtoupper(trim($data['nom']        ?? ''));
            $prenom     = trim($data['prenom']                ?? '');
            $email      = trim($data['email']                 ?? '');
            $telephone  = trim($data['telephone']             ?? '');
            $motdepasse = trim($data['motdepasse']            ?? '');
            $campusId   = $data['campus']                     ?? null;

            $campus = $campusId
                ? $this->entityManager->getRepository(Campus::class)->find($campusId)
                : null;

            $nouveau = new Utilisateur($nom, $prenom, $email, $telephone, $motdepasse, $type, $campus);
            $this->entityManager->persist($nouveau);
            $this->entityManager->flush();

            $redirect = $type === 'pilote' ? '/espace/pilotes' : '/espace/etudiants';
            return $response->withHeader('Location', $redirect)->withStatus(302);
        }

        $view = Twig::fromRequest($request);
        return $view->render($response, 'creer-compte.html.twig', [
            'user'     => $user,
            'active'   => $type === 'pilote' ? 'pilotes' : 'etudiants',
            'type'     => $type,
            'campuses' => $campuses,
            'errors'   => $errors,
            'mode'     => 'creer',
        ]);
    }

    public function modifierCompte(Request $request, Response $response, array $args): Response
    {
        $user     = $request->getAttribute('user');
        $uri      = $request->getUri()->getPath();
        $type     = str_contains($uri, 'pilotes') ? 'pilote' : 'etudiant';
        $compte   = $this->entityManager->getRepository(Utilisateur::class)->find($args['id']);
        $campuses = $this->entityManager->getRepository(Campus::class)->findAll();
        $errors   = [];

        if (!$compte) {
            return $response->withHeader('Location', '/espace/etudiants')->withStatus(302);
        }

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $compte->setNom(strtoupper(trim($data['nom']         ?? '')));
            $compte->setPrenom(trim($data['prenom']              ?? ''));
            $compte->setEmail(trim($data['email']                ?? ''));
            $compte->setTelephone(trim($data['telephone']        ?? ''));

            if (!empty($data['motdepasse'])) {
                $compte->setMotdePasse($data['motdepasse']);
            }

            $campusId = $data['campus'] ?? null;
            if ($campusId) {
                $campus = $this->entityManager->getRepository(Campus::class)->find($campusId);
                $compte->setCampus($campus);
            } else {
                $compte->setCampus(null);
            }

            $this->entityManager->flush();

            $redirect = $type === 'pilote' ? '/espace/pilotes' : '/espace/etudiants';
            return $response->withHeader('Location', $redirect)->withStatus(302);
        }

        $view = Twig::fromRequest($request);
        return $view->render($response, 'creer-compte.html.twig', [
            'user'     => $user,
            'active'   => $type === 'pilote' ? 'pilotes' : 'etudiants',
            'type'     => $type,
            'compte'   => $compte,
            'campuses' => $campuses,
            'errors'   => $errors,
            'mode'     => 'modifier',
        ]);
    }

    public function supprimerCompte(Request $request, Response $response, array $args): Response
    {
        $uri     = $request->getUri()->getPath();
        $type    = str_contains($uri, 'pilotes') ? 'pilote' : 'etudiant';
        $compte  = $this->entityManager->getRepository(Utilisateur::class)->find($args['id']);

        if ($compte) {
            $this->entityManager->remove($compte);
            $this->entityManager->flush();
        }

        $redirect = $type === 'pilote' ? '/espace/pilotes' : '/espace/etudiants';
        return $response->withHeader('Location', $redirect)->withStatus(302);
    }
}