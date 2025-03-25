<?php
// Connessione al database
include('db_connection.php');

// Imposta il numero di articoli per pagina
$perPagina = 8;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $perPagina;

// Ottieni gli articoli dal database
$sql = "SELECT * FROM reports LIMIT $perPagina OFFSET $offset";
$result = $conn->query($sql);
$reports = [];
while ($row = $result->fetch_assoc()) {
    $reports[] = $row;
}

// Calcola il numero di pagine
$sql = "SELECT COUNT(*) AS total FROM utenti";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total = $row['total'];
$paginas = ceil($total / $perPagina);

// Restituisci i dati in formato JSON
echo json_encode([
    'reports' => $reports,
    'pagina' => $pagina,
    'paginas' => $paginas
]);
?>