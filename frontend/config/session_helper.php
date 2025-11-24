<?php
// Helper para manejo de sesiones
function requireLogin() {
    if (!isset($_SESSION['jwt_token'])) {
        header('Location: login.php?error=' . urlencode('Debes iniciar sesión'));
        exit;
    }
}

function getUsuario() {
    return $_SESSION['usuario'] ?? null;
}

function logout() {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>