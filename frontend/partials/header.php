<?php
// partials/header.php
if (!isset($pageTitle)) {
    $pageTitle = "Sistema de Prácticas UNACH";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <!-- Note: folder on disk is `assets/CSS/` (Windows is case-insensitive, servers may not be) -->
    <link rel="stylesheet" href="assets/CSS/styles.css">
</head>
<body>
<div class="app">
