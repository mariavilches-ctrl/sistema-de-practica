
<?php
session_start();
include __DIR__ . '/../config/api_config.php';
include __DIR__ . '/../config/session_helper.php';
requireLogin();

$api = new ApiClient();
$practicas = $api->getPracticas();
$sesiones = $api->getSesiones();
?>
<!DOCTYPE html>
<html>
<head>
    ../assets/styles.css
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main">
    <div class="topbar">
        <h1>Dashboard</h1>
        <div class="user-info"><?= htmlspecialchars(getUsuario()['email'] ?? 'Usuario') ?></div>
    </div>
    <div class="content">
        <div class="cards">
            <div class="card"><h3>Prácticas</h3><div class="card-number"><?= count($practicas) ?></div></div>
            <div class="card"><h3>Sesiones</h3><div class="card-number"><?= count($sesiones) ?></div></div>
        </div>
        <h3>Últimas prácticas</h3>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Estudiante</th><th>Tipo</th><th>Centro</th><th>Estado</th></tr></thead>
                <tbody>
                <?php foreach ($practicas as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['idEstudiante']) ?></td>
                        <td><?= htmlspecialchars($p['tipo']) ?></td>
                        <td><?= htmlspecialchars($p['idCentroPractica']) ?></td>
                        <td><span class="badge"><?= htmlspecialchars($p['estado'] ?? 'Pendiente') ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
