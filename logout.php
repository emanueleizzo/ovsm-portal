<?php
session_start();
session_destroy(); // Termina la sessione
header("Location: login"); // Reindirizza al login
exit();
?>