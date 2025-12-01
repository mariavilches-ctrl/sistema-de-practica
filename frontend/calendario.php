<?php
session_start();
require_once 'config/api_config.php';
require_once 'config/session_helper.php';

requireLogin();

$pageTitle = "Calendarización — Sistema de Prácticas UNACH";
$activePage = "calendario";

include 'partials/header.php';
include 'partials/sidebar.php';
?>

<style>
.tabs-container { display: flex; gap: 10px; border-bottom: 2px solid #e5e7eb; margin-bottom: 20px; }
.tab-button { background: none; border: none; padding: 12px 20px; cursor: pointer; font-size: 1em; color: #6b7280; border-bottom: 3px solid transparent; transition: all 0.3s ease; }
.tab-button:hover { color: #2563eb; }
.tab-button.active { color: #2563eb; border-bottom-color: #2563eb; font-weight: bold; }
.tab-content { display: none; animation: fadeIn 0.3s ease; }
.tab-content.active { display: block; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>

<main class="main">
    <header class="topbar">
        <h1>Calendarización de Sesiones</h1>
        <div class="user-info">
            <?= htmlspecialchars(getUsuario()['nombreCompleto'] ?? getUsuario()['Email'] ?? 'Usuario') ?>
        </div>
    </header>

    <section class="content">
        
        <div class="tabs-container">
            <button class="tab-button active" onclick="mostrarTab(event, 'tab-generar')">📅 Generar Calendario</button>
            <button class="tab-button" onclick="mostrarTab(event, 'tab-sesiones')">📋 Sesiones Programadas</button>
        </div>

        <div id="tab-generar" class="tab-content active">
            <div class="section-header"><h2>Generar Calendario (Strategy)</h2></div>

            <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label>Estrategia</label>
                        <select id="estrategiaCalendario" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                            <option value="uniforme">📊 Uniforme</option>
                            <option value="intensiva">⚡ Intensiva</option>
                            <option value="progresiva">📈 Progresiva</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Horas Totales</label>
                        <input type="number" id="horasCalendario" value="80" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    </div>
                    <div class="form-group">
                        <label>Fecha de Inicio</label>
                        <input type="date" id="fechaInicioCalendario" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    </div>
                </div>
                <button class="btn-primary" onclick="generarCalendario()" style="width: 100%;">✨ Generar Propuesta</button>
            </div>

            <div class="table-wrapper">
                <table id="tablaCalendarioGenerado">
                    <thead><tr><th>#</th><th>Fecha</th><th>Horas</th><th>Estado</th></tr></thead>
                    <tbody><tr><td colspan="4" style="text-align: center; color: #999;">Genera un calendario primero</td></tr></tbody>
                </table>
            </div>
        </div>

        <div id="tab-sesiones" class="tab-content">
            <div class="section-header">
                <h2>Sesiones en Base de Datos</h2>
                <button class="btn-primary" onclick="abrirModalSesion()">+ Agregar Sesión</button>
            </div>

            <div class="table-wrapper">
                <table id="tablaSesiones">
                    <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Horario</th>
                        <th>Práctica</th>
                        <th>Actividad</th>
                        <th>Horas</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody><tr><td colspan="7" style="text-align: center;">Cargando...</td></tr></tbody>
                </table>
            </div>
        </div>
    </section>
</main>

<div id="modalSesion" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal('modalSesion')">&times;</span>
        <h2>Agregar Nueva Sesión</h2>
        
        <form id="formSesion" onsubmit="event.preventDefault(); guardarSesion();">
            
            <div class="form-group">
                <label>Práctica Asociada *</label>
                <select id="selectPracticaSesion" required>
                    <option value="">Cargando prácticas...</option>
                </select>
            </div>

            <div class="form-group">
                <label>Fecha *</label>
                <input type="date" id="fechaSesion" required>
            </div>

            <div class="form-group" style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label>Hora Inicio</label>
                    <input type="time" id="horaInicio" required>
                </div>
                <div style="flex: 1;">
                    <label>Hora Término</label>
                    <input type="time" id="horaTermino" required>
                </div>
            </div>

            <div class="form-group">
                <label>Horas Realizadas</label>
                <input type="number" id="horasSesion" placeholder="Ej: 4" required>
            </div>

            <div class="form-group">
                <label>Actividad</label>
                <textarea id="actividadSesion" rows="2" placeholder="Descripción..."></textarea>
            </div>

            <button type="submit" class="btn-save">Guardar Sesión</button>
        </form>
    </div>
</div>

<?php include 'partials/footer.php'; ?>

<script>
    // Establecer fecha de hoy
    document.addEventListener('DOMContentLoaded', function() {
        const hoy = new Date().toISOString().split('T')[0];
        const fechaInput = document.getElementById('fechaInicioCalendario');
        if(fechaInput) fechaInput.value = hoy;
    });

    // Función simple para cambiar pestañas
    function mostrarTab(evt, tabId) {
        // Ocultar todo
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));

        // Mostrar el seleccionado
        document.getElementById(tabId).classList.add('active');
        if(evt) evt.currentTarget.classList.add('active');

        // Si entramos a sesiones, cargamos la tabla
        if(tabId === 'tab-sesiones') {
            if(typeof cargarSesiones === 'function') cargarSesiones();
        }
    }
</script>