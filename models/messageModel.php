<?php

class MessageModel implements JsonSerializable
{

    private $id;
    private $sender_id;
    private $conversation_id;
    private $content;
    private $created_at;
    private $read_at;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        return $this->id = $id;
    }

    public function getSenderId()
    {
        return $this->sender_id;
    }

    public function setSenderId($sender_id)
    {
        return $this->sender_id = $sender_id;
    }

    public function getConversationId()
    {
        return $this->conversation_id;
    }

    public function setConversationId($conversation_id)
    {
        return $this->conversation_id = $conversation_id;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        return $this->content = $content;
    }

    public function getCreatedAt($created_at)
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at)
    {
        return $this->created_at = $created_at;
    }

    public function getReadAt($read_at)
    {
        return $this->read_at;
    }

    public function setReadAt($read_at)
    {
        return $this->read_at = $read_at;
    }

    //Création d'un tableau associatif pour la conversion en JSON
    //json_encode ne sait pas comment convertir un objet en JSON
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'conversation_id' => $this->conversation_id,
            'content' => $this->content,
            'created_at' => $this->created_at,
            'read_at' => $this->read_at
        ];
    }
}
