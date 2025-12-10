<?php
require_once '../models/userModel.php';

class UserManager
{
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
    }

    //Récupération du user par username pour le login
    public function getUserByEmail(string $email)
    {
        $getEmailRequest = 'SELECT * FROM user WHERE email = :email';

        $getEmailStmt = $this->db->prepare($getEmailRequest);
        $getEmailStmt->setFetchMode(PDO::FETCH_CLASS, 'User');
        $getEmailStmt->execute([
            //strtolower me retourne une nouvelle variable mais en minuscule
            'email' => strtolower($email)
        ]);

        return $getEmailStmt->fetch();
    }

    //Récupération du user par username pour le login
    public function getUserByUsername(string $name)
    {
        $getUserRequest = 'SELECT * FROM user WHERE username = :name';

        $getUserlStmt = $this->db->prepare($getUserRequest);
        $getUserlStmt->setFetchMode(PDO::FETCH_CLASS, 'User');
        $getUserlStmt->execute([
            //strtolower me retourne une nouvelle variable mais en minuscule
            'name' => strtolower($name)
        ]);

        return $getUserlStmt->fetch();
    }

    //Récupération des users par id
    public function getUserById(int $id)
    {

        $getUserRequest = 'SELECT * FROM user WHERE id = :id';

        $getUserStmt = $this->db->prepare($getUserRequest);
        $getUserStmt->setFetchMode(PDO::FETCH_CLASS, 'User');
        $getUserStmt->execute([
            'id' => $id
        ]);

        return $getUserStmt->fetch();
    }

    //Récupération de tous les users
    public function findAll()
    {

        $findAllRequest = 'SELECT * FROM user';

        $findAllStmt = $this->db->prepare($findAllRequest);
        $findAllStmt->setFetchMode(PDO::FETCH_CLASS, 'User');
        $findAllStmt->execute();

        return $findAllStmt->fetchAll();
    }

    //Création d'un user
    public function createUser(User $user)
    {

        $createUserRequest = 'INSERT INTO user (username, email, password) VALUES (:username, :email, :password)';

        $createuserStmt = $this->db->prepare($createUserRequest);
        $createuserStmt->execute([
            'username' => strtolower($user->getUsername()),
            'email' => $user->getEmail(),
            //On encrypte le PWD avant de le stocker
            'password' => password_hash($user->getPassword(), PASSWORD_DEFAULT)
        ]);
    }

    // Update d'un profil user
    public function updateUser(User $user): void
    {
        // On prépare les paramètres communs
        $params = [
            'id'       => $user->getId(),
            'username' => strtolower($user->getUsername()),
            'email'    => $user->getEmail(),
        ];

        // Si un nouveau mot de passe est fourni → on le hash et on l'update
        if ($user->getPassword() !== null && $user->getPassword() !== '') {
            $sql = 'UPDATE user 
                    SET username = :username, email = :email, password = :password
                    WHERE id = :id';

            $params['password'] = password_hash($user->getPassword(), PASSWORD_DEFAULT);
        } else {
            // Sinon, on ne touche pas au mot de passe
            $sql = 'UPDATE user 
                    SET username = :username, email = :email
                    WHERE id = :id';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }

    //update image d'un user
    public function updateUserImage(int $userId, string $imagePath): void
    {
        $sql = 'UPDATE user SET image = :image WHERE id = :id';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'image' => $imagePath,
            'id'    => $userId,
        ]);
    }
}
