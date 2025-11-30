<?php
// Evitar iniciar sesión dos veces
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica si el usuario está logueado, si no, lo manda al login
function requireLogin() {
    if (!isset($_SESSION['usuario']) && !isset($_SESSION['jwt_token'])) {
        // Redirige al login.php que está un nivel arriba (../login.php)
        // Nota: Si este archivo se incluye desde index.php, la ruta es relativa al index.
        // Lo más seguro es usar rutas relativas al archivo que invoca.
        header('Location: login.php?error=' . urlencode('Debes iniciar sesión'));
        exit;
    }
}

// Obtener datos del usuario actual de forma segura
function getUsuario() {
    return $_SESSION['usuario'] ?? null;
}

// Cerrar sesión
function logout() {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}
?>