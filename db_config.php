<?php
// Informations de connection à la base de données
$servername = "localhost";
$username = "user";
$password = "passxord";
$dbname = "AssociaSite";

// Créer la connection
$connection = new mysqli($servername, $username, $password, $dbname);
$connection->set_charset("utf8");

// Vérifier la connection
if ($connection->connect_error) {
    die("connection échouée: " . $connection->connect_error);
}
?>
