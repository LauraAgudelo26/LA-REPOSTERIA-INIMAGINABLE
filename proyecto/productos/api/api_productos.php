<?php
/**
 * API para obtener productos de la base de datos
 * api_productos.php
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../../config/config_db.php';

try {
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();
    
    // Obtener productos activos con información de categoría (EXCLUYENDO Tortas Especiales)
    $sql = "
        SELECT 
            p.id,
            p.nombre,
            p.descripcion,
            p.precio,
            p.stock,
            p.imagen,
            p.destacado,
            p.activo,
            c.nombre as categoria_nombre,
            c.id as categoria_id
        FROM producto p
        LEFT JOIN categoria c ON p.categoria_id = c.id
        WHERE p.activo = 1 AND p.stock > 0 AND c.id != 2
        ORDER BY p.destacado DESC, p.nombre ASC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $productos = $stmt->fetchAll();
    
    // Formatear precios y verificar imágenes
    foreach ($productos as &$producto) {
        // Formatear precio en pesos colombianos
        $producto['precio_formateado'] = number_format($producto['precio'], 0, ',', '.');
        
        // Verificar si la imagen existe en el directorio
        if (!empty($producto['imagen'])) {
            $rutaImagen = __DIR__ . '/../../public/img/' . $producto['imagen'];
            $producto['imagen_existe'] = file_exists($rutaImagen);
            $producto['imagen_url'] = '../img/' . $producto['imagen'];
        } else {
            $producto['imagen_existe'] = false;
            $producto['imagen_url'] = '../img/placeholder.svg';
        }
        
        // Agregar placeholder si no existe la imagen
        if (!$producto['imagen_existe']) {
            $producto['imagen_url'] = '../img/placeholder.svg';
        }
        
        // Convertir destacado a boolean
        $producto['destacado'] = (bool)$producto['destacado'];
        $producto['activo'] = (bool)$producto['activo'];
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
        'error' => 'Error al obtener productos',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
