<?php
namespace App\Controller;

use App\Domain\Utilisateur;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class AuthController
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function showLogin(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'Connexion.html.twig', []);
    }

    public function login(Request $request, Response $response): Response
    {
        $data     = $request->getParsedBody();
        $email    = $data['email']    ?? '';
        $password = $data['password'] ?? '';

        $user = $this->em->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

        if ($user === null) {
            $view = Twig::fromRequest($request);
            return $view->render($response, 'Connexion.html.twig', [
                'error' => 'Email introuvable',
            ]);
        }

        if ($user->verifierMotdePasse($password)) {
            $_SESSION['user_id'] = $user->getIdUtilisateur();
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $view = Twig::fromRequest($request);
        return $view->render($response, 'Connexion.html.twig', [
            'error' => 'Mot de passe incorrect',
        ]);
    }

    public function logout(Request $request, Response $response): Response
    {
        unset($_SESSION['user_id']);
        return $response->withHeader('Location', '/')->withStatus(302);
    }
}