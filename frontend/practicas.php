<?php
session_start();
// Ajusta la ruta según tu carpeta real
include __DIR__ . '/../config/api_config.php';
include __DIR__ . '/../config/session_helper.php';

requireLogin();

$api = new ApiClient();
$mensaje = '';

// --- 1. PROCESAR EL GUARDADO (Si se envió el formulario) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear') {
    
    // Recoger datos del formulario
    $datosFormulario = [
        'idEstudiante'      => $_POST['idEstudiante'],
        'idCentroPractica'  => $_POST['idCentroPractica'],
        'idTutor'           => $_POST['idTutor'],
        'idSupervisor'      => $_POST['idSupervisor'],
        'tipo'              => $_POST['tipo'],
        'fechaInicio'       => $_POST['fechaInicio'],
        'fechaTermino'      => $_POST['fechaTermino'],
        'actividades'       => $_POST['actividades'],
        'evidenciaImg'      => '' // Lo dejamos vacío por ahora
    ];

    // Enviar a Python
    $respuesta = $api->createPractica($datosFormulario);

    if (isset($respuesta['success']) && $respuesta['success']) {
        $mensaje = "✅ ¡Práctica creada exitosamente!";
    } else {
        $mensaje = "❌ Error: " . ($respuesta['message'] ?? 'No se pudo guardar');
    }
}

// --- 2. OBTENER LISTA ACTUALIZADA ---
$practicas = $api->getPracticas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Prácticas</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>

<div class="app">
    <?php 
        if(file_exists('partials/sidebar.php')) include 'partials/sidebar.php';
        else include 'sidebar.php'; 
    ?>

    <div class="main">
        <div class="topbar">
            <h1>Gestión de Prácticas</h1>
            <div class="user-info"><?= htmlspecialchars(getUsuario()['email'] ?? 'Usuario') ?></div>
        </div>
        
        <div class="content">
            
            <?php if ($mensaje): ?>
                <div class="alert <?= strpos($mensaje, '✅') !== false ? 'alert-success' : 'alert-error' ?>">
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>

            <div class="section-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
                <h2>Listado de Prácticas</h2>
                <button class="btn-primary" onclick="abrirModal()">+ Asignar práctica</button>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID Estudiante</th>
                            <th>Tipo</th>
                            <th>ID Centro</th>
                            <th>Fecha Inicio</th>
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
                                <td><?= htmlspecialchars(substr($p['fechaDeInicio'] ?? '', 0, 10)) ?></td>
                                <td><span class="badge">Activa</span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center;">No hay prácticas registradas.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="miModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="cerrarModal()">&times;</span>
        <h2>Nueva Asignación</h2>
        <hr style="margin-bottom:15px; border:0; border-top:1px solid #eee;">
        
        <form method="POST" action="practicas.php">
            <input type="hidden" name="accion" value="crear">

            <div class="form-group">
                <label>ID Estudiante (Número):</label>
                <input type="number" name="idEstudiante" required placeholder="Ej: 1">
            </div>

            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1">
                    <label>ID Centro:</label>
                    <input type="number" name="idCentroPractica" required placeholder="Ej: 5">
                </div>
                <div class="form-group" style="flex:1">
                    <label>Tipo:</label>
                    <select name="tipo">
                        <option value="Práctica I">Práctica I</option>
                        <option value="Práctica Profesional">Práctica Profesional</option>
                    </select>
                </div>
            </div>

            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1">
                    <label>ID Tutor:</label>
                    <input type="number" name="idTutor" required placeholder="Ej: 1">
                </div>
                <div class="form-group" style="flex:1">
                    <label>ID Supervisor:</label>
                    <input type="number" name="idSupervisor" required placeholder="Ej: 1">
                </div>
            </div>

            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1">
                    <label>Inicio:</label>
                    <input type="date" name="fechaInicio" required>
                </div>
                <div class="form-group" style="flex:1">
                    <label>Término:</label>
                    <input type="date" name="fechaTermino" required>
                </div>
            </div>

            <div class="form-group">
                <label>Actividades:</label>
                <textarea name="actividades" rows="2" placeholder="Descripción breve..."></textarea>
            </div>

            <button type="submit" class="btn-save">Guardar Práctica</button>
        </form>
    </div>
</div>

<script>
    // Referencias
    var modal = document.getElementById("miModal");

    // Abrir
    function abrirModal() {
        modal.style.display = "block";
    }

    // Cerrar
    function cerrarModal() {
        modal.style.display = "none";
    }

    // Cerrar si clic fuera
    window.onclick = function(event) {
        if (event.target == modal) {
            cerrarModal();
        }
    }
</script>

</body>
</html>