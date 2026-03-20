<?php

declare(strict_types=1);

use App\Controller\AuthController;
use App\Controller\HomeController;
use App\Controller\OffreController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Controller\UserController;
use App\Controller\WishlistController;

return function (App $app) {
    $loader = new FilesystemLoader(__DIR__ . '/../templates');
    $twig   = new Environment($loader);

    $home  = new HomeController($twig);
    $offre = new OffreController($twig);
    $auth  = new AuthController($twig);

    $app->get('/',            [$home,  'index']);
    $app->get('/page_offres', [$home,  'pageOffres']);
    $app->get('/page_entreprise', [$home,  'pageEntreprise']);
    $app->get('/Mentions',    [$home,  'mentions']);
    $app->get('/Contact',     [$home,  'contact']);
    $app->get('/offre/{id}',  [$offre, 'show']);
    $app->get('/Connexion',   [$auth,  'showLogin']);
    $app->post('/Connexion',  [$auth,  'login']);
    $app->get('/logout',      [$auth,  'logout']);
    $app->get('/espace', [$home, 'monEspace']);
 
    $user = new UserController($twig);
    $app->get('/espace/profil', [$user, 'mesInformations']);
    $app->get('/espace/etudiants', [$user, 'gestionEtudiants']);
    $app->get('/espace/pilotes',        [$user, 'gestionPilotes']);
    $app->get('/espace/pilotes/creer',  [$user, 'creerCompte']);
    $app->get('/espace/etudiants/creer',[$user, 'creerCompte']);
    $app->get('/espace/candidatures', [$user, 'gestionCandidatures']);
    $app->get('/espace/entreprises', [$user, 'gestionEntreprises']);
    $app->get('/espace/offres', [$user, 'gestionOffres']);
    $app->get('/espace/entreprises/creer', [$user, 'creerEntrepriseForm']);
    $app->post('/espace/entreprises/creer', [$user, 'creerEntrepriseForm']);
    $app->get('/espace/offres/creer', [$user, 'creerOffreForm']);
    $app->post('/espace/offres/creer', [$user, 'creerOffreForm']);

    $wishlist = new WishlistController();
    $app->get('/wishlist', [$wishlist, 'wishlist']);
    
    

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
     return $response;
    });
};