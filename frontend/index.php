<?php
session_start();
require_once 'config/api_config.php';
require_once 'config/session_helper.php';

$pageTitle = "Dashboard – Sistema de Prácticas UNACH";
$activePage = "dashboard";

// TODO: Descomentar cuando el backend esté listo
// requireLogin();

// Datos de ejemplo (temporal - se reemplazará con API)
$estudiantesEnPractica = 24;
$practicasEnCurso = 12;
$centrosPractica = 8;
$practicasRecientes = [
    ['estudiante' => 'Juan Pérez', 'tipo' => 'Práctica Profesional', 'centro' => 'Hospital Clínico', 'estado' => 'En curso'],
    ['estudiante' => 'Ana Díaz', 'tipo' => 'Práctica I', 'centro' => 'Colegio Adventista', 'estado' => 'Pendiente'],
];

// Cuando el backend esté listo, reemplazar con:
/*
$api = new ApiClient();
$stats = $api->getDashboardStats();
$practicasRecientes = $api->getPracticas();

if (isset($stats['error'])) {
    $estudiantesEnPractica = 0;
    $practicasEnCurso = 0;
    $centrosPractica = 0;
} else {
    $estudiantesEnPractica = $stats['estudiantesEnPractica'] ?? 0;
    $practicasEnCurso = $stats['practicasEnCurso'] ?? 0;
    $centrosPractica = $stats['centrosPractica'] ?? 0;
}
*/

include 'partials/header.php';
include 'partials/sidebar.php';
?>
<main class="main">
    <header class="topbar">
        <h1>Dashboard</h1>
        <div class="user-info">
            <span id="openUserModal" class="user-name-btn">
                <?= htmlspecialchars(getUsuario()['nombre'] ?? 'Ignacio (Front)') ?>
            </span>
        </div>
    </header>

    <section class="content">
        <div class="cards">
            <div class="card">
                <h3>Estudiantes en práctica</h3>
                <p class="card-number"><?= $estudiantesEnPractica ?></p>
            </div>
            <div class="card">
                <h3>Prácticas en curso</h3>
                <p class="card-number"><?= $practicasEnCurso ?></p>
            </div>
            <div class="card">
                <h3>Centros de práctica</h3>
                <p class="card-number"><?= $centrosPractica ?></p>
            </div>
        </div>

        <div class="section-header">
            <h2>Últimas prácticas asignadas</h2>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Tipo de Práctica</th>
                    <th>Centro</th>
                    <th>Estado</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($practicasRecientes)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #6b7280;">
                            No hay prácticas registradas
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($practicasRecientes as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['estudiante']) ?></td>
                            <td><?= htmlspecialchars($p['tipo']) ?></td>
                            <td><?= htmlspecialchars($p['centro']) ?></td>
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

<!-- Modal de usuario (agregar aquí) -->
<?php include 'partials/footer.php'; ?>