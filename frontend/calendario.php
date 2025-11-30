<?php
session_start();
require_once 'config/api_config.php';
require_once 'config/session_helper.php';

// 1. Proteger página
requireLogin();

$pageTitle  = "Calendario de Sesiones";
$activePage = "calendario";

$api = new ApiClient();
$sesiones = $api->getSesiones();

if (isset($sesiones['error']) || !is_array($sesiones)) {
    $sesiones = [];
    $errorMsg = "No hay conexión con el calendario.";
}

include 'partials/header.php';
include 'partials/sidebar.php';
?>
<main class="main">
    <header class="topbar">
        <h1>Calendario de Sesiones</h1>
        <div class="user-info">
             <?= htmlspecialchars($_SESSION['usuario']['nombreCompleto'] ?? 'Usuario') ?>
        </div>
    </header>

    <section class="content">
        <div class="section-header">
            <h2>Sesiones Programadas</h2>
            <button class="btn-primary">+ Agregar sesión</button>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Horario</th>
                    <th>ID Práctica</th>
                    <th>Actividad</th>
                    <th>Horas</th>
                    <th>Estado</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($sesiones)): ?>
                    <tr><td colspan="6" style="text-align: center;">No hay sesiones programadas</td></tr>
                <?php else: ?>
                    <?php foreach ($sesiones as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['fecha'] ?? '') ?></td>
                            <td><?= htmlspecialchars($s['horaInicio'] ?? '') ?> - <?= htmlspecialchars($s['horaTermino'] ?? '') ?></td>
                            <td>Practica #<?= htmlspecialchars($s['idPractica'] ?? '') ?></td>
                            <td><?= htmlspecialchars($s['actividad'] ?? '') ?></td>
                            <td><?= htmlspecialchars($s['horas'] ?? 0) ?> hrs</td>
                            <td>
                                <?php 
                                    $estado = $s['estado'] ?? 'Programada';
                                    $clase = ($estado === 'Realizada') ? 'badge-success' : 'badge-warning';
                                ?>
                                <span class="badge <?= $clase ?>"><?= htmlspecialchars($estado) ?></span>
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