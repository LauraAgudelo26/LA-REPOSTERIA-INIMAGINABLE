<?php
// Simular petición GET
$_GET['seccion'] = 'todos';
$_GET['debug'] = '1';

// Incluir la API
include '../api/productos.php';
?>