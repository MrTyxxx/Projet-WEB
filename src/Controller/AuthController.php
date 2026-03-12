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

        $users = [
            'admin@test.com'    => ['password' => 'admin',    'roles' => ['ROLE_ADMIN']],
            'pilote@test.com'   => ['password' => 'pilote',   'roles' => ['ROLE_PILOTE']],
            'etudiant@test.com' => ['password' => 'etudiant', 'roles' => ['ROLE_ETUDIANT']],
        ];

        $email    = $data['email']    ?? '';
        $password = $data['password'] ?? '';

        if (isset($users[$email]) && $users[$email]['password'] === $password) {
            $_SESSION['user'] = [
                'email' => $email,
                'roles' => $users[$email]['roles'],
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
