<?php
// partials/sidebar.php
if (!isset($activePage)) {
    $activePage = '';
}
?>
<aside class="sidebar">
    <div class="logo">
        <h2>Prácticas UNACH</h2>
    </div>
    <nav class="menu">
        <a href="index.php" class="menu-item <?= $activePage === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
        <a href="practicas.php" class="menu-item <?= $activePage === 'practicas' ? 'active' : '' ?>">Prácticas Asignadas</a>
        <a href="calendario.php" class="menu-item <?= $activePage === 'calendario' ? 'active' : '' ?>">Calendario</a>
        <a href="bitacora.php" class="menu-item <?= $activePage === 'bitacora' ? 'active' : '' ?>">Bitácora</a>
        <!-- Más adelante pueden agregar Estudiantes, Reportes, etc. -->
    </nav>
</aside>
