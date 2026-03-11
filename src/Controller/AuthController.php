<?php
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment;

class AuthController
{
    public function __construct(private Environment $twig) {}

    public function showLogin(Request $request, Response $response): Response
    {
        $html = $this->twig->render('Connexion.html.twig', [
            'user' => $_SESSION['user'] ?? null,
        ]);
        $response->getBody()->write($html);
        return $response;
    }

    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        // exemple statique
        if ($data['email'] === 'admin@test.com' && $data['password'] === 'admin') {
            $_SESSION['user'] = [
                'email' => $data['email'],
                'roles' => ['ROLE_ADMIN'],
            ];
            return $response->withHeader('Location', '/')->withStatus(302);
        }
        $html = $this->twig->render('Connexion.html.twig', [
            'error' => 'Identifiants incorrects',
            'user'  => null,
        ]);
        $response->getBody()->write($html);
        return $response;
    }

    public function logout(Request $request, Response $response): Response
    {
        unset($_SESSION['user']);
        return $response->withHeader('Location', '/')->withStatus(302);
    }
}