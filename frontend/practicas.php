<?php
session_start();
require_once 'config/api_config.php';
require_once 'config/session_helper.php';

// 1. Proteger página (Si no hay login, te saca)
requireLogin();

$pageTitle = "Prácticas Asignadas – Sistema de Prácticas UNACH";
$activePage = "practicas";

// 2. Llamada inicial a la API para pintar la tabla con PHP (Renderizado del lado del servidor)
$api = new ApiClient();
$practicas = $api->getPracticas();

// Manejo de error si Python está apagado
if (isset($practicas['error']) || !is_array($practicas)) {
    $errorMessage = "No se pudo conectar con el servidor Python.";
    $practicas = [];
}

include 'partials/header.php';
include 'partials/sidebar.php';
?>

<main class="main">
    <header class="topbar">
        <h1>Gestión de Prácticas</h1>
        <div class="user-info">
             <?= htmlspecialchars(getUsuario()['nombreCompleto'] ?? getUsuario()['Email'] ?? 'Usuario') ?>
        </div>
    </header>

    <section class="content">
        
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>

        <div class="section-header">
            <h2>Listado de Prácticas</h2>
            <button class="btn-primary" onclick="abrirModalPractica()">+ Asignar práctica</button>
        </div>

        <div class="table-wrapper">
            <table id="tablaPracticas">
                <thead>
                <tr>
                    <th>ID Práctica</th>
                    <th>ID Estudiante</th>
                    <th>Tipo</th>
                    <th>ID Centro</th>
                    <th>ID Tutor</th>
                    <th>Inicio / Fin</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($practicas)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px;">
                            No hay prácticas registradas todavía.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($practicas as $p): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($p['idPractica'] ?? '') ?></td>
                            <td><?= htmlspecialchars($p['idEstudiante'] ?? '-') ?></td>
                            <td>
                                <span class="badge badge-warning"><?= htmlspecialchars($p['tipo'] ?? '-') ?></span>
                            </td>
                            <td><?= htmlspecialchars($p['idCentroPractica'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($p['idTutor'] ?? '-') ?></td>
                            <td>
                                <small>Del: <?= htmlspecialchars($p['fechaDeInicio'] ?? '?') ?></small><br>
                                <small>Al: <?= htmlspecialchars($p['fechaDeTermino'] ?? '?') ?></small>
                            </td>
                            <td>
                                <button class="btn-delete" style="padding: 5px 10px;" onclick="eliminarPractica(<?= $p['idPractica'] ?>)">Eliminar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<div id="modalPractica" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal('modalPractica')">&times;</span>
        <h2>Asignar Nueva Práctica</h2>
        
        <form id="formPractica" onsubmit="event.preventDefault(); guardarPractica();">
            
            <div class="form-group">
                <label>Estudiante *</label>
                <select id="selectEstudiante" required>
                    <option value="">Cargando estudiantes...</option>
                </select>
            </div>

            <div class="form-group">
                <label>Centro de Práctica *</label>
                <select id="selectCentro" required>
                    <option value="">Cargando centros...</option>
                </select>
            </div>

            <div class="form-group">
                <label>Tipo de Práctica *</label>
                <select id="selectTipo" required>
                    <option value="Laboral">Laboral</option>
                    <option value="Profesional">Profesional</option>
                </select>
            </div>

            <div class="form-group" style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label>ID Tutor (Académico)</label>
                    <input type="number" id="idTutor" placeholder="Ej: 2" required>
                </div>
                <div style="flex: 1;">
                    <label>ID Supervisor (Centro)</label>
                    <input type="number" id="idSupervisor" placeholder="Ej: 2" required>
                </div>
            </div>

            <div class="form-group" style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label>Fecha Inicio *</label>
                    <input type="date" id="fechaInicio" required>
                </div>
                <div style="flex: 1;">
                    <label>Fecha Término *</label>
                    <input type="date" id="fechaTermino" required>
                </div>
            </div>

            <button type="submit" class="btn-save">Guardar Asignación</button>
        </form>
    </div>
</div>

<?php include 'partials/footer.php'; ?>