<?php
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CampusController
{
    
public function gestionCampus(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);

        return $view->render($response, 'gestion-campus.html.twig', [
            'user' => $request->getAttribute('user'),
            'active' => 'campus',
        ]);

        }
}