<?php
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class WishlistController
{
    public function wishlist(Request $request, Response $response, array $args): Response
    {
        $view = Twig::fromRequest($request);

        $user = $_SESSION['user'] ?? null;

        return $view->render($response, 'wishlist.html.twig', [
            'user'   => $user,
            'active' => 'offres-enregistrees',
        ]);
    }
}
