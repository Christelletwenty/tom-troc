<?php
require_once '../models/userModel.php';

class UserManager {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }

    //Récupération du user par email pour le login
    public function getUserByUsername(string $email) {
        $getEmailRequest = 'SELECT * FROM user WHERE email = :email';

        $getEmailStmt = $this->db->prepare($getEmailRequest);
        $getEmailStmt->setFetchMode(PDO::FETCH_CLASS, 'User');
        $getEmailStmt->execute ([
            //strtolower me retourne une nouvelle variable mais en minuscule
            'email' => strtolower($email)
        ]);

        return $getEmailStmt->fetch();
    }

    //Récupération des users par id
    public function getUserById(int $id) {

        $getUserRequest = 'SELECT * FROM user WHERE id = :id';

        $getUserStmt = $this->db->prepare($getUserRequest);
        $getUserStmt->setFetchMode(PDO::FETCH_CLASS, 'User');
        $getUserStmt->execute( [
                'id' => $id
            ]);

        return $getUserStmt->fetch();
    }

    //Récupération de tous les users
    public function findAll() {

        $findAllRequest = 'SELECT * FROM user';

        $findAllStmt = $this->db->prepare($findAllRequest);
        $findAllStmt->setFetchMode(PDO::FETCH_CLASS, 'User');
        $findAllStmt->execute();

        return $findAllStmt->fetchAll();
    }

    //Création d'un user
    public function createUser(User $user) {

        $createUserRequest = 'INSERT INTO user (username, email, password) VALUES (:username, :email, :password)';

        $createuserStmt = $this->db->prepare($createUserRequest);
        $createuserStmt->execute ([
            'username' => strtolower($user->getUsername()),
            //On encrypte le PWD avant de le stocker
            'password' => password_hash($user->getPassword(), PASSWORD_DEFAULT)
        ]);
    }

    //Update d'un profoil user
    public function updateUser(User $user) {

        $updateUserRequest = 'UPDATE user SET username = :username, password = :password WHERE id = :id';

        $updateUserStmt = $this->db->prepare($updateUserRequest);
        $updateUserStmt->execute ([
            'username' => $user->getUsername(),
            'password' => $user->getPassword()
        ]);
    }
}

?>