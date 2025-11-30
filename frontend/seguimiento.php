<?php
session_start();
require_once 'config/api_config.php';
require_once 'config/session_helper.php';

requireLogin();

$pageTitle = "Registro de Seguimiento (Observer Pattern)";
$activePage = "seguimiento";

include 'partials/header.php';
include 'partials/sidebar.php';
?>
<main class="main">
    <header class="topbar">
        <h1>Registro de Seguimiento</h1>
        <div class="user-info">
            <?= htmlspecialchars($_SESSION['usuario']['nombreCompleto'] ?? 'Usuario') ?>
        </div>
    </header>

    <section class="content">
        <div class="section-header">
            <h2>Eventos Registrados (Observer Pattern)</h2>
            <button class="btn-primary" onclick="cargarRegistroSeguimiento()">Actualizar</button>
        </div>

        <div class="table-wrapper">
            <table id="tablaRegistro">
                <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Evento</th>
                    <th>Detalles</th>
                </tr>
                </thead>
                <tbody>
                    <tr><td colspan="3" style="text-align: center;">Cargando...</td></tr>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php include 'partials/footer.php'; ?>
<script src="assets/JS/app.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        cargarRegistroSeguimiento();
    });
</script>