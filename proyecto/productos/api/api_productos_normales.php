<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../config/config_db.php';

try {
    $db = DatabaseConnection::getInstance();
    
    // Consultar productos normales (excluyendo categoría 4 - Pasteles Especiales)
    $sql = "SELECT 
                p.idProducto,
                p.Nombre as nombre,
                p.Descripcion as descripcion,
                p.Precio as precio,
                p.Stock as stock,
                c.Nombre as categoria,
                c.idCategoria as categoria_id,
                CASE 
                    -- POSTRES (categoría 1)
                    WHEN p.Nombre LIKE '%Cheesecake%' THEN 'Cheesecake de Fresa Delicioso.jpg'
                    WHEN p.Nombre LIKE '%Churros%' THEN 'churros.jpg'
                    WHEN p.Nombre LIKE '%Donas%' THEN 'donas.jpg'
                    WHEN p.Nombre LIKE '%Gelatina%' THEN 'postre de gelatina.jpg'
                    WHEN p.Nombre LIKE '%Limón%' THEN 'postre de limon.jpg'
                    WHEN p.Nombre LIKE '%Oreo%' THEN 'postre de oreo.jfif'
                    WHEN p.Nombre LIKE '%Canela%' THEN 'rollos de canela.jpg'
                    WHEN p.Nombre LIKE '%Cuca%' THEN 'cuca.jfif'
                    
                    -- FRUTAS (categoría 3)
                    WHEN p.Nombre LIKE '%Fresas con Crema%' THEN 'fresas con crema.jpg'
                    WHEN p.Nombre LIKE '%Fresas con Marshmallow%' THEN 'fresas con masmelo.jpeg'
                    WHEN p.Nombre LIKE '%Banano%' THEN 'banano.jpg'
                    WHEN p.Nombre LIKE '%Cerezas%' OR p.Nombre LIKE '%Cereza%' THEN 'cereza.jpeg'
                    WHEN p.Nombre LIKE '%Manzanas%' OR p.Nombre LIKE '%Manzana%' THEN 'manzana.jpeg'
                    WHEN p.Nombre LIKE '%Uvas%' THEN 'uvas.jpeg'
                    
                    -- GALLETAS (categoría 5)
                    WHEN p.Nombre LIKE '%Galletas con Chips%' THEN 'galletas con chips de chocolate.jpg'
                    
                    -- BEBIDAS (categoría 2)
                    WHEN p.Nombre LIKE '%Cappuccino%' THEN 'Cappuccino .jpg'
                    WHEN p.Nombre LIKE '%Bebidas Refrescantes%' THEN 'bebidas refrescantes.png'
                    
                    -- TARTAS NORMALES
                    WHEN p.Nombre LIKE '%Mini Tarta%' THEN 'mini tarta de frutas.jpg'
                    
                    ELSE 'logo.crdownload'
                END as imagen
            FROM Producto p
            JOIN Categoria c ON p.idCategoria = c.idCategoria
            WHERE c.idCategoria != 4
            ORDER BY c.idCategoria, p.Nombre";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Verificar que las imágenes existen y marcar productos destacados
    foreach ($productos as &$producto) {
        $rutaImagen = __DIR__ . '/img/' . $producto['imagen'];
        if (!file_exists($rutaImagen)) {
            // Si la imagen no existe, usar una imagen por defecto según la categoría
            switch ($producto['categoria_id']) {
                case 1: // Postres
                    $producto['imagen'] = 'donas.jpg';
                    break;
                case 2: // Bebidas
                    $producto['imagen'] = 'bebidas refrescantes.png';
                    break;
                case 3: // Frutas
                    $producto['imagen'] = 'fresas con crema.jpg';
                    break;
                case 5: // Galletas
                    $producto['imagen'] = 'galletas con chips de chocolate.jpg';
                    break;
                default:
                    $producto['imagen'] = 'donas.jpg';
            }
        }
        
        // Marcar algunos productos como destacados
        $productosDestacados = ['Cheesecake', 'Churros', 'Fresas con Crema', 'Cappuccino'];
        $producto['destacado'] = false;
        foreach ($productosDestacados as $destacado) {
            if (stripos($producto['nombre'], $destacado) !== false) {
                $producto['destacado'] = true;
                break;
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'productos' => $productos,
        'total' => count($productos),
        'categorias' => array_unique(array_column($productos, 'categoria'))
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al cargar productos: ' . $e->getMessage(),
        'productos' => []
    ]);
}
?>
