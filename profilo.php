<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit();
}

// Connessione al database
include 'db_connection.php';

// Recupero informazioni dell'utente dal database
$stmt = $conn->prepare("SELECT username, foto_profilo, nome, cognome, ruolo, password_hash FROM utenti WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT nome, cognome, email, foto_profilo FROM utenti WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$utente = $result->fetch_assoc();
$stmt->close();

// Gestione del cambiamento password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['old_password'], $_POST['new_password'], $_POST['confirm_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verifica della vecchia password
    if (password_verify($old_password, $user['password'])) {
        // Verifica che la nuova password e la conferma corrispondano
        if ($new_password === $confirm_password) {
            // Hash della nuova password
            $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);

            // Aggiornamento della password nel database
            $stmt = $conn->prepare("UPDATE utenti SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new_password_hashed, $user_id);
            $stmt->execute();
            $stmt->close();

            // Invio della notifica via email
            $subject = "Cambio Password - Sodalitas Quaerito";
            $message = "Ciao " . $utente['nome'] . ",\n\nLa tua password è stata cambiata con successo. Se non sei stato tu, contatta immediatamente il supporto.";
            $headers = "From: emanuele.izzo_1998@outlook.it";

            mail($utente['email'], $subject, $message, $headers);

            $success_message = "La tua password è stata cambiata con successo!";
        } else {
            $error_message = "La nuova password e la conferma non corrispondono.";
        }
    } else {
        $error_message = "La vecchia password non è corretta.";
    }
}

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
                <h1>Profilo Utente</h1>
                
                <div class="row justify-content-around">
                    <div class="col-5">
                        <h2>Informazioni</h2>

                        <div class="profilo-container row justify-content-center mb-3">
                            <img src="uploads/<?php echo htmlspecialchars($utente['foto_profilo'] ?: 'default.jpg'); ?>" alt="Foto Profilo" class="img-fluid rounded-circle col-5" style="width: 300px; height: 300px;">
                            <div class="col-6 justify-content-center align-items-center">
                                <p><h2>Nome:</h2> <?php echo htmlspecialchars($utente['nome']); ?></p>
                                <p><h2>Cognome:</h2> <?php echo htmlspecialchars($utente['cognome']); ?></p>
                                <p><h2>Email:</h2> <?php echo htmlspecialchars($utente['email']); ?></p>
                            </div>
                        </div>

                        <hr class="text-light">

                        <!-- Modifica password -->
                        <h2>Modifica la tua password</h2>
                        <?php if (isset($success_message)) { echo "<div class='alert alert-success'>$success_message</div>"; } ?>
                        <?php if (isset($error_message)) { echo "<div class='alert alert-danger'>$error_message</div>"; } ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="old_password" class="form-label">Vecchia Password</label>
                                <input type="password" class="form-control" id="old_password" name="old_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Nuova Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Conferma Nuova Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Modifica Password</button>
                        </form>
                    </div>

                    <div class="col-6 justify-content-center align-items-center">
                        <h2>Report</h2>

                        <a href="crea_report" class="btn btn-primary mb-3">Aggiungi report</a>
                        
                        <!-- Sezione per gli articoli -->
                        <div id="reports-container" class="row">
                            <!-- Gli articoli saranno caricati dinamicamente qui -->
                        </div>

                        <!-- Navigazione paginazione -->
                        <nav id="pagination" class="mb-3">
                            <ul class="pagination align-items-center justify-content-center">
                                <!-- Pagine saranno caricate dinamicamente -->
                            </ul>
                        </nav>
                    </div>

                    <script>
                        $(document).ready(function() {
                            // Funzione per caricare gli articoli tramite AJAX
                            function caricaReports(pagina) {
                                $.ajax({
                                    url: 'carica_reports.php', // Script che carica gli articoli
                                    method: 'GET',
                                    data: { pagina: pagina },
                                    success: function(response) {
                                        const data = JSON.parse(response);
                                        let reportsHtml = '';

                                        // Carica gli articoli nella pagina
                                        data.reports.forEach(function(report) {
                                            reportsHtml += `
                                                <div class="col-md-6 mb-3">
                                                    <div class="card align-items-center p-3 user-box">
                                                        <div class="card-body">
                                                            <h5 class="card-title">${report.titolo}</h5>
                                                            <p class="card-text">${report.sinossi}</p>
                                                            <a href="modifica_report?id=${report.id}" class="btn btn-primary">Modifica</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            `;
                                        });

                                        // Mostra gli articoli nella pagina
                                        $('#reports-container').html(reportsHtml);

                                        // Paginazione
                                        let paginationHtml = '';
                                        for (let i = 1; i <= data.paginas; i++) {
                                            paginationHtml += `<li class="page-item ${i === data.pagina ? 'active' : ''}">
                                                                <a class="page-link" href="#" data-pagina="${i}">${i}</a>
                                                            </li>`;
                                        }

                                        // Mostra la paginazione
                                        $('#pagination .pagination').html(paginationHtml);
                                    }
                                });
                            }

                            // Carica i primi articoli (pagina 1)
                            caricaReports(1);

                            // Cambia pagina
                            $(document).on('click', '.page-link', function(e) {
                                e.preventDefault();
                                const pagina = $(this).data('pagina');
                                caricaReports(pagina);
                            });
                        });
                    </script>
                </div>

                <hr class="text-light">

                <a href="index" class="btn btn-secondary mt-4">Torna alla home</a>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>