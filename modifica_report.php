<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit();
}

// Connessione al database
include 'db_connection.php';

// Verifica ruolo utente
$stmt = $conn->prepare("SELECT username, foto_profilo, nome, cognome, ruolo FROM utenti WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user['ruolo'] !== 'admin') {
    header("Location: index");
    exit();
}

// Recupero ID articolo dall'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: gestione_articoli");
    exit();
}
$articolo_id = $_GET['id'];

// Recupero dati dell'articolo
$stmt = $conn->prepare("SELECT titolo, sinossi, testo, immagine FROM reports WHERE id = ?");
$stmt->bind_param("i", $articolo_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: profilo");
    exit();
}

$report = $result->fetch_assoc();

// Gestione della modifica dell'articolo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['modifica'])) {
    $titolo = $_POST['titolo'];
    $sinossi = $_POST['sinossi'];
    $testo = $_POST['testo'];
    $immagine = $report['immagine'];

    // Gestione upload immagine
    if (!empty($_FILES["immagine"]["name"])) {
        $target_dir = "uploads/"; // Cartella dove salvare le immagini
        $file_extension = strtolower(pathinfo($_FILES["immagine"]["name"], PATHINFO_EXTENSION));
        $target_file = $target_dir . $_FILES["immagine"]["name"];

        // Controllo validità del file (tipo e dimensione)
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_extension, $allowed_extensions)) {
            die("Errore: formato immagine non valido.");
        }

        if ($_FILES["immagine"]["size"] > 5000000) { // 5MB limite
            die("Errore: immagine troppo grande.");
        }

        // Salva il file nella cartella 'uploads'
        if (move_uploaded_file($_FILES["immagine"]["tmp_name"], $target_file)) {
            echo "Immagine caricata con successo.";
            $immagine = $_FILES["immagine"]["name"];
        } else {
            die("Errore nel caricamento dell'immagine.");
        }
    }

    // Aggiornamento articolo nel database
    $sql = "UPDATE articoli SET titolo = ?, sinossi = ?, testo = ?, immagine = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $titolo, $sinossi, $testo, $immagine, $articolo_id);

    if ($stmt->execute()) {
        header("Location: profilo?modifica=success");
        exit();
    } else {
        echo "Errore nella modifica dell'articolo.";
    }
}

// Gestione eliminazione articolo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['elimina'])) {
    $stmt = $conn->prepare("DELETE FROM articoli WHERE id = ?");
    $stmt->bind_param("i", $articolo_id);

    if ($stmt->execute()) {
        header("Location: profilo?eliminato=success");
        exit();
    } else {
        echo "Errore nella cancellazione dell'articolo.";
    }
}

$stmt->close();
$conn->close();

include 'head.html';
?>
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
                    <div class="container">
                        <h1 class="mb-4">Modifica Articolo</h1>

                        <form action="modifica_articolo.php?id=<?php echo $articolo_id; ?>" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="titolo">Titolo</label>
                                <input type="text" class="form-control" id="titolo" name="titolo"
                                    value="<?php echo htmlspecialchars($articolo['titolo']); ?>" required>
                            </div>

                            <div class="form-group mt-3">
                                <label for="sinossi">Sinossi</label>
                                <textarea class="form-control" id="sinossi" name="sinossi" rows="2"
                                    required><?php echo htmlspecialchars($articolo['sinossi']); ?></textarea>
                            </div>

                            <div class="form-group mt-3">
                                <label for="testo">Testo dell'Articolo</label>
                                <textarea id="testo" class="form-control"
                                    name="testo"><?php echo htmlspecialchars($articolo['testo']); ?></textarea>
                            </div>

                            <div class="form-group mt-3">
                                <label for="immagine">Immagine attuale: </label>
                                <?php if (!empty($articolo['immagine'])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($articolo['immagine']); ?>" class="img-thumbnail mb-2"
                                        width="200">
                                <?php else: ?>
                                    <p class="text-muted">Nessuna immagine presente</p>
                                <?php endif; ?>
                                <input type="file" class="form-control-file mt-2" id="immagine" name="immagine">
                            </div>

                            <button type="submit" name="modifica" class="btn btn-primary mt-3">Salva Modifiche</button>
                        </form>

                        <hr class="text-light">

                        <h3>Eliminazione Articolo</h3>
                        <form action="modifica_articolo?id=<?php echo $articolo_id; ?>" method="POST"
                            onsubmit="return confirmEliminazione();">
                            <button type="submit" name="elimina" class="btn btn-danger mt-3">Elimina Articolo</button>
                        </form>

                        <hr class="text-light">

                        <a href="gestione_articoli" class="btn btn-secondary mt-3">Torna alla gestione articoli</a>
                    </div>
                </main>
            </div>
        </div>

        <script>
            tinymce.init({
                selector: '#testo',
                height: 400,
                plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
                toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | code',
                menubar: false,
                branding: false
            });

            function confirmEliminazione() {
                return confirm("Sei sicuro di voler eliminare questo articolo? Questa azione è irreversibile.");
            }
        </script>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>

</html>