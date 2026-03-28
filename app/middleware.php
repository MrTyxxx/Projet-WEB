<?php
declare(strict_types=1);

use App\Application\Middleware\SessionMiddleware;
use App\Application\Middleware\UserTwigMiddleware;
use Doctrine\ORM\EntityManager;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return function (App $app) {
    $twig = Twig::create(__DIR__ . '/../templates', ['cache' => false]);
    $app->add(TwigMiddleware::create($app, $twig));
    $app->add(new UserTwigMiddleware($twig, $app->getContainer()->get(EntityManager::class)));
    $app->add(SessionMiddleware::class);
};