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

if ($user['ruolo'] !== 'admin') {
    header("Location: index");
    exit();
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
                    <div class="container">
                        <h1>Gestione Articoli</h1>

                        <!-- Bottone per aggiungere nuovo articolo -->
                        <a href="crea_articolo" class="btn btn-primary mb-3">Aggiungi Articolo</a>

                        <!-- Sezione per gli articoli -->
                        <div id="articoli-container" class="row">
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
                            function caricaArticoli(pagina) {
                                $.ajax({
                                    url: 'carica_articoli.php', // Script che carica gli articoli
                                    method: 'GET',
                                    data: { pagina: pagina },
                                    success: function(response) {
                                        const data = JSON.parse(response);
                                        let articoliHtml = '';

                                        // Carica gli articoli nella pagina
                                        data.articoli.forEach(function(articolo) {
                                            articoliHtml += `
                                                <div class="col-md-3 mb-3">
                                                    <div class="card align-items-center p-3 user-box">
                                                        <div class="card-body">
                                                            <h5 class="card-title">${articolo.titolo}</h5>
                                                            <p class="card-text">${articolo.sinossi}</p>
                                                            <a href="modifica_articolo?id=${articolo.id}" class="btn btn-primary">Modifica</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            `;
                                        });

                                        // Mostra gli articoli nella pagina
                                        $('#articoli-container').html(articoliHtml);

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
                            caricaArticoli(1);

                            // Cambia pagina
                            $(document).on('click', '.page-link', function(e) {
                                e.preventDefault();
                                const pagina = $(this).data('pagina');
                                caricaArticoli(pagina);
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