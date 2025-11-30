
<?php
session_start();
include __DIR__ . '/../config/api_config.php';
include __DIR__ . '/../config/session_helper.php';
requireLogin();

$api = new ApiClient();
$bitacora = $api->getBitacora();
?>
<!DOCTYPE html>
<html>
<head>
    ../assets/styles.css
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main">
    <div class="topbar"><h1>Bitácora</h1></div>
    <div class="content">
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Fecha</th><th>Estudiante</th><th>Práctica</th><th>Logros</th></tr></thead>
                <tbody>
                <?php foreach ($bitacora as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['fechaRegistro']) ?></td>
                        <td><?= htmlspecialchars($b['idEstudiante']) ?></td>
                        <td><?= htmlspecialchars($b['idPractica']) ?></td>
                        <td><?= htmlspecialchars($b['logros']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
