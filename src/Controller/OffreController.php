<?php
namespace App\Controller;

use App\Domain\Offrestage;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class OffreController
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function pageOffres(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $page   = max(1, (int)($params['page'] ?? 1));
        $limit  = 6;
        $offset = ($page - 1) * $limit;

        $total  = $this->em->getRepository(Offrestage::class)->count([]);
        $offres = $this->em->getRepository(Offrestage::class)->findBy([], ['id_offre' => 'ASC'], $limit, $offset);
        $pages  = (int)ceil($total / $limit);

        $view = Twig::fromRequest($request);
        return $view->render($response, 'page_offres.html.twig', [
            'offres' => $offres,
            'page'   => $page,
            'pages'  => $pages,
            'user'   => $request->getAttribute('user'),
        ]);
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $offre = $this->em->getRepository(Offrestage::class)->find($args['id']);
        $view  = Twig::fromRequest($request);
        return $view->render($response, 'offre1.html.twig', [
            'offre' => $offre,
            'user'  => $request->getAttribute('user'),
        ]);
    }

    public function gestionOffres(Request $request, Response $response): Response
    {
        $offres = $this->em->getRepository(Offrestage::class)->findAll();
        $view   = Twig::fromRequest($request);
        return $view->render($response, 'gestion-offres.html.twig', [
            'offres' => $offres,
            'user'   => $request->getAttribute('user'),
            'active' => 'offres',
        ]);
    }

    public function creerOffreForm(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'creer-offre.html.twig', [
            'user'   => $request->getAttribute('user'),
            'active' => 'offres',
        ]);
    }
}