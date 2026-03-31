<?php
namespace App\Controller;

use App\Domain\Campus;
use App\Domain\Entreprise;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class EntrepriseController
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    // Page publique /page_entreprise (tu peux garder ton code précédent si tu veux)
    public function pageEntreprises(Request $request, Response $response): Response
    {
        $page   = $request->getQueryParams()['page'] ?? 1;
        $depart = ($page - 1) * 6;

        $entreprises = $this->em->getRepository(Entreprise::class)
                                ->findBy([], ['id_entreprise' => 'ASC'], 6, $depart);

        $pages = max(1, (int)ceil($this->em->getRepository(Entreprise::class)->count([]) / 6));

        return Twig::fromRequest($request)->render($response, 'page_entreprise.html.twig', [
            'entreprises' => $entreprises,
            'page'        => $page,
            'pages'       => $pages,
        ]);
    }

    // Page détail publique /entreprise/{id}
    public function showEntreprise(Request $request, Response $response, array $args): Response
    {
        $entreprise = $this->em->getRepository(Entreprise::class)->find($args['id']);

        if (!$entreprise) {
            $response->getBody()->write("Entreprise introuvable");
            return $response->withStatus(404);
        }

        return Twig::fromRequest($request)->render($response, 'Entreprise1.html.twig', [
            'entreprise' => $entreprise,
            'user'       => $request->getAttribute('user'),
        ]);
    }

    // Tableau admin des entreprises
    public function gestionEntreprises(Request $request, Response $response): Response
    {
        $user   = $request->getAttribute('user');
        $params = $request->getQueryParams();

        $searchNom    = $params['nom']    ?? '';
        $searchSecteur= $params['secteur']?? '';
        $page         = max(1, (int)($params['page'] ?? 1));
        $limit        = 10;

        // On récupère toutes les entreprises puis on filtre en PHP (comme pour les offres)
        $toutes = $this->em->getRepository(Entreprise::class)->findAll();

        if ($searchNom !== '') {
            $toutes = array_values(array_filter($toutes, function (Entreprise $e) use ($searchNom) {
                return str_contains(strtolower($e->getNom()), strtolower($searchNom));
            }));
        }

        if ($searchSecteur !== '') {
            $toutes = array_values(array_filter($toutes, function (Entreprise $e) use ($searchSecteur) {
                return str_contains(strtolower($e->getSecteur()), strtolower($searchSecteur));
            }));
        }

        $total  = count($toutes);
        $pages  = max(1, (int)ceil($total / $limit));
        $page   = min($page, $pages);
        $entreprises = array_slice($toutes, ($page - 1) * $limit, $limit);

        return Twig::fromRequest($request)->render($response, 'gestion-entreprises.html.twig', [
            'user'         => $user,
            'active'       => 'entreprises',
            'entreprises'  => $entreprises,
            'searchNom'    => $searchNom,
            'searchSecteur'=> $searchSecteur,
            'page'         => $page,
            'pages'        => $pages,
        ]);
    }

    // Supprimer une entreprise
    public function supprimerEntreprise(Request $request, Response $response, array $args): Response
    {
        $entreprise = $this->em->getRepository(Entreprise::class)->find($args['id']);

        if ($entreprise) {
            $this->em->remove($entreprise);
            $this->em->flush();
        }

        return $response->withHeader('Location', '/espace/entreprises')->withStatus(302);
    }

    // Créer une entreprise (GET = affiche le formulaire, POST = traite)
    public function creerEntrepriseForm(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('user');
        $campuses = $this->em->getRepository(Campus::class)->findAll();

        return Twig::fromRequest($request)->render($response, 'creer-entreprises.html.twig', [
            'user'     => $user,
            'active'   => 'entreprises',
            'campuses' => $campuses,
        ]);
    }

    public function creerEntreprise(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('user');
        $campuses = $this->em->getRepository(Campus::class)->findAll();
        $data = $request->getParsedBody();

        $nom    = trim($data['nom_entreprise'] ?? '');
        $secteur= trim($data['secteur']        ?? '');
        $email  = trim($data['email_contact']  ?? '');
        $desc   = trim($data['description']    ?? '');
        $idCampus = (int)($data['campus']      ?? 0);

        if ($nom === '' || $secteur === '' || $email === '' || $idCampus <= 0) {
            return Twig::fromRequest($request)->render($response->withStatus(400), 'creer-entreprises.html.twig', [
                'user'     => $user,
                'active'   => 'entreprises',
                'campuses' => $campuses,
                'error'    => 'Tous les champs obligatoires doivent être remplis.',
                'old'      => $data,
            ]);
        }

        $campus = $this->em->getRepository(Campus::class)->find($idCampus);
        if (!$campus) {
            return Twig::fromRequest($request)->render($response->withStatus(400), 'creer-entreprises.html.twig', [
                'user'     => $user,
                'active'   => 'entreprises',
                'campuses' => $campuses,
                'error'    => 'Campus introuvable.',
                'old'      => $data,
            ]);
        }

        $entreprise = new Entreprise(
            $nom,
            $secteur,
            $email,
            $campus,
            $desc !== '' ? $desc : null
        );

        $this->em->persist($entreprise);
        $this->em->flush();

        return $response->withHeader('Location', '/espace/entreprises')->withStatus(302);
    }
}