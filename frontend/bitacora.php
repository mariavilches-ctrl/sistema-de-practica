<?php
$pageTitle  = "Bitácora de Prácticas – Sistema de Prácticas UNACH";
$activePage = "bitacora";
// session_start();

$entradas = [
    [
        'fecha'      => '2025-03-08',
        'estudiante' => 'Juan Pérez',
        'practica'   => 'Práctica Profesional',
        'descripcion'=> 'Primera visita al centro, presentación al equipo y revisión de funciones.',
        'evidencia'  => '#',
    ],
    [
        'fecha'      => '2025-03-09',
        'estudiante' => 'Ana Díaz',
        'practica'   => 'Práctica I',
        'descripcion'=> 'Apoyo en sala y observación de clases de matemática.',
        'evidencia'  => '#',
    ],
];

include 'partials/header.php';
include 'partials/sidebar.php';
?>
<main class="main">
    <header class="topbar">
        <h1>Bitácora de Prácticas</h1>
        <div class="user-info">
            <span>Ignacio (Front)</span>
        </div>
    </header>

    <section class="content">
        <div class="section-header">
            <h2>Entradas de Bitácora</h2>
            <button class="btn-primary">+ Nueva entrada</button>
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
                <?php foreach ($entradas as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['fecha']) ?></td>
                        <td><?= htmlspecialchars($e['estudiante']) ?></td>
                        <td><?= htmlspecialchars($e['practica']) ?></td>
                        <td><?= htmlspecialchars($e['descripcion']) ?></td>
                        <td>
                            <span class="badge">Sin archivo</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php include 'partials/footer.php'; ?>
