<?php
// Helper para manejo de sesiones
function requireLogin() {
    // Accept either a jwt_token (future backend) or usuario (current backend response)
    if (!isset($_SESSION['jwt_token']) && !isset($_SESSION['usuario'])) {
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