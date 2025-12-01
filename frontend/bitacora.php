<?php
session_start();
require_once 'config/api_config.php';
require_once 'config/session_helper.php';

// 1. Proteger la página
requireLogin();

$pageTitle = "Bitácora – Sistema de Prácticas UNACH";
$activePage = "bitacora"; // Variable para que el sidebar sepa dónde estamos

// 2. Obtener datos de la API
$api = new ApiClient();
$bitacora = $api->getBitacora();

// Manejo de errores si la API falla o devuelve error
if (isset($bitacora['error']) || !is_array($bitacora)) {
    $bitacora = [];
    $errorMessage = "No se pudieron cargar los registros de la bitácora.";
}

// 3. Inclusiones correctas (buscando en la carpeta 'partials')
include 'partials/header.php';
include 'partials/sidebar.php';
?>

<main class="main">
    <header class="topbar">
        <h1>Bitácora de Actividades</h1>
        <div class="user-info">
            <?= htmlspecialchars(getUsuario()['nombreCompleto'] ?? getUsuario()['Email'] ?? 'Usuario') ?>
        </div>
    </header>

    <section class="content">

    <div class="section-header">
        <h2>Bitácora</h2>
        <button class="btn-primary" onclick="abrirFormularioBitacora()">+ Nueva entrada</button>
    </div>

    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-error" style="color: red; margin-bottom: 15px;">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <div class="table-wrapper">
        <table id="tablaBitacora">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>ID Estudiante</th>
                    <th>ID Práctica</th>
                    <th>Título / Logros</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($bitacora)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: gray; padding: 20px;">
                        No hay registros en la bitácora.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($bitacora as $b): ?>
                    <tr
                        onclick="abrirFormularioBitacoraDesdeFila(this)"
                        data-id="<?= htmlspecialchars($b['idBitacora'] ?? '') ?>"
                        data-fecha="<?= htmlspecialchars($b['fechaRegistro'] ?? '') ?>"
                        data-id-estudiante="<?= htmlspecialchars($b['idEstudiante'] ?? '') ?>"
                        data-id-practica="<?= htmlspecialchars($b['idPractica'] ?? '') ?>"
                        data-titulo="<?= htmlspecialchars($b['titulo'] ?? ($b['logros'] ?? '')) ?>"
                        data-descripcion="<?= htmlspecialchars($b['descripcion'] ?? '') ?>"
                    >
                        <td>#<?= htmlspecialchars($b['idBitacora'] ?? '') ?></td>
                        <td><?= htmlspecialchars($b['fechaRegistro'] ?? '') ?></td>
                        <td><?= htmlspecialchars($b['idEstudiante'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($b['idPractica'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($b['titulo'] ?? $b['logros'] ?? 'Sin título') ?></td>
                        <td><?= htmlspecialchars($b['descripcion'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<div id="modalBitacora" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal('modalBitacora')">&times;</span>
        <h2>Registro de Bitácora</h2>

<form id="formBitacora" onsubmit="event.preventDefault(); guardarBitacora();">
    <input type="hidden" id="idBitacora">

    <div class="form-group">
        <label>ID Práctica *</label>
        <input type="number" id="idPracticaBitacora" required>
    </div>

    <div class="form-group">
        <label>ID Estudiante *</label>
        <input type="number" id="idEstudianteBitacora" required>
    </div>

    <div class="form-group">
        <label>Habilidades desarrolladas *</label>
        <textarea id="habilidadesBitacora" rows="2" required></textarea>
    </div>

    <div class="form-group">
        <label>Desafíos</label>
        <textarea id="desafiosBitacora" rows="2"></textarea>
    </div>

    <div class="form-group">
        <label>Logros</label>
        <textarea id="logrosBitacora" rows="2"></textarea>
    </div>

    <button type="submit" class="btn-save">Guardar</button>
</form>

    </div>
</div>

<?php include 'partials/footer.php'; ?>

