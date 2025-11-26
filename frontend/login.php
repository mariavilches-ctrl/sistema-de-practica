<?php
session_start();
require_once 'config/api_config.php';

$mensajeError = $_GET['error'] ?? null;

// Procesamiento del formulario (validaciones simples antes de llamar a la API)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // normalize inputs
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($correo === '' || $password === '') {
        $mensajeError = 'Por favor completa todos los campos.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensajeError = 'Ingresa un correo válido.';
    } else {
        $api = new ApiClient();
        $resultado = $api->login($correo, $password);

        if (isset($resultado['token'])) {
            // Guardar token/usuario en sesión
            $_SESSION['jwt_token'] = $resultado['token'];
            $_SESSION['usuario'] = $resultado['usuario'];
            header('Location: index.php');
            exit;
        } else {
            $mensajeError = $resultado['message'] ?? 'Credenciales inválidas';
        }
    }
}
?>
<!DOCTYPE html>
<?php
    $pageTitle = 'Iniciar sesión - Sistema de Prácticas UNACH';
?>
<?php include 'partials/header.php'; ?>

<main class="login-body" aria-labelledby="login-heading">
    <div class="login-container" role="main">
        <h1 id="login-heading">Iniciar sesión</h1>
        <p class="login-subtitle">Ingresa con tu correo institucional para continuar.</p>

        <?php if (!empty($mensajeError)): ?>
            <div class="alert-error" role="alert" aria-live="polite"><?= htmlspecialchars($mensajeError) ?></div>
        <?php endif; ?>

        <form id="loginForm" class="login-form" method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" novalidate>
            <div>
                <label for="correo">Correo institucional</label>
                <input id="correo" name="correo" type="email" required autocomplete="username" placeholder="nombre@institucion.edu" value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">
            </div>

            <div>
                <label for="password">Contraseña</label>
                <div style="position:relative; display:flex; gap:0.5rem; align-items:center;">
                    <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="Tu contraseña">
                    <button type="button" id="togglePass" aria-label="Mostrar contraseña" style="background:transparent;border:none;cursor:pointer;">👁️</button>
                </div>
            </div>

            <button id="submitBtn" class="btn-primary btn-full" type="submit">Entrar</button>
        </form>

        <div class="login-footer">
            <p><a href="#">¿Olvidaste tu contraseña?</a></p>
        </div>
    </div>
</main>

<script>
// Small client-side helpers: toggle password visibility and prevent double submit
document.addEventListener('DOMContentLoaded', function(){
    const toggle = document.getElementById('togglePass');
    const pwd = document.getElementById('password');
    const form = document.getElementById('loginForm');
    const submit = document.getElementById('submitBtn');

    toggle?.addEventListener('click', function(){
        if (pwd.type === 'password') {
            pwd.type = 'text';
            toggle.setAttribute('aria-label', 'Ocultar contraseña');
        } else {
            pwd.type = 'password';
            toggle.setAttribute('aria-label', 'Mostrar contraseña');
        }
    });

    form?.addEventListener('submit', function(e){
        // Simple client-side validation to improve UX
        if (!form.reportValidity()) {
            // Let browser display validation UI
            return;
        }
        // Disable submit to prevent duplicate sends
        submit.disabled = true;
        submit.textContent = 'Ingresando...';
    });
});
</script>

<?php include 'partials/footer.php'; ?>