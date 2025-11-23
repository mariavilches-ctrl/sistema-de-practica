<?php
// login.php
session_start();
$mensajeError = $_GET['error'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión – Sistema de Prácticas UNACH</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="login-body">
<div class="login-container">
    <h1>Prácticas UNACH</h1>
    <p class="login-subtitle">Sistema de Gestión de Prácticas Profesionales</p>

    <?php if ($mensajeError): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($mensajeError) ?>
        </div>
    <?php endif; ?>

    <form action="procesar_login.php" method="POST" class="login-form">
        <label for="correo">Correo institucional</label>
        <input type="email" id="correo" name="correo" required placeholder="ignacio@unach.cl">

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required placeholder="********">

        <button type="submit" class="btn-primary btn-full">Iniciar sesión</button>
    </form>

    <p class="login-footer">
        Universidad Adventista de Chile · Sistemas de Información II
    </p>
</div>
</body>
</html>
