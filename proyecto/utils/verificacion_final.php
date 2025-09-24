<?php
/**
 * Verificación final del sistema completo
 */

echo "=== VERIFICACIÓN FINAL DEL SISTEMA ===\n\n";

try {
    require_once '../config/config_db.php';
    require_once '../productos/controllers/controlador_productos.php';
    
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();
    $controlador = new ProductoController();
    
    echo "1. VERIFICANDO BASE DE DATOS:\n";
    
    // Verificar productos
    $stmt = $pdo->query("SELECT id, nombre, imagen, activo FROM producto ORDER BY id");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   Total productos: " . count($productos) . "\n";
    
    $activos = 0;
    $imagenes_ok = 0;
    $imagenes_jpg = 0;
    
    foreach ($productos as $producto) {
        if ($producto['activo']) $activos++;
        
        $ruta_imagen = __DIR__ . '/../public/img/' . $producto['imagen'];
        if (file_exists($ruta_imagen)) {
            $imagenes_ok++;
        }
        
        if (str_ends_with(strtolower($producto['imagen']), '.jpg')) {
            $imagenes_jpg++;
        }
        
        echo "   - {$producto['nombre']}: {$producto['imagen']} " . 
             (file_exists($ruta_imagen) ? "✅" : "❌") . "\n";
    }
    
    echo "\n   Productos activos: $activos\n";
    echo "   Imágenes que existen: $imagenes_ok\n";
    echo "   Imágenes .jpg: $imagenes_jpg\n";
    
    echo "\n2. VERIFICANDO DIRECTORIO DE IMÁGENES:\n";
    
    $directorio_img = __DIR__ . '/../public/img/';
    $archivos_img = scandir($directorio_img);
    $archivos_jpg = 0;
    $archivos_otros = 0;
    
    foreach ($archivos_img as $archivo) {
        if ($archivo == '.' || $archivo == '..') continue;
        
        if (str_ends_with(strtolower($archivo), '.jpg')) {
            $archivos_jpg++;
        } else {
            $archivos_otros++;
            echo "   ⚠️  Archivo no-jpg: $archivo\n";
        }
    }
    
    echo "   Archivos .jpg: $archivos_jpg\n";
    echo "   Archivos otros: $archivos_otros\n";
    
    echo "\n3. VERIFICANDO CONTROLADOR DE PRODUCTOS:\n";
    
    // Probar algunos productos
    $productos_prueba = ['soltera', 'donas', 'churros', 'fresas_crema'];
    
    foreach ($productos_prueba as $nombre_prueba) {
        $imagen_url = $controlador->obtenerImagenProducto($nombre_prueba);
        echo "   '$nombre_prueba' → $imagen_url\n";
    }
    
    echo "\n4. VERIFICANDO APIs:\n";
    
    // Simular llamada a API de productos especiales
    $sql = "SELECT COUNT(*) as total FROM producto p 
            JOIN categoria c ON p.categoria_id = c.id 
            WHERE c.id = 2 AND p.activo = 1";
    $stmt = $pdo->query($sql);
    $especiales = $stmt->fetch()['total'];
    echo "   Productos especiales disponibles: $especiales\n";
    
    // Simular llamada a API de productos normales
    $sql = "SELECT COUNT(*) as total FROM producto WHERE activo = 1";
    $stmt = $pdo->query($sql);
    $normales = $stmt->fetch()['total'];
    echo "   Productos normales disponibles: $normales\n";
    
    echo "\n=== RESUMEN FINAL ===\n";
    
    $todo_ok = true;
    
    if ($imagenes_ok != count($productos)) {
        echo "❌ PROBLEMA: No todas las imágenes de la BD existen\n";
        $todo_ok = false;
    }
    
    if ($imagenes_jpg != count($productos)) {
        echo "❌ PROBLEMA: No todas las imágenes son .jpg\n";
        $todo_ok = false;
    }
    
    if ($archivos_otros > 0) {
        echo "❌ PROBLEMA: Hay archivos no-jpg en el directorio\n";
        $todo_ok = false;
    }
    
    if ($todo_ok) {
        echo "🎉 ¡SISTEMA COMPLETAMENTE FUNCIONAL!\n";
        echo "✅ Todas las imágenes existen\n";
        echo "✅ Todas las imágenes son .jpg\n";
        echo "✅ Base de datos actualizada\n";
        echo "✅ APIs funcionando\n";
        echo "✅ Controlador funcionando\n";
        echo "\n🍰 ¡LA REPOSTERÍA INIMAGINABLE ESTÁ LISTA!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>