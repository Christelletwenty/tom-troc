<?php

    class User implements JsonSerializable {

        private $id;
        private $username;
        private $email;
        private $password;


        public function getId() {
            return $this->id;
        }

        public function setId($id) {
            return $this->id;
        }

        public function getUsername() {
            return $this->username;
        }

        public function setUsername($username) {
            return $this->username;
        }

        public function getEmail() {
            return $this->email;
        }

        public function setEmail($email) {
            return $this->email;
        }

        public function getPassword() {
            return $this->password;
        }

        public function setPassword($password) {
            return $this->password;
        }

        //Création d'un tableau associatif pour la conversion en JSON
        //json_encode ne sait pas comment convertir un objet en JSON
        public function jsonSerialize(): array {
            return [
                'id' => $this->id,
                'name' => $this->username
            ];
        }
    }
?>