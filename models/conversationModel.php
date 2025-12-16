<?php

class Conversation implements JsonSerializable
{
    private $id;
    private $created_at;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        return $this->id = $id;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at)
    {
        return $this->created_at = $created_at;
    }
    //Création d'un tableau associatif pour la conversion en JSON
    //json_encode ne sait pas comment convertir un objet en JSON
    public function jsonSerialize(): array
    {
        return [
            'id'         => $this->id,
            'created_at' => $this->created_at,
        ];
    }
}
