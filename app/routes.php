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

return function (App $app) {
    $loader = new FilesystemLoader(__DIR__ . '/../templates');
    $twig   = new Environment($loader);

    $home  = new HomeController($twig);
    $offre = new OffreController($twig);
    $auth  = new AuthController($twig);

    $app->get('/',            [$home,  'index']);
    $app->get('/page_offres', [$home,  'pageOffres']);
    $app->get('/Mentions',    [$home,  'mentions']);
    $app->get('/Contact',     [$home,  'contact']);
    $app->get('/offre/{id}',  [$offre, 'show']);
    $app->get('/Connexion',   [$auth,  'showLogin']);
    $app->post('/Connexion',  [$auth,  'login']);
    $app->get('/logout',      [$auth,  'logout']);

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });
};