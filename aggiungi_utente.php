<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit();
}

include 'db_connection.php';

// Verifica se l'utente ha il ruolo di admin
$stmt = $conn->prepare("SELECT ruolo FROM utenti WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user['ruolo'] !== 'admin') {
    header("Location: index");
    exit();
}

// Variabili di errore
$error = '';

// Gestione dell'invio dei dati
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $email = $_POST['email'];
    $username = $nome.".".$cognome;
    
    // Generazione della password casuale
    $password = bin2hex(random_bytes(4)); // 8 caratteri
    
    // Hash della password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Normalizza il nome e cognome per il file
    $nome_pulito = strtolower(str_replace(" ", "_", preg_replace("/[^a-zA-Z0-9]/", "", $nome)));  // Rimuove caratteri speciali
    $cognome_pulito = strtolower(str_replace(" ", "_", preg_replace("/[^a-zA-Z0-9]/", "", $cognome)));  

    // Controlla se è stato caricato un file
    if (isset($_FILES['foto_profilo']) && $_FILES['foto_profilo']['error'] == 0) {
        $target_dir = "uploads/"; // Cartella dove salvare le immagini
        $file_extension = strtolower(pathinfo($_FILES["foto_profilo"]["name"], PATHINFO_EXTENSION));
        $new_file_name = $nome_pulito . "_" . $cognome_pulito . "." . $file_extension; // Nome basato su nome e cognome
        $target_file = $target_dir . $new_file_name;

        // Controllo validità del file (tipo e dimensione)
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_extension, $allowed_extensions)) {
            die("Errore: formato immagine non valido.");
        }

        if ($_FILES["foto_profilo"]["size"] > 5000000) { // 5MB limite
            die("Errore: immagine troppo grande.");
        }

        // Salva il file nella cartella 'uploads'
        if (move_uploaded_file($_FILES["foto_profilo"]["tmp_name"], $target_file)) {
            echo "Immagine caricata con successo.";
        } else {
            die("Errore nel caricamento dell'immagine.");
        }
    } else {
        $new_file_name = "default.jpg"; // Se non viene caricata un'immagine, usa un'immagine predefinita
    }

    // Inserimento nel database
    $stmt = $conn->prepare("INSERT INTO utenti (nome, cognome, email, username, password_hash, ruolo, foto_profilo, stato) VALUES (?, ?, ?, ?, ?, 'utente', ?, 'attivo')");
    $stmt->bind_param("ssssss", $nome, $cognome, $email, $username, $password_hash, $new_file_name);
    
    if ($stmt->execute()) {
        // Invia la mail
        $to = $email;
        $subject = "Credenziali di accesso - Sodalidas Quaerito";
        $message = "Ciao $nome $cognome,\n\nLe tue credenziali di accesso sono:\nUsername: $username\nPassword: $password\n\nPuoi accedere al sistema a partire da ora.";
        $headers = "From: no-reply@sodalidasquaerito.com";
        
        if (mail($to, $subject, $message, $headers)) {
            echo "<p>Utente aggiunto e email inviata con successo!</p>";
        } else {
            $error = "Errore nell'invio dell'email.";
        }
    } else {
        $error = "Errore nell'aggiunta dell'utente.";
    }

    $stmt->close();
}

// Recupero informazioni dell'utente dal database
$stmt = $conn->prepare("SELECT username, foto_profilo, nome, cognome, ruolo FROM utenti WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

if ($user['ruolo'] !== 'admin') {
    header("Location: index");
    exit();
}
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
                        <a href="profile" class="btn btn-success btn-sm mt-2 w-100">Visualizza profilo</a>
                        <?php if ($user['ruolo'] === 'admin'): ?>
                            <a href="admin" class="btn btn-warning btn-sm mt-2 w-100">Amministrazione</a>
                        <?php endif; ?>

                        <hr class="text-light">

                        <a href="logout" class="btn btn-danger btn-sm mt-2 w-100">Logout</a>
                    </div>
                </nav>

                <!-- Contenuto principale -->
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 d-flex-inline align-items-center justify-content-center position-relative my-5">
                    <h1>Aggiungi Nuovo Utente</h1>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <!-- Form per l'aggiunta dell'utente -->
                    <form action="aggiungi_utente" method="POST">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="cognome" class="form-label">Cognome</label>
                            <input type="text" class="form-control" id="cognome" name="cognome" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="foto_profilo" class="form-label">Foto Profilo:</label>
                            <input type="file" class="form-control" name="foto_profilo" accept="image/*">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Aggiungi Utente</button>
                    </form>
                    
                    <a href="gestione_utenti" class="btn btn-secondary mt-4">Torna alla gestione utenti</a>
                </main>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>