<?php
session_start();
session_unset();
session_destroy();
// Redirige al login que está dos carpetas arriba (desde config/logout.php -> ../login.php)
header("Location: ../login.php");
exit;
?>