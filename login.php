<?php
session_start();
// Connessione al database
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $sql = "SELECT id, username, password_hash, ruolo FROM utenti WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $db_username, $db_password_hash, $db_ruolo);
        $stmt->fetch();

        if (password_verify($password, $db_password_hash)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $db_username;
            $_SESSION["ruolo"] = $db_ruolo;
            header("Location: index");
            exit();
        } else {
            $error = "Password errata.";
        }
    } else {
        $error = "Utente non trovato.";
    }

    $stmt->close();
    $conn->close();
}

include 'head.html';
?>
    <body>
        <div class="login-container">
            <div class="login-header">
                <img src="images/logo.png" alt="Logo Agenzia" class="logo">
                <h2>Sodalidas Quaerito</h2>
                <p>Accesso Riservato</p>
            </div>
            <form action="login" method="POST">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
                
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                
                <button type="submit">Accedi</button>
            </form>
        </div>

        <?php if (isset($error)): ?>
            <script>showError("<?php echo $error; ?>");</script>
        <?php endif; ?>
    </body>
</html>