
<?php
session_start();
include __DIR__ . '/../config/api_config.php';
include __DIR__ . '/../config/session_helper.php';
requireLogin();

$api = new ApiClient();
$sesiones = $api->getSesiones();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href>
<body>
<?php include 'sidebar.php'; ?>
<div class="main">
    <div class="topbar"><h1>Calendario</h1></div>
    <div class="content">
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Fecha</th><th>Inicio</th><th>Término</th><th>Actividad</th></tr></thead>
                <tbody>
                <?php foreach ($sesiones as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['fecha']) ?></td>
                        <td><?= htmlspecialchars($s['horaInicio']) ?></td>
                        <td><?= htmlspecialchars($s['horaTermino']) ?></td>
                        <td><?= htmlspecialchars($s['actividad']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>

