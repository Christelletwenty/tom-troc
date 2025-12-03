<?php
require_once '../models/livreModel.php';

class LivreManager {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }

    //Récupérer tous les livres un nombre par user
    public function countBooksByUserId(int $userId): int {

    $countBooksByUserIdRequest = 'SELECT COUNT(*) FROM livre WHERE user_id = :user_id';

    $countBooksByUserIdStmt = $this->db->prepare($countBooksByUserIdRequest);
    $countBooksByUserIdStmt->execute([
        'user_id' => $userId,
    ]);

    return (int) $countBooksByUserIdStmt->fetchColumn();
}


    //Récupérer des livres par userId
    public function getBooksByUserId(int $userId) {

        $getBooksRequest = 'SELECT * FROM livre WHERE user_id = :user_id';

        $getBooksStmt = $this->db->prepare($getBooksRequest);
        $getBooksStmt->setFetchMode(PDO::FETCH_CLASS, 'Livre');
        $getBooksStmt->execute( [
            'user_id' => $userId
        ] );

        return $getBooksStmt->fetchAll();

    }

    //Récupérer des livres par userId
    public function getBookById(int $id) {

        $getBooksRequest = '
            SELECT
                l.id        AS id,
                l.titre     AS titre,
                l.auteur    AS auteur,
                l.image     AS image,
                l.description AS description,
                l.dispo     AS dispo,
                l.user_id   AS user_id,
                u.username  AS user_name
            FROM livre l
            JOIN user u ON l.user_id = u.id
            WHERE l.id = :id
        ';

        $getBooksStmt = $this->db->prepare($getBooksRequest);
        $getBooksStmt->setFetchMode(PDO::FETCH_CLASS, 'Livre');
        $getBooksStmt->execute( [
            'id' => $id
        ] );

        return $getBooksStmt->fetch();

    }

    // Récupérer tous les livres
    public function findAllBooks() {

        $findAllBooksRequest = '
            SELECT
                l.id        AS id,
                l.titre     AS titre,
                l.auteur    AS auteur,
                l.image     AS image,
                l.description AS description,
                l.dispo     AS dispo,
                l.user_id   AS user_id,
                u.username  AS user_name
            FROM livre l
            JOIN user u ON l.user_id = u.id
            WHERE l.dispo = 1
        ';

        $findAllBooksStmt = $this->db->prepare($findAllBooksRequest);
        $findAllBooksStmt->setFetchMode(PDO::FETCH_CLASS, 'Livre');
        $findAllBooksStmt->execute();

        return $findAllBooksStmt->fetchAll();
    }


    //Créer un livre : passer le currentUserId en paramètre (souvent venant de $_SESSION['user_id']).
    public function createBook(Livre $livre) {

        $createBookRequest = 'INSERT INTO livre (titre, auteur, image, description, dispo, user_id) VALUES (:titre, :auteur, :image, :description, :dispo, :user_id)';

        $createBookStmt = $this->db->prepare($createBookRequest);
        $createBookStmt->execute ([
            'titre' => $livre->getTitre(),
            'auteur' => $livre->getAuteur(),
            'image' => $livre->getImage(),
            'description' => $livre->getDescription(),
            'dispo' => $livre->getDispo(),
            'user_id' => $_SESSION['currentUserId']
        ]);
    }


    //Supprimer un livre : passer le currentUserId en paramètre (venant de $_SESSION['user_id']).
    public function deleteBook(int $id) {

        $deleteBookRequest = 'DELETE FROM livre WHERE id = :id AND user_id = :user_id';

        $deleteBookStmt = $this->db->prepare($deleteBookRequest);
        $deleteBookStmt->execute ([
            'id' => $id,
            'user_id' => $_SESSION['currentUserId']
        ]);
    }

    //Mettre à jour un livre
    public function updateBook(Livre $livre) {

        $updateBookRequest = 'UPDATE livre SET titre = :titre, auteur = :auteur, image = :image, description = :description, dispo = :dispo WHERE id = :id AND user_id = :user_id';

        $udpateBookStmt = $this->db->prepare($updateBookRequest);
        $updateBookStmt->execute ([
            'titre' => $livre->getTitre(),
            'auteur' => $livre->getAuteur(),
            'image' => $livre->getImage(),
            'description' => $livre->getDescription(),
            'dispo' => $livre->getDispo(),
            'id' => $livre->getId(),
            'user_id' => $_SESSION['currentUserId']
        ]);
    }
}
?>