<?php
/**
 * Prueba rápida del sistema de imágenes
 */

require_once '../config/config_db.php';
require_once '../productos/controllers/controlador_productos.php';

echo "=== PRUEBA RÁPIDA DEL SISTEMA DE IMÁGENES ===\n\n";

$controlador = new ProductoController();

// Probar algunos productos
$productos_prueba = [
    'Soltera',
    'Despedida Soltera', 
    'Torta para Despedida de Soltera',
    'Cheesecake de Fresa',
    'Cappuccino',
    'Producto Inexistente'
];

foreach ($productos_prueba as $producto) {
    $imagen_url = $controlador->obtenerImagenProducto($producto);
    $ruta_completa = __DIR__ . '/' . $imagen_url;
    $existe = file_exists($ruta_completa) ? '✅' : '❌';
    
    echo "$existe Producto: '$producto'\n";
    echo "   → Imagen: $imagen_url\n";
    echo "   → Archivo: " . basename($imagen_url) . "\n\n";
}

echo "=== PRUEBA DE API ===\n";
echo "URL para probar: http://localhost/proyecto/productos/api/api_productos_especiales.php\n";
echo "El API ahora debería devolver imagen_url correctas.\n";
?>