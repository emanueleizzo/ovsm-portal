<?php
// Connessione al database
include('db_connection.php');

// Imposta il numero di articoli per pagina
$perPagina = 12;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $perPagina;

// Ottieni gli articoli dal database
$sql = "SELECT * FROM articoli LIMIT $perPagina OFFSET $offset";
$result = $conn->query($sql);
$articoli = [];
while ($row = $result->fetch_assoc()) {
    $articoli[] = $row;
}

// Calcola il numero di pagine
$sql = "SELECT COUNT(*) AS total FROM articoli";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total = $row['total'];
$paginas = ceil($total / $perPagina);

// Restituisci i dati in formato JSON
echo json_encode([
    'articoli' => $articoli,
    'pagina' => $pagina,
    'paginas' => $paginas
]);
?>