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

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) use ($twig) {
        $html = $twig->render('index.html.twig');
        $response->getBody()->write($html);
        return $response;
    });
};