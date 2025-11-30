<?php
session_start(); // Siempre al inicio
require_once 'config/api_config.php';
require_once 'config/session_helper.php';

// 1. BLOQUEAR ACCESO: Si no hay login, adiós.
requireLogin();

$pageTitle = "Dashboard – Sistema de Prácticas UNACH";
$activePage = "dashboard";

// 2. OBTENER DATOS REALES DE PYTHON
$api = new ApiClient();

// Pedimos todas las listas para contar
$listaPracticas = $api->getPracticas();
$listaEstudiantes = $api->getEstudiantes(); // Asegúrate de tener esta función en ApiClient o usa getPracticas
$listaCentros = $api->getCentros(); 

// Calculamos estadísticas reales
$practicasEnCurso = is_array($listaPracticas) ? count($listaPracticas) : 0;
$estudiantesEnPractica = is_array($listaEstudiantes) ? count($listaEstudiantes) : 0;
$centrosPractica = is_array($listaCentros) ? count($listaCentros) : 0;

// Tomamos las últimas 5 prácticas para la tabla (si hay datos)
$practicasRecientes = is_array($listaPracticas) ? array_slice($listaPracticas, 0, 5) : [];

include 'partials/header.php';
include 'partials/sidebar.php';
?>
<main class="main">
    <header class="topbar">
        <h1>Dashboard</h1>
        <div class="user-info">
            <span id="openUserModal" class="user-name-btn">
                <?= htmlspecialchars($_SESSION['usuario']['nombreCompleto'] ?? $_SESSION['usuario']['Email'] ?? 'Usuario') ?>
            </span>
        </div>
    </header>

    <section class="content">
        <div class="cards">
            <div class="card">
                <h3>Estudiantes Registrados</h3>
                <p class="card-number"><?= $estudiantesEnPractica ?></p>
            </div>
            <div class="card">
                <h3>Prácticas Totales</h3>
                <p class="card-number"><?= $practicasEnCurso ?></p>
            </div>
            <div class="card">
                <h3>Centros de Práctica</h3>
                <p class="card-number"><?= $centrosPractica ?></p>
            </div>
        </div>

        <div class="section-header">
            <h2>Últimas prácticas registradas</h2>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>ID Estudiante</th>
                    <th>Tipo</th>
                    <th>ID Centro</th>
                    <th>Fechas</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($practicasRecientes)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #6b7280;">
                            No hay prácticas registradas en la Base de Datos.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($practicasRecientes as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['idEstudiante'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($p['tipo'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($p['idCentroPractica'] ?? 'N/A') ?></td>
                            <td>
                                <span class="badge badge-success">
                                    <?= htmlspecialchars($p['fechaDeInicio'] ?? '') ?> 
                                </span>
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