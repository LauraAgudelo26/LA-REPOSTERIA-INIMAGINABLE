<?php
echo "=== TEST DE PRODUCTOS PROBLEMÁTICOS ===\n\n";

require_once '../config/config_db.php';
require_once '../productos/controllers/controlador_productos.php';

$controlador = new ProductoController();

// Productos que sabemos que tenían problemas
$productos_problema = [
    'Fresas Marshmallow',
    'Bebidas Refrescantes', 
    'Donas',
    'Churros',
    'Banano'
];

foreach ($productos_problema as $producto) {
    $imagen_url = $controlador->obtenerImagenProducto($producto);
    $archivo_imagen = basename($imagen_url);
    $ruta_completa = __DIR__ . '/../public/img/' . $archivo_imagen;
    $existe = file_exists($ruta_completa);
    
    echo "Producto: $producto\n";
    echo "  → URL: $imagen_url\n";
    echo "  → Archivo: $archivo_imagen\n";
    echo "  → Existe: " . ($existe ? '✅ SÍ' : '❌ NO') . "\n";
    echo "  → Ruta: $ruta_completa\n\n";
}

echo "=== ESTADÍSTICAS ===\n";
$db = DatabaseConnection::getInstance();
$pdo = $db->getConnection();

$stmt = $pdo->query("SELECT COUNT(*) as total FROM producto WHERE activo = 1");
$total = $stmt->fetch()['total'];

echo "Total productos activos: $total\n";

// Contar cuántos tienen imagen que existe
$productos_con_imagen = 0;
$stmt = $pdo->query("SELECT nombre FROM producto WHERE activo = 1");
while ($row = $stmt->fetch()) {
    $imagen_url = $controlador->obtenerImagenProducto($row['nombre']);
    $archivo_imagen = basename($imagen_url);
    $ruta_completa = __DIR__ . '/../public/img/' . $archivo_imagen;
    if (file_exists($ruta_completa)) {
        $productos_con_imagen++;
    }
}

echo "Productos con imagen existente: $productos_con_imagen\n";
echo "Productos sin imagen: " . ($total - $productos_con_imagen) . "\n";

if ($productos_con_imagen == $total) {
    echo "\n🎉 ¡TODOS LOS PRODUCTOS TIENEN IMAGEN!\n";
} else {
    echo "\n⚠️  AÚN HAY PRODUCTOS SIN IMAGEN\n";
}
?>