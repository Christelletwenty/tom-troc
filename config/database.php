<?php
require_once 'constants.php';

session_start();

//Connexion à la base de données
try {
    $db = new PDO('mysql:host=' . $databse_uri . ';dbname=' . $database_name . ';charset=utf8', $database_user, $database_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
