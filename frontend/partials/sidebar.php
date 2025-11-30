<?php
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
        <a href="calendario.php" class="menu-item <?= $activePage === 'calendario' ? 'active' : '' ?>">Calendarización</a>
        <a href="bitacora.php" class="menu-item <?= $activePage === 'bitacora' ? 'active' : '' ?>">Bitácora</a>
        <a href="centros.php" class="menu-item <?= $activePage === 'centros' ? 'active' : '' ?>">Centros de Práctica</a>
        <a href="tipos.php" class="menu-item <?= $activePage === 'tipos' ? 'active' : '' ?>">Tipos de Práctica</a>
        <a href="seguimiento.php" class="menu-item <?= $activePage === 'seguimiento' ? 'active' : '' ?>">Seguimiento</a>
    </nav>
</aside>