<?php
session_start();
require_once 'config/api_config.php';
require_once 'config/session_helper.php';

requireLogin();

$pageTitle = "Centros de Práctica — Sistema de Prácticas UNACH";
$activePage = "centros";

include 'partials/header.php';
include 'partials/sidebar.php';
?>
<main class="main">
    <header class="topbar">
        <h1>Gestión de Centros de Práctica</h1>
        <div class="user-info">
            <?= htmlspecialchars($_SESSION['usuario']['nombreCompleto'] ?? 'Usuario') ?>
        </div>
    </header>

    <section class="content">
        <div class="section-header">
            <h2>Listado de Centros</h2>
            <button class="btn-primary" onclick="abrirFormularioCentro()">+ Agregar Centro</button>
        </div>

        <div class="table-wrapper">
            <table id="tablaCentros">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>RUT Empresa</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Dirección</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                    <tr><td colspan="6" style="text-align: center;">Cargando...</td></tr>
                </tbody>
            </table>
        </div>
    </section>
</main>

<!-- MODAL -->
<div id="modalCentro" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal('modalCentro')">&times;</span>
        <h2>Agregar Centro de Práctica</h2>
        
        <form id="formCentro" onsubmit="event.preventDefault(); guardarCentro();">
            <input type="hidden" id="idCentro">
            
            <div class="form-group">
                <label>RUT Empresa *</label>
                <input type="text" id="rutEmpresa" required>
            </div>

            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" id="nombreCentro" required>
            </div>

            <div class="form-group">
                <label>Descripción</label>
                <textarea id="descripcionCentro"></textarea>
            </div>

            <div class="form-group">
                <label>Habilidades Esperadas</label>
                <textarea id="habilidadesCentro"></textarea>
            </div>

            <div class="form-group">
                <label>Dirección *</label>
                <input type="text" id="direccionCentro" required>
            </div>

            <button type="submit" class="btn-save">Guardar</button>
        </form>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
<script src="assets/JS/app.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        cargarCentros();
    });
</script>