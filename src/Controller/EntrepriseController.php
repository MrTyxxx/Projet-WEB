<?php
namespace App\Controller;

use App\Domain\Campus;
use App\Domain\Evaluation;
use App\Domain\Entreprise;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class EntrepriseController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

public function pageEntreprises(Request $request, Response $response): Response
{
    $params        = $request->getQueryParams();
    $searchNom     = $params['nom']     ?? '';
    $searchSecteur = $params['secteur'] ?? '';
    $page          = max(1, (int)($params['page'] ?? 1));
    $limit         = 6;

    $toutes = $this->entityManager->getRepository(Entreprise::class)
                   ->findBy([], ['id_entreprise' => 'ASC']);

    if ($searchNom !== '') {
        $toutes = array_values(array_filter($toutes, function($e) use ($searchNom) {
            return str_contains(strtolower($e->getNom()), strtolower($searchNom));
        }));
    }

    if ($searchSecteur !== '') {
        $toutes = array_values(array_filter($toutes, function($e) use ($searchSecteur) {
            return str_contains(strtolower($e->getSecteur()), strtolower($searchSecteur));
        }));
    }

    $total       = count($toutes);
    $pages       = max(1, (int)ceil($total / $limit));
    $page        = min($page, $pages);
    $entreprises = array_slice($toutes, ($page - 1) * $limit, $limit);

    return Twig::fromRequest($request)->render($response, 'page_entreprise.html.twig', [
        'entreprises'   => $entreprises,
        'page'          => $page,
        'pages'         => $pages,
        'searchNom'     => $searchNom,
        'searchSecteur' => $searchSecteur,
    ]);
}

    public function showEntreprise(Request $request, Response $response, array $args): Response
    {
        $entreprise = $this->entityManager->getRepository(Entreprise::class)->find($args['id']);

        if (!$entreprise) {
            $response->getBody()->write("Entreprise introuvable");
            return $response->withStatus(404);
        }

        return Twig::fromRequest($request)->render($response, 'Entreprise1.html.twig', [
            'entreprise' => $entreprise,
            'user'       => $request->getAttribute('user'),
        ]);
    }

    public function gestionEntreprises(Request $request, Response $response): Response
    {
        $user   = $request->getAttribute('user');
        $params = $request->getQueryParams();

        $searchNom     = $params['nom'] ?? '';
        $searchSecteur = $params['secteur'] ?? '';
        $page          = max(1, (int)($params['page'] ?? 1));
        $limit         = 10;

        $toutes = $this->entityManager->getRepository(Entreprise::class)->findAll();

        if ($searchNom !== '') {
            $toutes = array_values(array_filter($toutes, function($e) use ($searchNom) {
                return str_contains(strtolower($e->getNom()), strtolower($searchNom));
            }));
        }

        if ($searchSecteur !== '') {
            $toutes = array_values(array_filter($toutes, function($e) use ($searchSecteur) {
                return str_contains(strtolower($e->getSecteur()), strtolower($searchSecteur));
            }));
        }

        $total       = count($toutes);
        $pages       = max(1, (int)ceil($total / $limit));
        $page        = min($page, $pages);
        $entreprises = array_slice($toutes, ($page - 1) * $limit, $limit);
        $campuses    = $this->entityManager->getRepository(Campus::class)->findAll();
        $evalRepo = $this->entityManager->getRepository(Evaluation::class);
        $evaluationExistante = [];

        foreach ($entreprises as $e) {
            $evaluationExistante[$e->getIdEntreprise()] = $evalRepo->findOneBy(['utilisateur' => $user, 'entreprise' => $e
            ]);
        }


        return Twig::fromRequest($request)->render($response, 'gestion-entreprises.html.twig', [
            'user'          => $user,
            'active'        => 'entreprises',
            'entreprises'   => $entreprises,
            'campuses'      => $campuses,
            'searchNom'     => $searchNom,
            'searchSecteur' => $searchSecteur,
            'page'          => $page,
            'pages'         => $pages,
            'evaluationExistante'  => $evaluationExistante
        ]);
    }

    public function creerEntreprise(Request $request, Response $response): Response
    {
        $user     = $request->getAttribute('user');
        $campuses = $this->entityManager->getRepository(Campus::class)->findAll();
        $errors   = [];

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $nom         = trim($data['nom_entreprise'] ?? '');
            $secteur     = trim($data['secteur'] ?? '');
            $email       = trim($data['email_contact'] ?? '');
            $description = trim($data['description'] ?? '');
            $campusId    = $data['campus'] ?? null;

            $campus = $campusId
                ? $this->entityManager->getRepository(Campus::class)->find($campusId)
                : null;

            if ($nom === '' || $secteur === '' || $email === '' || !$campus) {
                $errors[] = 'Tous les champs obligatoires doivent être remplis.';
            }

            if (empty($errors)) {
                $nouvelleEntreprise = new Entreprise(
                    $nom,
                    $secteur,
                    $email,
                    $campus,
                    $description !== '' ? $description : null
                );

                $this->entityManager->persist($nouvelleEntreprise);
                $this->entityManager->flush();

                return $response->withHeader('Location', '/espace/entreprises')->withStatus(302);
            }
        }

        return Twig::fromRequest($request)->render($response, 'creer-entreprises.html.twig', [
            'user'     => $user,
            'active'   => 'entreprises',
            'campuses' => $campuses,
            'errors'   => $errors,
            'mode'     => 'creer',
        ]);
    }

    public function modifierEntreprise(Request $request, Response $response, array $args): Response
    {
        $user       = $request->getAttribute('user');
        $entreprise = $this->entityManager->getRepository(Entreprise::class)->find($args['id']);
        $campuses   = $this->entityManager->getRepository(Campus::class)->findAll();
        $errors     = [];

        if (!$entreprise) {
            return $response->withHeader('Location', '/espace/entreprises')->withStatus(302);
        }

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $entreprise->setNom(trim($data['nom_entreprise'] ?? ''));
            $entreprise->setSecteur(trim($data['secteur'] ?? ''));
            $entreprise->setEmail(trim($data['email_contact'] ?? ''));
            $entreprise->setDescription(trim($data['description'] ?? ''));

            $campusId = $data['campus'] ?? null;
            if ($campusId) {
                $campus = $this->entityManager->getRepository(Campus::class)->find($campusId);
                $entreprise->setCampus($campus);
            }

            $this->entityManager->flush();

            return $response->withHeader('Location', '/espace/entreprises')->withStatus(302);
        }

        return Twig::fromRequest($request)->render($response, 'creer-entreprises.html.twig', [
            'user'       => $user,
            'active'     => 'entreprises',
            'entreprise' => $entreprise,
            'campuses'   => $campuses,
            'errors'     => $errors,
            'mode'       => 'modifier',
        ]);
    }

    public function supprimerEntreprise(Request $request, Response $response, array $args): Response
    {
        $entreprise = $this->entityManager->getRepository(Entreprise::class)->find($args['id']);

        if ($entreprise) {
            $this->entityManager->remove($entreprise);
            $this->entityManager->flush();
        }

        return $response->withHeader('Location', '/espace/entreprises')->withStatus(302);
    }
}