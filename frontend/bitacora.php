<?php
session_start();
require_once 'config/api_config.php';
require_once 'config/session_helper.php';

$pageTitle = "Bitácora de Prácticas – Sistema de Prácticas UNACH";
$activePage = "bitacora";

// TODO: Descomentar cuando el backend esté listo
// requireLogin();

// Datos de ejemplo (temporal)
$entradas = [
    [
        'fecha' => '2025-03-08',
        'estudiante' => 'Juan Pérez',
        'practica' => 'Práctica Profesional',
        'descripcion' => 'Primera visita al centro, presentación al equipo y revisión de funciones.',
        'evidencia' => '#',
    ],
    [
        'fecha' => '2025-03-09',
        'estudiante' => 'Ana Díaz',
        'practica' => 'Práctica I',
        'descripcion' => 'Apoyo en sala y observación de clases de matemática.',
        'evidencia' => '#',
    ],
];

// Cuando el backend esté listo, reemplazar con:
/*
$api = new ApiClient();
$entradas = $api->getBitacora();

if (isset($entradas['error'])) {
    $entradas = [];
    $errorMessage = $entradas['message'] ?? 'Error al cargar bitácora';
}
*/

include 'partials/header.php';
include 'partials/sidebar.php';
?>
<main class="main">
    <header class="topbar">
        <h1>Bitácora de Prácticas</h1>
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
            <h2>Entradas de Bitácora</h2>
            <button class="btn-primary" id="btnNuevaBitacora">+ Nueva entrada</button>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Estudiante</th>
                    <th>Práctica</th>
                    <th>Descripción</th>
                    <th>Evidencia</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($entradas)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #6b7280;">
                            No hay entradas registradas
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($entradas as $e): ?>
                        <tr>
                            <td><?= htmlspecialchars($e['fecha'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($e['estudiante'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($e['practica'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($e['descripcion'] ?? 'N/A') ?></td>
                            <td>
                                <?php if (!empty($e['evidencia']) && $e['evidencia'] !== '#'): ?>
                                    <a href="<?= htmlspecialchars($e['evidencia']) ?>" target="_blank" class="badge badge-success">Ver archivo</a>
                                <?php else: ?>
                                    <span class="badge">Sin archivo</span>
                                <?php endif; ?>
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