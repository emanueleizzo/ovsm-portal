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

if ($user['ruolo'] !== 'admin') {
    header("Location: index");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titolo = $_POST['titolo'];
    $sinossi = $_POST['sinossi'];
    $testo = $_POST['testo'];
    $immagine = '';

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
        } else {
            die("Errore nel caricamento dell'immagine.");
        }
    }

    // Inserisci articolo nel database
    $sql = "INSERT INTO reports (titolo, sinossi, testo, immagine, autore_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $titolo, $sinossi, $testo, $$_FILES["immagine"]["name"], $_SESSION['user_id']);
    if ($stmt->execute()) {
        header("Location: profilo?success=1");
        exit();
    } else {
        echo "Errore nell'inserimento del report.";
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
                        <h1>Inserimento Report</h1>

                        <form action="crea_report" method="POST" enctype="multipart/form-data" class="my-3">
                            <div class="form-group">
                                <label class="form-label" for="titolo">Titolo</label>
                                <input type="text" class="form-control" id="titolo" name="titolo" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="sinossi">Sinossi</label>
                                <textarea class="form-control" id="sinossi" name="sinossi" rows="2" required></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="testo">Testo del Report</label><br>
                                <textarea id="testo" class="form-control mb-2" name="testo"></textarea>
                            </div>

                            <div class="form-group mb-2">
                                <label class="form-label" for="immagine">Carica un'immagine</label>
                                <input type="file" class="form-control-file" id="immagine" name="immagine">
                            </div>

                            <button type="submit" class="btn btn-primary">Inserisci Report</button>
                        </form>
                    </div>

                    <script>
                        // Inizializza TinyMCE per l'editor di testo avanzato
                        tinymce.init({
                            selector: '#testo',
                            height: 400,
                            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
                            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | code',
                            menubar: false,
                            branding: false
                        });
                    </script>
                    
                    <a href="profilo" class="btn btn-secondary">Torna al profilo</a>
                </main>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>