<?php
require_once '../models/livreModel.php';

class LivreManager {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }

    //Récupérer des livres par userId
    public function getBooksByUserId(int $userId) {

        $getBooksRequest = 'SELECT * FROM livre WHERE user_id = :user_id';

        $getBooksStmt = $this->db->prepare($getBooksRequest);
        $getBooksStmt->setFetchMode(PDO::FETCH_CLASS, 'Livre');
        $getBooksStmt->execute( [
            'user_id' => $userId
        ] );

        return $getBooksStmt->fetch();

    }

    //Récupérer tous les livres
    public function findAllBooks() {

        $findAllBooksRequest = 'SELECT * FROM livre';

        $findAllBooksStmt = $this->db->prepare($findAllBooksRequest);
        $findAllBooksStmt->setFetchMode(PDO::FETCH_CLASS, 'Livres');
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


    //Supprimer un livre : passer le currentUserId en paramètre (souvent venant de $_SESSION['user_id']).
    public function deleteBook(int $id) {

        $deleteBookRequest = 'DELETE FROM livre WHERE id = :id AND user_id = :user_id';

        $deleteBookStmt = $this->db->prepare($deleteBookRequest);
        $deleteBookStmt->execute ([
            'id' => $id,
            'user_id' => $_SESSION['currentUserId']
        ]);
    }

    //Mettre à jour un livre
    public function UpdateBook(Livre $livre) {

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