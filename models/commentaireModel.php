<?php

    class Commentaire implements JsonSerializable {

        private $id;
        private $contenu;
        private $created_at;
        private $user_id;
        private $livre_id;

        public function getId() {
            return $this->id;
        }

        public function setId($id) {
            return $this->id;
        }

        public function getContenu() {
            return $this->contenu;
        }

        public function setContenu($contenu) {
            return $this->contenu;
        }

        public function getCreatedAt() {
            return $this->created_at;
        }

        public function setCreatedAt($created_at) {
            return $this->created_at;
        }

        public function getUserId() {
            return $this->user_id;
        }

        public function setUserId($user_id) {
            return $this->user_id;
        }

        public function getLivreId() {
            return $this->livre_id;
        }

        public function setLivreId($livre_id) {
            return $this->livre_id;
        }

        //Création d'un tableau associatif pour la conversion en JSON
        //json_encode ne sait pas comment convertir un objet en JSON
        public function jsonSerialize(): array {
            return [
                'id' => $this->id,
                'contenu' => $this->contenu,
                'created_at' => $this->created_at,
                'user_id' => $this->user_id,
                'livre_id' => $this->livre_id
            ];
        }

    }
?>