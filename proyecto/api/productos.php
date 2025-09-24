<?php
/**
 * API UNIFICADA DE PRODUCTOS
 * Una sola API para manejar todos los productos de La Repostería Inimaginable
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/config_db.php';
require_once '../productos/controllers/controlador_productos.php';

try {
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();
    $controlador = new ProductoController();
    
    // Obtener parámetros
    $seccion = $_GET['seccion'] ?? 'todos';
    $debug = isset($_GET['debug']) ? true : false;
    
    $response = [
        'success' => true,
        'timestamp' => date('Y-m-d H:i:s'),
        'seccion' => $seccion,
        'productos' => [],
        'total' => 0
    ];
    
    if ($debug) {
        $response['debug'] = [
            'directorio_img' => realpath(__DIR__ . '/../public/img/'),
            'controlador_cargado' => class_exists('ProductoController'),
            'base_url_imagenes' => '../public/img/'
        ];
    }
    
    // Query base
    $sql_base = "SELECT 
                    p.id,
                    p.nombre,
                    p.descripcion,
                    p.precio,
                    p.stock,
                    p.imagen,
                    p.destacado,
                    p.activo,
                    c.id as categoria_id,
                    c.nombre as categoria
                FROM producto p
                JOIN categoria c ON p.categoria_id = c.id
                WHERE p.activo = 1 AND p.stock > 0";
    
    // Filtros por sección
    switch ($seccion) {
        case 'especiales':
            $sql = $sql_base . " AND c.id = 2 ORDER BY p.precio DESC";
            break;
            
        case 'normales':
            $sql = $sql_base . " AND c.id != 2 ORDER BY p.destacado DESC, p.nombre ASC";
            break;
            
        case 'categoria':
            $categoria_id = intval($_GET['categoria_id'] ?? 1);
            $sql = $sql_base . " AND c.id = ? ORDER BY p.nombre ASC";
            break;
            
        case 'destacados':
            $sql = $sql_base . " AND p.destacado = 1 ORDER BY p.precio DESC";
            break;
            
        case 'todos':
        default:
            $sql = $sql_base . " ORDER BY p.destacado DESC, c.id ASC, p.nombre ASC";
            break;
    }
    
    // Ejecutar query
    if ($seccion === 'categoria' && isset($categoria_id)) {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$categoria_id]);
    } else {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }
    
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['total'] = count($productos);
    
    // Procesar cada producto
    foreach ($productos as $producto) {
        // Usar el controlador para obtener la imagen correcta
        $imagen_url = $controlador->obtenerImagenProducto($producto['nombre']);
        
        // Verificar que la imagen existe
        $ruta_imagen_absoluta = __DIR__ . '/../public/img/' . basename($imagen_url);
        $imagen_existe = file_exists($ruta_imagen_absoluta);
        
        // Formatear producto
        $producto_formateado = [
            'id' => intval($producto['id']),
            'nombre' => $producto['nombre'],
            'descripcion' => $producto['descripcion'],
            'precio' => floatval($producto['precio']),
            'precio_formateado' => '$' . number_format($producto['precio'], 0, ',', '.'),
            'stock' => intval($producto['stock']),
            'imagen' => basename($imagen_url), // Solo el nombre del archivo
            'imagen_url' => $imagen_url, // URL completa
            'imagen_existe' => $imagen_existe,
            'destacado' => (bool)$producto['destacado'],
            'categoria' => [
                'id' => intval($producto['categoria_id']),
                'nombre' => $producto['categoria']
            ]
        ];
        
        // Información de debug si se solicita
        if ($debug) {
            $producto_formateado['debug'] = [
                'imagen_original_bd' => $producto['imagen'],
                'ruta_absoluta' => $ruta_imagen_absoluta,
                'archivo_existe_fisicamente' => file_exists($ruta_imagen_absoluta),
                'tamaño_archivo' => file_exists($ruta_imagen_absoluta) ? filesize($ruta_imagen_absoluta) : 0
            ];
        }
        
        $response['productos'][] = $producto_formateado;
    }
    
    // Estadísticas adicionales
    $response['estadisticas'] = [
        'total_productos' => $response['total'],
        'productos_con_imagen' => count(array_filter($response['productos'], function($p) { return $p['imagen_existe']; })),
        'productos_destacados' => count(array_filter($response['productos'], function($p) { return $p['destacado']; }))
    ];
    
    // Si es debug, agregar más información
    if ($debug) {
        $response['debug']['secciones_disponibles'] = [
            'todos' => 'Todos los productos',
            'especiales' => 'Solo productos especiales (categoría 2)',
            'normales' => 'Productos normales (no especiales)',
            'destacados' => 'Solo productos destacados',
            'categoria' => 'Por categoría específica (usar ?categoria_id=X)'
        ];
        
        $response['debug']['ejemplos_uso'] = [
            'Todos los productos' => '?seccion=todos',
            'Productos especiales' => '?seccion=especiales',
            'Productos normales' => '?seccion=normales',
            'Productos destacados' => '?seccion=destacados',
            'Por categoría' => '?seccion=categoria&categoria_id=1',
            'Con debug' => '?seccion=todos&debug=1'
        ];
    }
    
} catch (PDOException $e) {
    $response = [
        'success' => false,
        'error' => 'Error de base de datos',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ];
} catch (Exception $e) {
    $response = [
        'success' => false,
        'error' => 'Error general',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ];
}

// Respuesta JSON
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>