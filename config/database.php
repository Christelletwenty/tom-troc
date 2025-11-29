<?php

session_start();

//Connexion à la base de données
try {
    $db = new PDO('mysql:host=localhost;dbname=tomtroc;charset=utf8', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>