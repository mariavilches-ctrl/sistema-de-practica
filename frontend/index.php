<?php
$pageTitle  = "Dashboard – Sistema de Prácticas UNACH";
$activePage = "dashboard";

// session_start(); // más adelante para validar login

// Datos de ejemplo
$estudiantesEnPractica = 24;
$practicasEnCurso      = 12;
$centrosPractica       = 8;

include 'partials/header.php';
include 'partials/sidebar.php';
?>
<main class="main">
    <header class="topbar">
        <h1>Dashboard</h1>
        <div class="user-info">
            <span>Ignacio (Front)</span>
        </div>
    </header>

    <section class="content">
        <div class="cards">
            <div class="card">
                <h3>Estudiantes en práctica</h3>
                <p class="card-number"><?= $estudiantesEnPractica ?></p>
            </div>
            <div class="card">
                <h3>Prácticas en curso</h3>
                <p class="card-number"><?= $practicasEnCurso ?></p>
            </div>
            <div class="card">
                <h3>Centros de práctica</h3>
                <p class="card-number"><?= $centrosPractica ?></p>
            </div>
        </div>

        <div class="section-header">
            <h2>Últimas prácticas asignadas</h2>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Tipo de Práctica</th>
                    <th>Centro</th>
                    <th>Estado</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Juan Pérez</td>
                    <td>Práctica Profesional</td>
                    <td>Hospital Clínico</td>
                    <td><span class="badge badge-success">En curso</span></td>
                </tr>
                <tr>
                    <td>Ana Díaz</td>
                    <td>Práctica I</td>
                    <td>Colegio Adventista</td>
                    <td><span class="badge badge-warning">Pendiente</span></td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php include 'partials/footer.php'; ?>
