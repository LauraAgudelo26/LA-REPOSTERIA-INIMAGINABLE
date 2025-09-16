<?php
/**
 * API para obtener categorías de la base de datos
 * api_categorias.php
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../../config/config_db.php';

try {
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();
    
    // Obtener categorías activas (EXCLUYENDO Tortas Especiales - categoría 2)
    $sql = "
        SELECT 
            c.id,
            c.nombre,
            c.descripcion,
            c.imagen,
            c.orden_mostrar,
            COUNT(p.id) as total_productos
        FROM categoria c
        LEFT JOIN producto p ON c.id = p.categoria_id AND p.activo = 1
        WHERE c.activa = 1 AND c.id != 2
        GROUP BY c.id, c.nombre, c.descripcion, c.imagen, c.orden_mostrar
        ORDER BY c.orden_mostrar ASC, c.nombre ASC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $categorias = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'categorias' => $categorias,
        'total' => count($categorias),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener categorías',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
