<?php
session_start();
require_once 'config/api_config.php';
require_once 'config/session_helper.php';

$pageTitle = "Prácticas Asignadas – Sistema de Prácticas UNACH";
$activePage = "practicas";

// TODO: Descomentar cuando el backend esté listo
// requireLogin();

// Datos de ejemplo (temporal)
$practicas = [
    [
        'estudiante' => 'Juan Pérez',
        'tipo' => 'Práctica Profesional',
        'centro' => 'Hospital Clínico Chillán',
        'supervisor_interno' => 'Prof. Soto',
        'supervisor_externo' => 'Dr. Morales',
        'estado' => 'En curso',
    ],
    [
        'estudiante' => 'Ana Díaz',
        'tipo' => 'Práctica I',
        'centro' => 'Colegio Adventista',
        'supervisor_interno' => 'Prof. García',
        'supervisor_externo' => 'Sr. López',
        'estado' => 'Pendiente',
    ],
];

// Cuando el backend esté listo, reemplazar con:
/*
$api = new ApiClient();
$practicas = $api->getPracticas();

if (isset($practicas['error'])) {
    $practicas = [];
    $errorMessage = $practicas['message'] ?? 'Error al cargar prácticas';
}
*/

include 'partials/header.php';
include 'partials/sidebar.php';
?>
<main class="main">
    <header class="topbar">
        <h1>Prácticas Asignadas</h1>
        <div class="user-info">
            <span><?= htmlspecialchars(getUsuario()['nombre'] ?? 'Ignacio (Front)') ?></span>
        </div>
    </header>

    <section class="content">
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>

        <div class="section-header">
            <h2>Listado de Prácticas</h2>
            <button class="btn-primary" id="btnNuevaPractica">+ Asignar práctica</button>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Tipo de Práctica</th>
                    <th>Centro de Práctica</th>
                    <th>Supervisor Interno</th>
                    <th>Supervisor Externo</th>
                    <th>Estado</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($practicas)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #6b7280;">
                            No hay prácticas registradas
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($practicas as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['estudiante'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($p['tipo'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($p['centro'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($p['supervisor_interno'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($p['supervisor_externo'] ?? 'N/A') ?></td>
                            <td>
                                <?php
                                $estado = $p['estado'] ?? 'Desconocido';
                                $badgeClass = match($estado) {
                                    'En curso' => 'badge-success',
                                    'Pendiente' => 'badge-warning',
                                    default => ''
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($estado) ?></span>
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