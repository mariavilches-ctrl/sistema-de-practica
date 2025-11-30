<?php
session_start();
require_once 'config/api_config.php';
require_once 'config/session_helper.php';

// 1. Proteger página
requireLogin();

$pageTitle = "Prácticas Asignadas – Sistema de Prácticas UNACH";
$activePage = "practicas";

// 2. Llamada a la API Real
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
             <?= htmlspecialchars($_SESSION['usuario']['nombreCompleto'] ?? 'Usuario') ?>
        </div>
    </header>

    <section class="content">
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>

        <div class="section-header">
            <h2>Listado de Prácticas</h2>
            <button class="btn-primary" onclick="alert('Aquí iría el modal para crear práctica')">+ Asignar práctica</button>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>ID Práctica</th>
                    <th>ID Estudiante</th>
                    <th>Tipo</th>
                    <th>ID Centro</th>
                    <th>ID Tutor</th>
                    <th>Inicio / Fin</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($practicas)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">
                            No hay prácticas registradas todavía.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($practicas as $p): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($p['idPractica'] ?? '') ?></td>
                            <td><?= htmlspecialchars($p['idEstudiante'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($p['tipo'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($p['idCentroPractica'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($p['idTutor'] ?? '-') ?></td>
                            <td>
                                <small>Del: <?= htmlspecialchars($p['fechaDeInicio'] ?? '?') ?></small><br>
                                <small>Al: <?= htmlspecialchars($p['fechaDeTermino'] ?? '?') ?></small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php include 'partials/footer.php'; ?>