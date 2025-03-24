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

// Verifica che l'ID dell'articolo sia presente
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Articolo non trovato.");
}

$articolo_id = $_GET['id'];

// Recupera l'articolo dal database
$stmt = $conn->prepare("SELECT titolo, sinossi, testo, immagine FROM articoli WHERE id = ?");
$stmt->bind_param("i", $articolo_id);
$stmt->execute();
$result = $stmt->get_result();
$articolo = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Se l'articolo non esiste
if (!$articolo) {
    die("Articolo non trovato.");
}

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
                    <!-- Titolo e Sinossi -->
                    <h1 class="mb-3"><?php echo htmlspecialchars($articolo['titolo']); ?></h1>
                    <p class="text-muted"><?php echo htmlspecialchars($articolo['sinossi']); ?></p>

                    <div class="row mt-4">
                        <!-- Colonna immagine -->
                        <div class="col-md-4">
                            <?php if (!empty($articolo['immagine'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($articolo['immagine']); ?>" class="img-fluid rounded shadow-sm" alt="Immagine articolo">
                            <?php else: ?>
                                <p class="text-muted">Nessuna immagine disponibile</p>
                            <?php endif; ?>
                        </div>

                        <!-- Colonna testo con scroll -->
                        <div class="col-md-8">
                            <div class="scrollable-text">
                                <?php echo $articolo['testo']; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Pulsante indietro -->
                    <a href="index" class="btn btn-secondary mt-4">Torna indietro</a>

                </main>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>