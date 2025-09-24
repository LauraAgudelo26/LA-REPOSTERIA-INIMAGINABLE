<?php
/**
 * Controlador de Productos
 * Maneja todas las operaciones relacionadas con productos
 */

// Detectar la ruta correcta hacia config_db.php de forma inteligente
$possible_paths = [
    '../../config/config_db.php',  // Desde productos/controllers/ 
    '../config/config_db.php',     // Desde utils/
    'config/config_db.php',        // Desde raíz del proyecto
    __DIR__ . '/../../config/config_db.php'  // Ruta absoluta
];

$config_loaded = false;
foreach ($possible_paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $config_loaded = true;
        break;
    }
}

if (!$config_loaded) {
    throw new Exception('No se pudo encontrar config_db.php');
}

class ProductoController {
    private $db;
    
    public function __construct() {
        $this->db = DatabaseConnection::getInstance();
    }
    
    /**
     * Obtener todos los productos con sus categorías
     */
    public function obtenerTodosLosProductos() {
        try {
            return $this->db->obtenerProductosPorCategoria();
        } catch (Exception $e) {
            logearActividad("Error al obtener productos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener productos por categoría
     */
    public function obtenerProductosPorCategoria($categoria_id) {
        try {
            return $this->db->obtenerProductosPorCategoria($categoria_id);
        } catch (Exception $e) {
            logearActividad("Error al obtener productos por categoría: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener todas las categorías
     */
    public function obtenerCategorias() {
        try {
            return $this->db->obtenerCategorias();
        } catch (Exception $e) {
            logearActividad("Error al obtener categorías: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener un producto específico por ID
     */
    public function obtenerProductoPorId($id) {
        try {
            $sql = "SELECT * FROM vista_productos_categoria WHERE idProducto = ?";
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            logearActividad("Error al obtener producto por ID: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtener productos destacados (más vendidos o con más stock)
     */
    public function obtenerProductosDestacados($limite = 6) {
        try {
            $sql = "SELECT * FROM vista_productos_categoria 
                    WHERE Stock > 0 
                    ORDER BY Stock DESC 
                    LIMIT ?";
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([$limite]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            logearActividad("Error al obtener productos destacados: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Buscar productos por nombre
     */
    public function buscarProductos($termino) {
        try {
            $sql = "SELECT * FROM vista_productos_categoria 
                    WHERE Producto LIKE ? OR Descripcion LIKE ?
                    ORDER BY Producto";
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare($sql);
            $termino_busqueda = "%$termino%";
            $stmt->execute([$termino_busqueda, $termino_busqueda]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            logearActividad("Error al buscar productos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Verificar disponibilidad de stock
     */
    public function verificarStock($producto_id, $cantidad = 1) {
        try {
            $sql = "SELECT Stock FROM Producto WHERE idProducto = ?";
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([$producto_id]);
            $producto = $stmt->fetch();
            
            if ($producto) {
                return $producto['Stock'] >= $cantidad;
            }
            return false;
        } catch (Exception $e) {
            logearActividad("Error al verificar stock: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener imagen del producto (mapeo con archivos físicos)
     * Sistema mejorado con búsqueda inteligente
     */
    public function obtenerImagenProducto($nombre_producto) {
        // Mapeo generado automáticamente - actualizado 2025-09-24
        $mapeo_imagenes = [
            // Postres
            'Cheesecake Fresa' => 'cheesecake_fresa.jpg',
            'Cheesecake de Fresa Delicioso' => 'cheesecake_fresa.jpg',
            'Cheesecake de Fresa' => 'cheesecake_fresa.jpg',
            'Churros' => 'churros.jpg',
            'Churros Tradicionales' => 'churros.jpg',
            'Cuca' => 'cuca.jpg',
            'Cuca Tradicional' => 'cuca.jpg',
            'Donas' => 'donas.jpg',
            'Donas Glaseadas' => 'donas.jpg',
            'Postre Gelatina' => 'postre_gelatina.jpg',
            'Postre de Gelatina' => 'postre_gelatina.jpg',
            'Postre Limon' => 'postre_limon.jpg',
            'Postre de Limón' => 'postre_limon.jpg',
            'Postre de Limon' => 'postre_limon.jpg',
            'Postre Oreo' => 'postre_oreo.jpg',
            'Postre de Oreo' => 'postre_oreo.jpg',
            'Rollos Canela' => 'rollos_canela.jpg',
            'Rollos de Canela' => 'rollos_canela.jpg',
            
            // Frutas
            'Banano' => 'banano.jpg',
            'Banano Fresco' => 'banano.jpg',
            'Cereza' => 'cereza.jpg',
            'Cerezas Selectas' => 'cereza.jpg',
            'Fresas Crema' => 'fresas_crema.jpg',
            'Fresas con Crema' => 'fresas_crema.jpg',
            'Fresas Marshmallow' => 'fresas_marshmallow.jpg',
            'Fresas con Marshmallow' => 'fresas_marshmallow.jpg',
            'Fresas con Masmelo' => 'fresas_marshmallow.jpg',
            'Manzana' => 'manzana.jpg',
            'Manzanas Rojas' => 'manzana.jpg',
            'Uvas' => 'uvas.jpg',
            'Uvas Frescas' => 'uvas.jpg',
            
            // Pasteles Especiales
            'Mini Tarta Frutas' => 'mini_tarta_frutas.jpg',
            'Mini Tarta de Frutas' => 'mini_tarta_frutas.jpg',
            'Torta Cumpleanos' => 'torta_cumpleanos.jpg',
            'Funny Birthday Cake' => 'torta_cumpleanos.jpg',
            'Birthday Cake' => 'torta_cumpleanos.jpg',
            'Torta Falica' => 'torta_falica.jpg',
            'Torta Fálica' => 'torta_falica.jpg',
            'Torta Lingerie' => 'torta_lingerie.jpg',
            'Lingerie Cake' => 'torta_lingerie.jpg',
            'Torta Soltera' => 'torta_soltera.jpg',
            'Despedida Soltera' => 'torta_soltera.jpg',
            'Torta Despedida Soltera' => 'torta_soltera.jpg',
            'Soltera' => 'torta_soltera.jpg',
            
            // Galletas
            'Galletas Chocolate' => 'galletas_chocolate.jpg',
            'Galletas con Chips de Chocolate' => 'galletas_chocolate.jpg',
            'Galletas con Chips' => 'galletas_chocolate.jpg',
            'Galletas Especiales' => 'galletas_especiales.jpg',
            'Galletas de Sujetadores y Tangas' => 'galletas_especiales.jpg',
            
            // Bebidas
            'Bebidas Refrescantes' => 'bebidas_refrescantes.jpg',
            'Bebidas' => 'bebidas_refrescantes.jpg',
            'Cappuccino' => 'cappuccino.jpg',
            'Cappuccino Artesanal' => 'cappuccino.jpg',
            
            // Otros
            'Pichos' => 'pichos.jpg'
        ];

        // Buscar imagen exacta
        if (isset($mapeo_imagenes[$nombre_producto])) {
            return '../public/img/' . $mapeo_imagenes[$nombre_producto];
        }

        // Buscar coincidencias parciales (búsqueda inteligente)
        foreach ($mapeo_imagenes as $producto => $imagen) {
            if (stripos($producto, $nombre_producto) !== false ||
                stripos($nombre_producto, $producto) !== false) {
                return '../public/img/' . $imagen;
            }
        }

        // Búsqueda por similitud de palabras clave
        $palabras_producto = explode(' ', strtolower($nombre_producto));
        foreach ($mapeo_imagenes as $producto => $imagen) {
            $palabras_mapeo = explode(' ', strtolower($producto));
            $coincidencias = array_intersect($palabras_producto, $palabras_mapeo);
            if (count($coincidencias) >= 1) {
                return '../public/img/' . $imagen;
            }
        }

        // Imagen por defecto
        return '../public/img/logo.jpg';
    }
    
    /**
     * Formatear precio colombiano
     */
    public function formatearPrecio($precio) {
        return '$' . number_format($precio, 0, ',', '.');
    }
}

// API REST para AJAX
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['api'])) {
    header('Content-Type: application/json');
    
    $controller = new ProductoController();
    $action = $_GET['api'];
    
    switch ($action) {
        case 'productos':
            $categoria_id = $_GET['categoria'] ?? null;
            if ($categoria_id) {
                echo json_encode($controller->obtenerProductosPorCategoria($categoria_id));
            } else {
                echo json_encode($controller->obtenerTodosLosProductos());
            }
            break;
            
        case 'categorias':
            echo json_encode($controller->obtenerCategorias());
            break;
            
        case 'producto':
            $id = $_GET['id'] ?? 0;
            echo json_encode($controller->obtenerProductoPorId($id));
            break;
            
        case 'buscar':
            $termino = $_GET['q'] ?? '';
            echo json_encode($controller->buscarProductos($termino));
            break;
            
        case 'destacados':
            $limite = $_GET['limite'] ?? 6;
            echo json_encode($controller->obtenerProductosDestacados($limite));
            break;
            
        default:
            echo json_encode(['error' => 'Acción no válida']);
    }
    exit;
}
?>
