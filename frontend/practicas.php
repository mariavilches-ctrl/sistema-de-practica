
<?php
session_start();
include __DIR__ . '/../config/api_config.php';
include __DIR__ . '/../config/session_helper.php';
requireLogin();

$api = new ApiClient();
$practicas = $api->getPracticas();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main">
    <div class="topbar"><h1>Prácticas Asignadas</h1></div>
    <div class="content">
        <div class="section-header">
            <h2>Listado de Prácticas</h2>
            <button class="btn-primary">+ Asignar práctica</button>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Tipo</th>
                        <th>Centro</th>
                        <th>Tutor</th>
                        <th>Supervisor</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($practicas)): ?>
                    <?php foreach ($practicas as $p): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($p['idEstudiante']) ?></td>
                                                    <td><?= htmlspecialchars($p['tipo']) ?></td>
                                                    <td><?= htmlspecialchars($p['idCentroPractica']) ?></td>
                                                    <td><?= htmlspecialchars($p['tutor']) ?></td>
                                                    <td><?= htmlspecialchars($p['supervisor']) ?></td>
                                                    <td><?= htmlspecialchars($p['estado']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="6">No hay prácticas asignadas</td></tr>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        </body>
                        </html>
