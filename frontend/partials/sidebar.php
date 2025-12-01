<?php
if (!isset($activePage)) {
    $activePage = '';
}
?>
<aside class="sidebar">
    <div class="logo">
        <h2><i class="fas fa-university"></i> Prácticas UNACH</h2>
    </div>
    <nav class="menu">
        <a href="index.php" class="menu-item <?= $activePage === 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-home"></i> Dashboard
        </a>
        
        <a href="practicas.php" class="menu-item <?= $activePage === 'practicas' ? 'active' : '' ?>">
            <i class="fas fa-briefcase"></i> Prácticas Asignadas
        </a>
        
        <a href="centros.php" class="menu-item <?= $activePage === 'centros' ? 'active' : '' ?>">
            <i class="fas fa-building"></i> Centros de Práctica
        </a>

        <a href="alumno.php" class="menu-item <?= $activePage === 'alumnos' ? 'active' : '' ?>">
            <i class="fas fa-user-graduate"></i> Estudiantes
        </a>

        <a href="tipos.php" class="menu-item <?= $activePage === 'tipos' ? 'active' : '' ?>">
            <i class="fas fa-shapes"></i> Tipos de Práctica
        </a>

        <a href="calendario.php" class="menu-item <?= $activePage === 'calendario' ? 'active' : '' ?>">
            <i class="fas fa-calendar-alt"></i> Calendarización
        </a>
        
        <a href="bitacora.php" class="menu-item <?= $activePage === 'bitacora' ? 'active' : '' ?>">
            <i class="fas fa-book"></i> Bitácora
        </a>

        <a href="seguimiento.php" class="menu-item <?= $activePage === 'seguimiento' ? 'active' : '' ?>">
            <i class="fas fa-eye"></i> Seguimiento
        </a>

        <div style="margin-top: 2rem; border-top: 1px solid #374151; padding-top: 1rem;">
            <a href="config/logout.php" class="menu-item" style="color: #f87171;">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </div>
    </nav>
</aside>
