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
<main class="main">
    <header class="topbar">
        <h1>Calendarización de Sesiones</h1>
        <div class="user-info">
            <?= htmlspecialchars($_SESSION['usuario']['nombreCompleto'] ?? 'Usuario') ?>
        </div>
    </header>

    <section class="content">
        <!-- PESTAÑA 1: Generar Calendario -->
        <div class="tabs-container" style="margin-bottom: 30px;">
            <button class="tab-button active" onclick="mostrarTab('tab-generar')">📅 Generar Calendario</button>
            <button class="tab-button" onclick="mostrarTab('tab-sesiones')">📋 Sesiones Programadas</button>
        </div>

        <!-- TAB 1: GENERAR CALENDARIO (Strategy Pattern) -->
        <div id="tab-generar" class="tab-content active">
            <div class="section-header">
                <h2>Generar Calendario (Strategy Pattern)</h2>
            </div>

            <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label>Estrategia de Calendarización</label>
                        <select id="estrategiaCalendario" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                            <option value="uniforme">📊 Uniforme (distribuye horas igual)</option>
                            <option value="intensiva">⚡ Intensiva (más horas al inicio)</option>
                            <option value="progresiva">📈 Progresiva (aumenta gradualmente)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Horas Totales</label>
                        <input type="number" id="horasCalendario" value="80" min="40" max="500" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    </div>

                    <div class="form-group">
                        <label>Fecha de Inicio</label>
                        <input type="date" id="fechaInicioCalendario" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    </div>

                    <div class="form-group">
                        <label>Sesiones por Semana</label>
                        <input type="number" id="sesionesSemanasCalendario" value="2" min="1" max="7" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    </div>
                </div>

                <button class="btn-primary" onclick="generarCalendario()" style="width: 100%; padding: 12px; font-size: 1em;">
                    ✨ Generar Calendario
                </button>
            </div>

            <div class="table-wrapper">
                <h3 style="margin-bottom: 15px;">Calendario Generado (primeras 15 sesiones)</h3>
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
                        <tr><td colspan="4" style="text-align: center; color: #999;">Genera un calendario primero</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 2: SESIONES PROGRAMADAS -->
        <div id="tab-sesiones" class="tab-content" style="display: none;">
            <div class="section-header">
                <h2>Sesiones Programadas</h2>
                <button class="btn-primary" onclick="abrirFormularioSesion()">+ Agregar Sesión</button>
            </div>

            <div class="table-wrapper">
                <table id="tablaSesiones">
                    <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Horario</th>
                        <th>ID Práctica</th>
                        <th>Actividad</th>
                        <th>Horas</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="7" style="text-align: center;">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>

<!-- MODAL PARA AGREGAR SESIÓN -->
<div id="modalSesion" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal('modalSesion')">&times;</span>
        <h2>Agregar Sesión</h2>
        
        <form id="formSesion" onsubmit="event.preventDefault(); guardarSesion();">
            <div class="form-group">
                <label>ID Práctica *</label>
                <input type="number" id="idPracticaSesion" required>
            </div>

            <div class="form-group">
                <label>Fecha *</label>
                <input type="date" id="fechaSesion" required>
            </div>

            <div class="form-group">
                <label>Hora Inicio *</label>
                <input type="time" id="horaInicioSesion" required>
            </div>

            <div class="form-group">
                <label>Hora Término *</label>
                <input type="time" id="horaTerminoSesion" required>
            </div>

            <div class="form-group">
                <label>Horas *</label>
                <input type="number" id="horasSesion" min="1" required>
            </div>

            <div class="form-group">
                <label>Actividad</label>
                <textarea id="actividadSesion" placeholder="Descripción de la actividad"></textarea>
            </div>

            <div class="form-group">
                <label>Estado</label>
                <select id="estadoSesion">
                    <option value="Programada">Programada</option>
                    <option value="Realizada">Realizada</option>
                    <option value="Cancelada">Cancelada</option>
                </select>
            </div>

            <button type="submit" class="btn-save">Guardar</button>
        </form>
    </div>
</div>

<?php include 'partials/footer.php'; ?>

<style>
.tabs-container {
    display: flex;
    gap: 10px;
    border-bottom: 2px solid #e5e7eb;
    margin-bottom: 20px;
}

.tab-button {
    background: none;
    border: none;
    padding: 12px 20px;
    cursor: pointer;
    font-size: 1em;
    color: #6b7280;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
}

.tab-button:hover {
    color: #2563eb;
}

.tab-button.active {
    color: #2563eb;
    border-bottom-color: #2563eb;
}

.tab-content {
    animation: fadeIn 0.3s ease;
}

.tab-content.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>

<script src="assets/JS/app.js"></script>
<script>
// Funciones de tabs
function mostrarTab(tabId) {
    // Ocultar todos los tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
        tab.style.display = 'none';
    });
    
    // Desactivar todos los botones
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Mostrar tab seleccionado
    document.getElementById(tabId).classList.add('active');
    document.getElementById(tabId).style.display = 'block';
    
    // Activar botón
    event.target.classList.add('active');
    
    // Si es tab de sesiones, cargar datos
    if (tabId === 'tab-sesiones') {
        cargarSesiones();
    }
}

// Cargar sesiones al abrir pestaña
document.addEventListener('DOMContentLoaded', function() {
    cargarSesiones();
    
    // Set fecha de hoy como default
    const hoy = new Date().toISOString().split('T')[0];
    document.getElementById('fechaInicioCalendario').value = hoy;
});
</script>