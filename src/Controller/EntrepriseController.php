<?php
namespace App\Controller;


use App\Domain\Entreprise;
use Doctrine\ORM\EntityManager; 
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class EntrepriseController
{

private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }



public function pageEntreprises(Request $request, Response $response): Response
    {
        // Récupération paramètres pagination
        $params = $request->getQueryParams();
        $page = max(1, (int)($params['page'] ?? 1));
        $limit = 6; // 👈 nombre d'entreprises par page
        $offset = ($page - 1) * $limit;

        // Total des entreprises
        $total = $this->em->getRepository(Entreprise::class)->count([]);

        // Entreprises pour cette page
        $entreprises = $this->em
            ->getRepository(Entreprise::class)
            ->findBy([], ['id_entreprise' => 'ASC'], $limit, $offset);

        // Nombre de pages
        $pages = (int) ceil($total / $limit);

        return Twig::fromRequest($request)->render($response, 'page_entreprise.html.twig', [
            'entreprises' => $entreprises,
            'page'        => $page,
            'pages'       => $pages
        ]);
    }

    
public function showEntreprise(Request $request, Response $response, array $args): Response
{
    $id = $args['id'];
    $entreprise = $this->em->getRepository(Entreprise::class)->find($id);
    // Si l'entreprise existe pas → 404
    if (!$entreprise) {
        $response->getBody()->write("Entreprise introuvable");
        return $response->withStatus(404);
    }

    // On rend la vue
    return Twig::fromRequest($request)->render($response, 'Entreprise1.html.twig', [
        'entreprise' => $entreprise,   // 👈 IMPORTANT
        'user'       => $request->getAttribute('user'),
    ]);
}

    public function gestionEntreprises(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'gestion-entreprises.html.twig', [
            'user'   => $request->getAttribute('user'),
            'active' => 'entreprises',
        ]);
    }

    public function creerEntrepriseForm(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'creer-entreprises.html.twig', [
            'user'   => $request->getAttribute('user'),
            'active' => 'entreprises',
        ]);
    }
}