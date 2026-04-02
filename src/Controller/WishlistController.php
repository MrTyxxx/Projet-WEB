<?php
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use PDO;

class WishlistController {
    private PDO $db;

    public function __construct() {
        
        $this->db = new PDO('mysql:host=db;dbname=yourjob;charset=utf8', 'root', 'root');
    }
    
    public function wishlist(Request $request, Response $response): Response {
        $user = $request->getAttribute('user');
        $idUser = $user->getIdUtilisateur();

        // Récupérer les IDs 
        $stmt = $this->db->prepare("SELECT id_offre FROM Wishlist WHERE id_utilisateur = ?");
        $stmt->execute([$idUser]);
        $listeIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $mesOffres = [];

        //  on cherche les détails
        if (!empty($listeIds)) {
            // "implode" pour transformer la liste en nombres
            $ids = implode(',', $listeIds);
            $stmtOffres = $this->db->query("SELECT * FROM offrestages WHERE id_offre IN ($ids)");
            $mesOffres = $stmtOffres->fetchAll(PDO::FETCH_ASSOC);
        }

        return Twig::fromRequest($request)->render($response, 'Wishlist.html.twig', [
            'offres' => $mesOffres,
            'user'   => $user
        ]);
    }

    public function add(Request $request, Response $response, array $args): Response {
        $stmt = $this->db->prepare("INSERT IGNORE INTO Wishlist (id_utilisateur, id_offre, date_ajout) VALUES (?, ?, ?)");
        $stmt->execute([$request->getAttribute('user')->getIdUtilisateur(), $args['id'], date('Y-m-d')]);

        return $response->withHeader('Location', $request->getHeaderLine('Referer'))->withStatus(302);
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $stmt = $this->db->prepare("DELETE FROM Wishlist WHERE id_utilisateur = ? AND id_offre = ?");
        $stmt->execute([$request->getAttribute('user')->getIdUtilisateur(), $args['id']]);

        return $response->withHeader('Location', $request->getHeaderLine('Referer'))->withStatus(302);
    }
}