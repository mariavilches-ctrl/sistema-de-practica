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
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<div class="app">
