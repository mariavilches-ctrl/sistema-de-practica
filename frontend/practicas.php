<?php
$pageTitle  = "Prácticas Asignadas – Sistema de Prácticas UNACH";
$activePage = "practicas";
// session_start();

// Datos de ejemplo (luego BD)
$practicas = [
    [
        'estudiante'         => 'Juan Pérez',
        'tipo'               => 'Práctica Profesional',
        'centro'             => 'Hospital Clínico Chillán',
        'supervisor_interno' => 'Prof. Soto',
        'supervisor_externo' => 'Dr. Morales',
        'estado'             => 'En curso',
    ],
    [
        'estudiante'         => 'Ana Díaz',
        'tipo'               => 'Práctica I',
        'centro'             => 'Colegio Adventista',
        'supervisor_interno' => 'Prof. García',
        'supervisor_externo' => 'Sr. López',
        'estado'             => 'Pendiente',
    ],
];

include 'partials/header.php';
include 'partials/sidebar.php';
?>
<main class="main">
    <header class="topbar">
        <h1>Prácticas Asignadas</h1>
        <div class="user-info">
            <span>Ignacio (Front)</span>
        </div>
    </header>

    <section class="content">
        <div class="section-header">
            <h2>Listado de Prácticas</h2>
            <button class="btn-primary">+ Asignar práctica</button>
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
                <?php foreach ($practicas as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['estudiante']) ?></td>
                        <td><?= htmlspecialchars($p['tipo']) ?></td>
                        <td><?= htmlspecialchars($p['centro']) ?></td>
                        <td><?= htmlspecialchars($p['supervisor_interno']) ?></td>
                        <td><?= htmlspecialchars($p['supervisor_externo']) ?></td>
                        <td>
                            <?php if ($p['estado'] === 'En curso'): ?>
                                <span class="badge badge-success">En curso</span>
                            <?php elseif ($p['estado'] === 'Pendiente'): ?>
                                <span class="badge badge-warning">Pendiente</span>
                            <?php else: ?>
                                <span class="badge"><?= htmlspecialchars($p['estado']) ?></span>
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
