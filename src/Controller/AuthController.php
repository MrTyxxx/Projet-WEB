<?php
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class AuthController
{

    public function showLogin(Request $request, Response $response): Response
    {
        $view = Twig ::fromRequest($request);
        return $view->render($response,'Connexion.html.twig', [
            'user' => $_SESSION['user'] ?? null,
        ]);
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
        $view = Twig :: fromRequest($request);
        return $view->render('Connexion.html.twig', [
            'error' => 'Identifiants incorrects',
            'user'  => null,
        ]);
       
    }

    public function logout(Request $request, Response $response): Response
    {
        unset($_SESSION['user']);
        return $response->withHeader('Location', '/')->withStatus(302);
    }
   
}
 