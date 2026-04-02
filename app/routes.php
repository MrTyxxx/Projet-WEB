<?php
declare(strict_types=1);

use App\Application\Middleware\LoggedMiddleware;
use App\Controller\AuthController;
use App\Controller\CandidatureController;
use App\Controller\EntrepriseController;
use App\Controller\EvaluationController;
use App\Controller\HomeController;
use App\Controller\OffreController;
use App\Controller\UserController;
use App\Controller\WishlistController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });

    $factory = $app->getContainer()->get(ResponseFactoryInterface::class);

    // Routes publiques
    $app->get('/',                [HomeController::class,  'index']);
    $app->get('/page_offres',     [OffreController::class,  'pageOffres']);
    $app->get('/page_entreprise', [EntrepriseController::class, 'pageEntreprises']);
    $app->get('/Mentions',        [HomeController::class,  'mentions']);
    $app->get('/Contact',         [HomeController::class,  'contact']);
    $app->get('/offre/{id}',      [OffreController::class, 'show']);
    $app->get('/entreprise/{id}', [EntrepriseController::class, 'showEntreprise']);
    $app->post('/entreprise/{id}/noter', [EvaluationController::class, 'noterEntreprise']);
    $app->get('/Connexion',       [AuthController::class,  'showLogin']);
    $app->post('/Connexion',      [AuthController::class,  'login']);
    $app->get('/logout',          [AuthController::class,  'logout']);
    // Routes protégées
    $app->group('/espace', function ($group) {
        $group->get('',                    [HomeController::class,        'monEspace']);
        $group->get('/profil',             [UserController::class,        'mesInformations']);
        $group->get('/candidatures', [CandidatureController::class, 'gestionCandidatures']);

        $group->get('/etudiants',          [UserController::class,        'gestionEtudiants']);
        $group->get('/etudiants/creer',    [UserController::class,        'creerCompte']);
        $group->post('/etudiants/creer',   [UserController::class,        'creerCompte']);
        $group->get('/etudiants/{id}/modifier',  [UserController::class, 'modifierCompte']);
        $group->post('/etudiants/{id}/modifier', [UserController::class, 'modifierCompte']);
        $group->post('/etudiants/{id}/supprimer',[UserController::class, 'supprimerCompte']);

        $group->get('/pilotes',            [UserController::class,        'gestionPilotes']);
        $group->get('/pilotes/creer',      [UserController::class,        'creerCompte']);
        $group->post('/pilotes/creer',     [UserController::class,        'creerCompte']);
        $group->get('/pilotes/{id}/modifier',   [UserController::class, 'modifierCompte']);
        $group->post('/pilotes/{id}/modifier',  [UserController::class, 'modifierCompte']);
        $group->post('/pilotes/{id}/supprimer', [UserController::class, 'supprimerCompte']);

        $group->get('/entreprises',                [EntrepriseController::class, 'gestionEntreprises']);
        $group->get('/entreprises/creer',          [EntrepriseController::class, 'creerEntreprise']);
        $group->post('/entreprises/creer',         [EntrepriseController::class, 'creerEntreprise']);
        $group->get('/entreprises/{id}/modifier',  [EntrepriseController::class, 'modifierEntreprise']);
        $group->post('/entreprises/{id}/modifier',  [EntrepriseController::class, 'modifierEntreprise']);
        $group->post('/entreprises/{id}/supprimer',[EntrepriseController::class, 'supprimerEntreprise']);

        $group->get('/offres',             [OffreController::class,       'gestionOffres']);
        $group->get('/offres/creer',       [OffreController::class,       'creerOffreForm']);
        $group->post('/offres/creer',      [OffreController::class,       'creerOffreForm']);
        $group->get('/offres/{id}/modifier',   [OffreController::class, 'modifierOffre']);
        $group->post('/offres/{id}/modifier',  [OffreController::class, 'modifierOffre']);
        $group->post('/offres/{id}/supprimer', [OffreController::class, 'supprimerOffre']);
    })->add(new LoggedMiddleware($factory));

        $app->get('/wishlist',             [WishlistController::class, 'wishlist']);
        $app->get('/wishlist/add/{id}',    [WishlistController::class, 'add']);
        $app->get('/wishlist/delete/{id}', [WishlistController::class, 'delete']);
    

    $app->get('/offre/{id}/postuler',      [CandidatureController::class, 'formulaire']);
};