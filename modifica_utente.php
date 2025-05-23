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

// Ottieni l'ID dell'utente dall'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID utente non valido.");
}
$user_id = intval($_GET['id']);

// Recupera i dati dell'utente da modificare dal database
$query = "SELECT id, username, foto_profilo, nome, cognome, email FROM utenti WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_modify = $result->fetch_assoc();
$stmt->close();

// Aggiornamento dati utente
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_user'])) {
    $nome = trim($_POST['nome']);
    $cognome = trim($_POST['cognome']);
    $email = trim($_POST['email']);
    
    $update_query = "UPDATE utenti SET nome = ?, cognome = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sssi", $nome, $cognome, $email, $user_id);
    $stmt->execute();
    header("Location: modifica_utente.php?id=$user_id&success=1");
    exit();
}

// Aggiornamento immagine del profilo
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_image'])) {
    if (isset($_FILES['foto_profilo']) && $_FILES['foto_profilo']['error'] == 0) {
        $target_dir = "uploads/";
        $file_extension = strtolower(pathinfo($_FILES["foto_profilo"]["name"], PATHINFO_EXTENSION));
        $new_file_name = strtolower($user['nome'] . "." . $user['cognome'] . "." . $file_extension);
        $target_file = $target_dir . $new_file_name;
        
        if (move_uploaded_file($_FILES["foto_profilo"]["tmp_name"], $target_file)) {
            $update_img_query = "UPDATE utenti SET foto_profilo = ? WHERE id = ?";
            $stmt = $conn->prepare($update_img_query);
            $stmt->bind_param("si", $new_file_name, $user_id);
            $stmt->execute();
            header("Location: modifica_utente.php?id=$user_id&image_updated=1");
            exit();
        }
    }
}

// Rigenerazione della password
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reset_password'])) {
    $new_password = bin2hex(random_bytes(4)); // Genera una password casuale
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    $update_pass_query = "UPDATE utenti SET password_hash = ? WHERE id = ?";
    $stmt = $conn->prepare($update_pass_query);
    $stmt->bind_param("si", $hashed_password, $user_id);
    $stmt->execute();
    
    // Invia email con la nuova password
    $to = $user['email'];
    $subject = "Nuova Password";
    $message = "Ciao " . $user['nome'] . ",\n\nLa tua nuova password è: " . $new_password . "\n\nUsername: " . $user['nome'] . "." . $user['cognome'];
    $headers = "From: no-reply@sodalidasquaerito.org";
    mail($to, $subject, $message, $headers);
    
    header("Location: modifica_utente.php?id=$user_id&password_reset=1");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_user'])) {
    $stmt = $conn->prepare("DELETE FROM utenti WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_persone");
    exit;
}

include 'head.html';
?>
    <body>
        <script>
            function confermaEliminazione() {
                if (confirm("Sei sicuro di voler eliminare questo utente?")) {
                    document.getElementById('deleteForm').submit();
                }
            }
        </script>
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
                    <h1>Modifica Utente</h1>
                    <form method="POST">
                        <label class="form-label">Nome:</label>
                        <input class="form-control" type="text" name="nome" value="<?php echo htmlspecialchars($user_modify['nome']); ?>" required>
                        
                        <label class="form-label">Cognome:</label>
                        <input class="form-control" type="text" name="cognome" value="<?php echo htmlspecialchars($user_modify['cognome']); ?>" required>
                        
                        <label class="form-label">Email:</label>
                        <input class="form-control" type="email" name="email" value="<?php echo htmlspecialchars($user_modify['email']); ?>" required>
                        
                        <button type="submit" class="btn btn-primary" name="update_user">Aggiorna Dati</button>
                    </form>

                    <hr class="text-light">

                    <h3>Foto Profilo</h3>
                    <img src="uploads/<?php echo htmlspecialchars($user_modify['foto_profilo']); ?>" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px;">
                    <form method="POST" enctype="multipart/form-data">
                        <input class="form-control" type="file" name="foto_profilo" required>
                        <button type="submit" class="btn btn-primary" name="update_image">Aggiorna Immagine</button>
                    </form>
                    
                    <hr class="text-light">

                    <div class="row">
                        <div class="col-6">
                            <h3>Reset Password</h3>
                            <form method="POST">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($user_modify['id']); ?>">
                                <button type="submit" class="btn btn-warning" name="reset_password">Genera Nuova Password</button>
                            </form>
                        </div>

                        <div class="col-6">
                            <h3>Eliminazione Utente</h3>
                            <form id="deleteForm" method="post">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user_modify['id']); ?>">
                                <button type="button" class="btn btn-danger" onclick="confermaEliminazione()">Elimina Utente</button>
                            </form>
                        </div>
                    </div>
                    
                    <hr class="text-light">

                    <a href="gestione_utenti" class="btn btn-secondary">Torna alla gestione utenti</a>
                </main>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

        <?php if (isset($_GET['password_reset']) && $_GET['password_reset'] == 1) { ?>
            <script>
                alert("Password modificata con successo!");
            </script>
        <?php } ?>
    </body>
</html>