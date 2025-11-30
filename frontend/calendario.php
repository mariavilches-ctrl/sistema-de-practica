<?php
session_start();
require_once 'config/api_config.php';
require_once 'config/session_helper.php';

requireLogin();

$pageTitle = "Calendarización (Strategy Pattern)";
$activePage = "calendario";

include 'partials/header.php';
include 'partials/sidebar.php';
?>
<main class="main">
    <header class="topbar">
        <h1>Calendarización de Sesiones</h1>
        <div class="user-info">
            <?= htmlspecialchars($_SESSION['usuario']['nombreCompleto'] ?? 'Usuario') ?>
        </div>
    </header>

    <section class="content">
        <div class="section-header">
            <h2>Generar Calendario (Strategy Pattern)</h2>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <div class="form-group" style="display: inline-block; margin-right: 20px; width: calc(25% - 15px);">
                <label>Estrategia de Calendarización</label>
                <select id="estrategiaCalendario">
                    <option value="uniforme">Uniforme</option>
                    <option value="intensiva">Intensiva</option>
                    <option value="progresiva">Progresiva</option>
                </select>
            </div>

            <div class="form-group" style="display: inline-block; margin-right: 20px; width: calc(25% - 15px);">
                <label>Horas Totales</label>
                <input type="number" id="horasCalendario" value="80" min="40" max="500">
            </div>

            <div class="form-group" style="display: inline-block; margin-right: 20px; width: calc(25% - 15px);">
                <label>Fecha de Inicio</label>
                <input type="date" id="fechaInicioCalendario">
            </div>

            <button class="btn-primary" onclick="generarCalendario()" style="margin-top: 22px;">Generar</button>
        </div>

        <div class="table-wrapper">
            <table id="tablaCalendarioGenerado">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Horas</th>
                    <th>Estado</th>
                </tr>
                </thead>
                <tbody>
                    <tr><td colspan="4" style="text-align: center;">Genera un calendario primero</td></tr>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php include 'partials/footer.php'; ?>
<script src="assets/JS/app.js"></script>