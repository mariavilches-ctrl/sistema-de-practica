<?php
session_start();
require_once 'config/api_config.php';
require_once 'config/session_helper.php';

requireLogin();

$pageTitle = "Estudiantes — Sistema de Prácticas UNACH";
$activePage = "alumnos";

include 'partials/header.php';
include 'partials/sidebar.php';
?>
<main class="main">
    <header class="topbar">
        <h1>Gestión de Estudiantes</h1>
        <div class="user-info">
            <?= htmlspecialchars($_SESSION['usuario']['nombreCompleto'] ?? 'Usuario') ?>
        </div>
    </header>

    <section class="content">
        <div class="section-header">
            <h2>Listado de Estudiantes</h2>
            <button class="btn-primary" onclick="abrirModalEstudiante()">+ Agregar Estudiante</button>
        </div>

        <div class="table-wrapper">
            <table id="tablaEstudiantes">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>RUT</th>
                    <th>Nombre Completo</th>
                    <th>Año Lectivo</th>
                    <th>Carrera</th>
                    <th>Correo</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                    <tr><td colspan="7" style="text-align: center;">Cargando...</td></tr>
                </tbody>
            </table>
        </div>
    </section>
</main>

<div id="modalEstudiante" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal('modalEstudiante')">&times;</span>
        <h2>Agregar Estudiante</h2>

        <form id="formEstudiante" onsubmit="event.preventDefault(); guardarEstudiante();">
            <input type="hidden" id="idEstudiante">

            <div class="form-group">
                <label>RUT *</label>
                <input type="text" id="rutEstudiante" required placeholder="Ej: 12345678-9">
            </div>

            <div class="form-group">
                <label>Nombre Completo *</label>
                <input type="text" id="nombreEstudiante" required placeholder="Nombre completo del estudiante">
            </div>

            <div class="form-group">
                <label>Año Lectivo *</label>
                <input type="text" id="anoLectivo" required placeholder="Ej: 2023">
            </div>

            <div class="form-group">
                <label>Domicilio</label>
                <input type="text" id="domicilioEstudiante" placeholder="Dirección del estudiante">
            </div>

            <div class="form-group">
                <label>Teléfono</label>
                <input type="text" id="telefonoEstudiante" placeholder="Ej: +56912345678">
            </div>

            <div class="form-group">
                <label>Correo Institucional *</label>
                <input type="email" id="correoEstudiante" required placeholder="correo@alumnos.unach.cl">
            </div>

            <div class="form-group">
                <label>Carrera *</label>
                <select id="selectCarrera" required>
                    <option value="">Cargando carreras...</option>
                </select>
            </div>

            <button type="submit" class="btn-save">Guardar</button>
        </form>
    </div>
</div>

<?php include 'partials/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Llama a la función de app.js para llenar la tabla
        cargarEstudiantes();
        cargarCarreras();
    });
</script>
