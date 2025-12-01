<?php
session_start();
require_once 'config/api_config.php';
require_once 'config/session_helper.php';


requireLogin();

$pageTitle = "Dashboard – Sistema de Prácticas UNACH";
$activePage = "dashboard";


$api = new ApiClient();


$listaPracticas = $api->getPracticas();
$listaEstudiantes = $api->getEstudiantes();
$listaCentros = $api->getCentros(); 

// Contadores 
$practicasEnCurso = is_array($listaPracticas) ? count($listaPracticas) : 0;
$estudiantesEnPractica = is_array($listaEstudiantes) ? count($listaEstudiantes) : 0;
$centrosPractica = is_array($listaCentros) ? count($listaCentros) : 0;


$practicasRecientes = is_array($listaPracticas) ? array_slice($listaPracticas, 0, 5) : [];


include 'partials/header.php';
include 'partials/sidebar.php'; 
?>

<main class="main">
    <header class="topbar">
        <h1>Dashboard</h1>
        <div class="user-info">
            <?= htmlspecialchars(getUsuario()['nombreCompleto'] ?? getUsuario()['Email'] ?? 'Usuario') ?>
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
                    <th>Estudiante</th>
                    <th>Tipo</th>
                    <th>Centro</th>
                    <th>Fechas</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($practicasRecientes)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #6b7280; padding: 20px;">
                            No hay prácticas registradas todavía.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($practicasRecientes as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['NombreEstudiante'] ?? 'Desconocido') ?></td>
                            <td>
                                <span class="badge badge-warning"><?= htmlspecialchars($p['tipo'] ?? '-') ?></span>
                            </td>
                            <td><?= htmlspecialchars($p['NombreCentro'] ?? 'Desconocido') ?></td>
                            <td>
                                <small class="badge badge-success">
                                    <?= htmlspecialchars($p['fechaDeInicio'] ?? '') ?> 
                                </small>
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