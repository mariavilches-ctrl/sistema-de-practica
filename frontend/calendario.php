<?php
$pageTitle  = "Calendario de Sesiones – Sistema de Prácticas UNACH";
$activePage = "calendario";
// session_start();

$sesiones = [
    [
        'fecha'        => '2025-03-10',
        'hora_inicio'  => '09:00',
        'hora_termino' => '11:00',
        'estudiante'   => 'Juan Pérez',
        'tipo'         => 'Práctica Profesional',
        'actividad'    => 'Inducción en el centro',
        'estado'       => 'Programada',
    ],
    [
        'fecha'        => '2025-03-12',
        'hora_inicio'  => '14:00',
        'hora_termino' => '17:00',
        'estudiante'   => 'Ana Díaz',
        'tipo'         => 'Práctica I',
        'actividad'    => 'Observación de clases',
        'estado'       => 'Realizada',
    ],
];

include 'partials/header.php';
include 'partials/sidebar.php';
?>
<main class="main">
    <header class="topbar">
        <h1>Calendario de Sesiones</h1>
        <div class="user-info">
            <span>Ignacio (Front)</span>
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
                    <th>Hora Inicio</th>
                    <th>Hora Término</th>
                    <th>Estudiante</th>
                    <th>Tipo de Práctica</th>
                    <th>Actividad</th>
                    <th>Estado</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($sesiones as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['fecha']) ?></td>
                        <td><?= htmlspecialchars($s['hora_inicio']) ?></td>
                        <td><?= htmlspecialchars($s['hora_termino']) ?></td>
                        <td><?= htmlspecialchars($s['estudiante']) ?></td>
                        <td><?= htmlspecialchars($s['tipo']) ?></td>
                        <td><?= htmlspecialchars($s['actividad']) ?></td>
                        <td>
                            <?php if ($s['estado'] === 'Realizada'): ?>
                                <span class="badge badge-success">Realizada</span>
                            <?php elseif ($s['estado'] === 'Programada'): ?>
                                <span class="badge badge-warning">Programada</span>
                            <?php else: ?>
                                <span class="badge"><?= htmlspecialchars($s['estado']) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php include 'partials/footer.php'; ?>