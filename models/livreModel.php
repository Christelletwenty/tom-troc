<?php

    class Livre {

        private $id;
        private $titre;
        private $auteur;
        private $image;
        private $description;
        private $dispo;

        public function getId() {
            return $this-> id;
        } 

        public function setId($id) {
            return $this-> id;
        }

        public function getTitre() {
            return $this-> titre;
        }

        public function setTitre($titre) {
            return $this-> titre;
        }

        public function getAuteur() {
            return $this-> auteur;
        }

        public function setAuteur($auteur) {
            return $this-> auteur;
        }

        public function getImage() {
            return $this-> image;
        }

        public function setImage($image) {
            return $this-> image;
        }

        public function getDescription() {
            return $this-> description;
        }

        public function setDescription($description) {
            return $this-> description;
        }

        public function getDispo() {
            return $this-> dispo;
        }

        public function setDispo($dispo) {
            return $this-> dispo;
        }
    }

?>