
<?php
session_start();
require_once 'config/api_config.php';
require_once 'config/session_helper.php';

$api = new ApiClient();
$mensajeError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $resultado = $api->login($correo, $password);

    if (!empty($resultado['error'])) {
        $mensajeError = $resultado['message'] ?? 'Error al conectar con la API';
    } elseif (!empty($resultado['token'])) {
        $_SESSION['jwt_token'] = $resultado['token'];
        $_SESSION['usuario'] = $resultado['usuario'] ?? $resultado['datos'] ?? null;
        header('Location: index.php');
        exit;
    } elseif (($resultado['success'] ?? false) && !empty($resultado['datos'])) {
        $_SESSION['usuario'] = $resultado['datos'];
        header('Location: index.php');
        exit;
    } else {
        $mensajeError = $resultado['message'] ?? 'Credenciales inválidas';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    
    <link rel="stylesheet" href="assets/CSS/styles.css">
</head>
<body class="login-body">
    <div class="login-container">
        <h1>Iniciar sesión</h1>
        
        <?php if (!empty($mensajeError)): ?>
            <div class="alert-error"><?= htmlspecialchars($mensajeError) ?></div>
        <?php endif; ?>

        <form method="POST" class="login-form">
            <label>Correo institucional</label>
            <input type="email" name="correo" placeholder="ejemplo@unach.cl" required>
            
            <label>Contraseña</label>
            <input type="password" name="password" placeholder="••••••" required>
            
            <button type="submit" class="btn-primary btn-full">Entrar</button>
        </form>
    </div>
</body>
</html>