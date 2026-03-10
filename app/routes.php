<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

return function (App $app) {
    $loader = new FilesystemLoader(__DIR__ . '/../templates');
    $twig = new Environment($loader);

    $app->get('/', function (Request $request, Response $response) use ($twig) {
        $html = $twig->render('acceuil.html.twig');
        $response->getBody()->write($html);
        return $response;
    });

    $app->get('/offre/{id}', function (Request $request, Response $response, $args) use ($twig) {
        $html = $twig->render('offre1.html.twig', ['id' => $args['id']]);
        $response->getBody()->write($html);
        return $response;
    });

    $app->get('/page_offres', function (Request $request, Response $response) use ($twig) {
    $html = $twig->render('page_offres.html.twig');
    $response->getBody()->write($html);
    return $response;
});
    $app->get('/Mentions', function ($request, $response) use ($twig) {
    $html = $twig->render('Mentions.html.twig');
    $response->getBody()->write($html);
    return $response;
});

$app->get('/Contact', function ($request, $response) use ($twig) {
    $html = $twig->render('Contact.html.twig');
    $response->getBody()->write($html);
    return $response;
});
$app->get('/Connexion', function ($request, $response) use ($twig) {
    $html = $twig->render('Connexion.html.twig');
    $response->getBody()->write($html);
    return $response;
});

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });
};