<?php

    class User implements JsonSerializable {

        private $id;
        private $username;
        private $email;
        private $password;
        private $image;
        private $created_at;
        // TODO -> ajouter les champs de la DB manquant et lées mettre dans le JSON_SERIALIZABLE


        public function getId() {
            return $this->id;
        }

        public function setId($id) {
            return $this->id = $id;
        }

        public function getUsername() {
            return $this->username;
        }

        public function setUsername($username) {
            return $this->username = $username;
        }

        public function getEmail() {
            return $this->email;
        }

        public function setEmail($email) {
            return $this->email = $email;
        }

        public function getPassword() {
            return $this->password;
        }

        public function setPassword($password) {
            return $this->password = $password;
        }

        public function getImage() {
            return $this->image;
        }

        public function setImage($image) {
            return $this->image = $image;
        }

        public function getCreatedAt() {
            return $this->created_at;
        }

        public function setCreatedAt($created_at) {
            return $this->created_at = $created_at;
        }
        //Création d'un tableau associatif pour la conversion en JSON
        //json_encode ne sait pas comment convertir un objet en JSON
        public function jsonSerialize(): array {
            return [
                'id' => $this->id,
                'user_name' => $this->username,
                'email' => $this->email,
                'image' => $this->image,
                'created_at' => $this->created_at
            ];
        }
    }
?>