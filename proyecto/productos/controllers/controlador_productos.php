<?php
/**
 * Controlador de Productos
 * Maneja todas las operaciones relacionadas con productos
 */

require_once '../../config/config_db.php';

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
     */
    public function obtenerImagenProducto($nombre_producto) {
        // Mapeo de nombres de productos con imágenes
        $mapeo_imagenes = [
            // Postres
            'Cheesecake de Fresa Delicioso' => 'Cheesecake de Fresa Delicioso.jpg',
            'Churros Tradicionales' => 'churros.jpg',
            'Donas Glaseadas' => 'donas.jpg',
            'Postre de Gelatina' => 'postre de gelatina.jpg',
            'Postre de Limón' => 'postre de limon.jpg',
            'Postre de Oreo' => 'postre de oreo.jfif',
            'Rollos de Canela' => 'rollos de canela.jpg',
            'Cuca Tradicional' => 'cuca.jfif',
            
            // Frutas
            'Fresas con Crema' => 'fresas con crema.jpg',
            'Fresas con Marshmallow' => 'fresas con masmelo.jpeg',
            'Banano Fresco' => 'banano.jpg',
            'Cerezas Selectas' => 'cereza.jpeg',
            'Manzanas Rojas' => 'manzana.jpeg',
            'Uvas Frescas' => 'uvas.jpeg',
            
            // Pasteles Especiales
            'Torta para Despedida de Soltera' => 'torta para despedida de soltera___.jpg',
            'Mini Tarta de Frutas' => 'mini tarta de frutas.jpg',
            'Funny Birthday Cake' => 'funny birthday cake.jpg',
            'Galletas de Sujetadores y Tangas' => 'Galletas de Sujetadores y Tangas.jpg',
            'Lingerie Cake' => 'Lingerie Cake.jpg',
            'Torta Fálica' => 'pene.jpg',
            
            // Galletas
            'Galletas con Chips de Chocolate' => 'galletas con chips de chocolate.jpg',
            
            // Bebidas
            'Cappuccino Artesanal' => 'Cappuccino .jpg',
            'Bebidas Refrescantes' => 'bebidas refrescantes.png',
            
            // Mapeos adicionales para compatibilidad
            'Cheesecake de Fresa' => 'Cheesecake de Fresa Delicioso.jpg',
            'Churros' => 'churros.jpg',
            'Fresas con Crema' => 'fresas con crema.jpg',
            'Cappuccino' => 'Cappuccino .jpg',
            'Galletas con Chips' => 'galletas con chips de chocolate.jpg',
            'Torta Despedida Soltera' => 'torta para despedida de soltera___.jpg'
        ];
        
        // Buscar imagen exacta
        if (isset($mapeo_imagenes[$nombre_producto])) {
            return 'img/' . $mapeo_imagenes[$nombre_producto];
        }
        
        // Buscar coincidencias parciales
        foreach ($mapeo_imagenes as $producto => $imagen) {
            if (stripos($producto, $nombre_producto) !== false || 
                stripos($nombre_producto, $producto) !== false) {
                return 'img/' . $imagen;
            }
        }
        
        // Imagen por defecto si no se encuentra
        return 'img/logo.crdownload';
    }
    
    /**
     * Formatear precio colombiano
     */
    public function formatearPrecio($precio) {
        return '$' . number_format($precio, 0, ',', '.');
    }
}

// API REST para AJAX
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['api'])) {
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
