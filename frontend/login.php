<?php
session_start();
require_once 'config/api_config.php';

$mensajeError = $_GET['error'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $api = new ApiClient();
    $resultado = $api->login($_POST['correo'], $_POST['password']);
    
    if (isset($resultado['token'])) {
        $_SESSION['jwt_token'] = $resultado['token'];
        $_SESSION['usuario'] = $resultado['usuario'];
        header('Location: index.php');
        exit;
    } else {
        $mensajeError = $resultado['message'] ?? 'Credenciales inválidas';
    }
}
?>
<!DOCTYPE html>
<!-- ... resto del HTML ... -->