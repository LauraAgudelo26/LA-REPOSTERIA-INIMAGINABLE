<?php
// Simular petición GET para solo productos problemáticos
$_GET['seccion'] = 'todos';
$_GET['debug'] = '1';

// Incluir la API
include '../api/productos.php';

// No hacer echo del JSON completo, solo el estado de imágenes
?>