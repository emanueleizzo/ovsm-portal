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

if ($user['ruolo'] !== 'admin') {
    header("Location: index");
    exit();
}

// Recupero tutti gli utenti
$stmt = $conn->prepare("SELECT id, nome, cognome, foto_profilo, email, ruolo FROM utenti");
$stmt->execute();
$utenti_result = $stmt->get_result();
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
                    <h1>Gestione Utenti</h1>
                    
                    <!-- Pulsante per aggiungere un nuovo utente -->
                    <a href="aggiungi_utente" class="btn btn-primary mb-4">Aggiungi Nuovo Utente</a>
                    
                    <!-- Tabella degli utenti -->
                    <div id="utenti-container" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
                    </div>

                    <!-- Navigazione paginazione -->
                    <nav id="pagination" class="mb-3">
                        <ul class="pagination align-items-center justify-content-center">
                            <!-- Pagine saranno caricate dinamicamente -->
                        </ul>
                    </nav>
                    
                    <script>
                        $(document).ready(function() {
                            // Funzione per caricare gli articoli tramite AJAX
                            function caricaUtenti(pagina) {
                                $.ajax({
                                    url: 'carica_utenti.php', // Script che carica gli articoli
                                    method: 'GET',
                                    data: { pagina: pagina },
                                    success: function(response) {
                                        const data = JSON.parse(response);
                                        let utentiHtml = '';

                                        // Carica gli articoli nella pagina
                                        data.utenti.forEach(function(utente) {
                                            utentiHtml += `
                                                <div class="col mb-4">
                                                    <div class="card align-items-center p-3 user-box">
                                                        <img src="uploads/`+utente.foto_profilo+`" class="img-fluid rounded-circle" style="width: 100px; height: 100px;" alt="Foto di ${utente.nome} ${utente.cognome}">
                                                        <div class="card-body">
                                                            <h5 class="card-title">${utente.nome} ${utente.cognome}</h5>
                                                            <p class="card-text m-0"><strong>Email:</strong> ${utente.email}</p>
                                                            <p class="card-text"><strong>Ruolo:</strong> ${utente.ruolo}</p>
                                                            <div class="d-flex justify-content-center">
                                                                <a href="modifica_utente?id=${utente.id}" class="btn btn-primary btn-sm">Modifica</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>  
                                            `;
                                        });

                                        // Mostra gli articoli nella pagina
                                        $('#utenti-container').html(utentiHtml);

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
                            caricaUtenti(1);

                            // Cambia pagina
                            $(document).on('click', '.page-link', function(e) {
                                e.preventDefault();
                                const pagina = $(this).data('pagina');
                                caricaUtenti(pagina);
                            });
                        });
                    </script>

                    <a href="admin" class="btn btn-secondary">Torna alla pagina di amministrazione</a>
                </main>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>