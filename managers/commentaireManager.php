<?php
require_once '../models/commentaireModel.php';

class CommentaireManager {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }

    //Récupération des commentaires par livre
    public function getCommentsByBookId(int $livreId) {

        $getCommentsByBookRequest = 'SELECT * FROM commentaire WHERE livre_id = :livre_id';

        $getCommentsByBookStmt = $this->db->prepare($getCommentsByBookRequest);
        $getCommentsByBookStmt->setFetchMode(PDO::FETCH_CLASS, 'Commentaire');
        $getCommentsByBookStmt->execute([
            'livre_id' => $livreId
        ]);

        return $getCommentsByBookStmt->fetchAll();
    }

    //Créer un commentaire pour un livre
    public function createCommentByBook(Commentaire $commentaire) {

        $createCommentByBookRequest = 'INSERT INTO commentaire (contenu, created_at, user_id, livre_id) VALUES (:contenu, _created_at, :user_id, _livre_id)';

        $createCommentByBookStmt = $this->db-prepare($createCommentByBookRequest);
        $createCommentByBookStmt->execute ([
            'contenu' => $contenu->getContenu(),
            'created_at' => date('Y-m-d H:i:s'),
            'user_id' => $_SESSION['currentUserId'],
            'livre_id' => $livre_id->getLivreId()
        ]);
    }

    //Supprimer un commentaire pour un livre donné seulememt si c'est le user qui l'a crée 
    public function deleteCommentBook(int $id) {

        $deleteCommentBookRequest = 'DELETE FROM commentaire WHERE id = :id AND user_id = :user_id';

        $deleteCommentBookStmt = $this->db->prepare($deleteCommentBookRequest);
        $deleteCommentBookStmt->execute ([
            'id' => $id,
            'user_id' => $_SESSION['currentUserId']
        ]);
    }
}

?>