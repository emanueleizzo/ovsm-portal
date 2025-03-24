<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit();
}

// Connessione al database
include 'db_connection.php';

// Recupero informazioni dell'utente dal database
$stmt = $conn->prepare("SELECT username, foto_profilo, nome, cognome, ruolo FROM utenti WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Variabile per i risultati della ricerca
$articoli = [];

// Se viene eseguita una ricerca
if (isset($_GET['search'])) {
    $keyword = "%" . $_GET['search'] . "%";

    $sql = "SELECT id, titolo, sinossi, testo, immagine,
            (CASE 
                WHEN titolo LIKE ? THEN 3
                WHEN sinossi LIKE ? THEN 2
                WHEN testo LIKE ? THEN 1
                ELSE 0 
            END) AS priorita
            FROM articoli
            WHERE titolo LIKE ? OR sinossi LIKE ? OR testo LIKE ?
            ORDER BY priorita DESC, id DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $keyword, $keyword, $keyword, $keyword, $keyword, $keyword);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $articoli[] = $row;
    }
    $stmt->close();
}
$conn->close();

include 'head.html';
?>
    <body>
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar">
                    <div class="text-center py-4">
                        <a href="index"><img src="images/logo.png" alt="Logo Agenzia" class="logo"><br></a>
                        <hr class="text-light">
                        <img src="<?php echo "uploads/".htmlspecialchars($user['foto_profilo']); ?>" alt="Profilo" class="img-fluid rounded-circle" style="width: 100px; height: 100px;">
                        <h5 class="text-light mt-2"><?php echo htmlspecialchars($user['nome'])." ".htmlspecialchars($user['cognome']); ?></h5>
                        <a href="profilo" class="btn btn-success btn-sm mt-2 w-100">Visualizza profilo</a>
                        <?php if ($user['ruolo'] === 'admin'): ?>
                            <a href="admin" class="btn btn-warning btn-sm mt-2 w-100">Amministrazione</a>
                        <?php endif; ?>
                        <hr class="text-light">
                        <a href="logout" class="btn btn-danger btn-sm mt-2 w-100">Logout</a>
                    </div>
                </nav>

                <!-- Contenuto principale -->
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 my-4">
                    <h1>Index</h1>

                    <!-- Barra di ricerca -->
                    <h2 class="mb-3">Ricerca Articoli</h2>
                    <form method="GET" class="mb-4">
                        <div class="">
                            <input type="text" name="search" class="form-control mb-3" placeholder="Cerca articoli..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button class="btn btn-primary" type="submit">Cerca</button>
                        </div>
                    </form>

                    <!-- Risultati della ricerca -->
                    <?php if (isset($_GET['search'])): ?>
                        <h2>Risultati per: <strong><?php echo htmlspecialchars($_GET['search']); ?></strong></h2>

                        <?php if (empty($articoli)): ?>
                            <p class="text-muted">Nessun articolo trovato.</p>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($articoli as $articolo): ?>
                                    <div class="col-md-3 mb-3">
                                        <div class="card align-items-center p-3 user-box">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo htmlspecialchars($articolo['titolo']); ?></h5>
                                                <p class="card-text"><?php echo htmlspecialchars($articolo['sinossi']); ?></p>
                                                <a href="visualizza_articolo?id=<?php echo $articolo["id"]; ?>" class="btn btn-primary">Visualizza</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                </main>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>