<?php
session_start();
require_once 'config/api_config.php';
require_once 'config/session_helper.php';

requireLogin();

$pageTitle = "Tipos de Práctica — Sistema de Prácticas UNACH";
$activePage = "tipos";

include 'partials/header.php';
include 'partials/sidebar.php';
?>
<main class="main">
    <header class="topbar">
        <h1>Catálogo de Tipos de Práctica</h1>
        <div class="user-info">
            <?= htmlspecialchars($_SESSION['usuario']['nombreCompleto'] ?? 'Usuario') ?>
        </div>
    </header>

    <section class="content">
        <div class="section-header">
            <h2>Tipos Disponibles</h2>
        </div>

        <div class="table-wrapper">
            <table id="tablaTipos">
                <thead>
                <tr>
                    <th>Tipo de Práctica</th>
                    <th>Horas Requeridas</th>
                    <th>Duración</th>
                    <th>Descripción</th>
                </tr>
                </thead>
                <tbody>
                    <tr><td colspan="4" style="text-align: center;">Cargando...</td></tr>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php include 'partials/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        cargarTiposPractica();
    });
</script>