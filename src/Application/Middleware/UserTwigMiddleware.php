<?php
namespace App\Application\Middleware;

use App\Domain\Utilisateur;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;

class UserTwigMiddleware
{
    private $twig;
    private EntityManager $em;

    public function __construct($twig, EntityManager $em)
    {
        $this->twig = $twig;
        $this->em   = $em;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $user_id = $request->getAttribute('session')['user_id'] ?? null;

        $user = null;
        if ($user_id) {
            $user = $this->em->getRepository(Utilisateur::class)->find($user_id);
        }

        $this->twig->getEnvironment()->addGlobal('user', $user);
        $request = $request->withAttribute('user', $user);

        return $handler->handle($request);
    }
}