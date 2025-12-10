<?php

class Livre implements JsonSerializable
{

    private $id;
    private $titre;
    private $auteur;
    private $image;
    private $description;
    private $dispo;
    private $user_id;
    private $user_name;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        return $this->id = $id;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function setTitre($titre)
    {
        return $this->titre = $titre;
    }

    public function getAuteur()
    {
        return $this->auteur;
    }

    public function setAuteur($auteur)
    {
        return $this->auteur = $auteur;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        return $this->image = $image;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        return $this->description = $description;
    }

    public function getDispo()
    {
        return $this->dispo;
    }

    public function setDispo($dispo)
    {
        return $this->dispo = $dispo;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id)
    {
        return $this->user_id = $user_id;
    }

    public function getUserName()
    {
        return $this->user_name;
    }

    public function setUserName($userName)
    {
        return $this->user_name = $userName;
    }

    //Création d'un tableau associatif pour la conversion en JSON
    //json_encode ne sait pas comment convertir un objet en JSON
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'auteur' => $this->auteur,
            'image' => $this->image,
            'description' => $this->description,
            'dispo' => $this->dispo,
            'user_id' => $this->user_id,
            'user_name' => $this->user_name
        ];
    }
}
