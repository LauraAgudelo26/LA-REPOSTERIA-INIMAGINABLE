<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../config/config_db.php';
require_once '../controllers/controlador_productos.php';

try {
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();
    $controlador = new ProductoController();
    
    // Consultar productos especiales (categoría 2 - Tortas Especiales)
    $sql = "SELECT 
                p.id,
                p.nombre,
                p.descripcion,
                p.precio,
                p.stock,
                p.imagen,
                p.destacado,
                c.nombre as categoria
            FROM producto p
            JOIN categoria c ON p.categoria_id = c.id
            WHERE c.id = 2 AND p.activo = 1 AND p.stock > 0
            ORDER BY p.precio DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatear productos similares a la API principal
    foreach ($productos as &$producto) {
        // Formatear precio en pesos colombianos
        $producto['precio_formateado'] = number_format($producto['precio'], 0, ',', '.');
        
        // Usar el controlador para obtener la imagen correcta
        $imagen_url = $controlador->obtenerImagenProducto($producto['nombre']);
        
        // Verificar si la imagen existe usando la ruta correcta
        $rutaImagen = __DIR__ . '/../../public/img/' . $producto['imagen'];
        $producto['imagen_existe'] = file_exists($rutaImagen);
        $producto['imagen_url'] = $imagen_url;
        
        // Convertir destacado a boolean
        $producto['destacado'] = (bool)$producto['destacado'];
    }
    
    echo json_encode([
        'success' => true,
        'productos' => $productos,
        'total' => count($productos),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener productos especiales',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
