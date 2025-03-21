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
$conn->close();
?>

<!DOCTYPE html>
    <html lang="it">
    <head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="Portale dedicato all'agenzia O.V.M.S. per gli eventi di Omega">
        <meta name="keywords" content="OVMS, O.V.M.S., ovms">
        <meta name="author" content="Emanuele Izzo">
        
        <title>Sodalitas Quaerito</title>

        <!-- Open Graph per condivisione social -->
        <meta property="og:title" content="Sodalitas Quaerito">
        <meta property="og:description" content="Portale dedicato all'agenzia O.V.M.S. per gli eventi di Omega">
        <meta property="og:image" content="URL dell'immagine di anteprima">
        <meta property="og:url" content="http://www.ovms-portal.com">
        <meta property="og:type" content="website">

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="favicon.png">
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>

        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar vh-100">
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
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 d-flex-inline align-items-center justify-content-center position-relative my-5">
                    <h1>Pagina di amministrazione</h1>

                    <div class="d-flex justify-content-center gap-4 my-4">
                        <a href="gestione_utenti" class="btn btn-page btn-lg px-5 py-3">Gestione Utenti</a>
                        <a href="gestione_articoli" class="btn btn-page btn-lg px-5 py-3">Gestione Articoli</a>
                        <a href="gestione_report" class="btn btn-page btn-lg px-5 py-3">Gestione Report</a>
                    </div>  

                    <a href="index" class="btn btn-secondary">Torna alla pagina principale</a>
                </main>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>