<?php
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use PDO;

class WishlistController {
    
   public function wishlist(Request $request, Response $response, array $args): Response {
    $user = $request->getAttribute('user');
    $db = new PDO('mysql:host=db;dbname=yourjob;charset=utf8', 'root', 'root');

    // ÉTAPE 1 : On récupère juste la liste des numéros d'offres likées
    $sql1 = "SELECT id_offre FROM WISHLIST WHERE id_utilisateur = ?";
    $stmt1 = $db->prepare($sql1);
    $stmt1->execute([$user->getIdUtilisateur()]);
    
    // FETCH_COLUMN permet d'avoir juste une liste de chiffres comme [1, 3, 5]
    $listeIds = $stmt1->fetchAll(PDO::FETCH_COLUMN);

    // ÉTAPE 2 : Si l'utilisateur a des likes, on va chercher les détails
    $mesOffres = [];
    if (!empty($listeIds)) {
        // implode transforme [1, 3, 5] en "1, 3, 5" pour le SQL
        $ids = implode(',', $listeIds);
        $sql2 = "SELECT * FROM offrestages WHERE id_offre IN ($ids)";
        $mesOffres = $db->query($sql2)->fetchAll(PDO::FETCH_ASSOC);
    }

    return Twig::fromRequest($request)->render($response, 'Wishlist.html.twig', [
        'offres' => $mesOffres,
        'user' => $user
    ]);
}
    // AJOUTER UN LIKE
    public function add(Request $request, Response $response, array $args): Response {
        $db = new PDO('mysql:host=db;dbname=yourjob;charset=utf8', 'root', 'root');
        $sql = "INSERT IGNORE INTO WISHLIST (id_utilisateur, id_offre, date_ajout) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$request->getAttribute('user')->getIdUtilisateur(), $args['id'], date('Y-m-d')]);

        return $response->withHeader('Location', $request->getHeaderLine('Referer'))->withStatus(302);
    }

    // SUPPRIMER UN LIKE
    public function delete(Request $request, Response $response, array $args): Response {
        $db = new PDO('mysql:host=db;dbname=yourjob;charset=utf8', 'root', 'root');
        $sql = "DELETE FROM WISHLIST WHERE id_utilisateur = ? AND id_offre = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$request->getAttribute('user')->getIdUtilisateur(), $args['id']]);

        return $response->withHeader('Location', $request->getHeaderLine('Referer'))->withStatus(302);
    }
}