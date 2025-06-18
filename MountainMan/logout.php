<?php
session_start(); // Mulai sesi

// Cek apakah sesi ada
if (isset($_SESSION['email'])) {
    session_unset();
    session_destroy();
    header("Location: register.html");
    exit();
} else {
    header("Location: register.html");
    exit();
}
?>
