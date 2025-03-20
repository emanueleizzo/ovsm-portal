<?php
$servername = "localhost"; // O l'IP del server
$username = "user"; // Username del database
$password = ""; // Password del database
$dbname = "sodalidas_quaerito";

// Creazione della connessione
$conn = new mysqli($servername, $username, $password, $dbname);

// Controllo connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}
?>