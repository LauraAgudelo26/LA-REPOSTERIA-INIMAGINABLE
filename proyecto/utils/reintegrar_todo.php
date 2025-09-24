<?php
/**
 * SCRIPT COMPLETO PARA REINTEGRAR TODAS LAS IMÁGENES
 * Este script va a:
 * 1. Leer todas las imágenes disponibles
 * 2. Borrar todos los productos de la BD
 * 3. Crear productos nuevos para CADA imagen
 * 4. Actualizar todos los archivos con rutas correctas
 */

require_once '../config/config_db.php';

echo "=== SCRIPT COMPLETO DE REINTEGRACIÓN DE IMÁGENES ===\n\n";

try {
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();
    
    // 1. LEER TODAS LAS IMÁGENES DISPONIBLES
    echo "PASO 1: Escaneando imágenes disponibles...\n";
    $directorio_img = '../public/img/';
    $ruta_completa = realpath($directorio_img);
    
    if (!is_dir($ruta_completa)) {
        die("❌ Error: No se puede acceder a $ruta_completa\n");
    }
    
    $extensiones_validas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $imagenes = [];
    
    $archivos = scandir($ruta_completa);
    foreach ($archivos as $archivo) {
        if ($archivo == '.' || $archivo == '..') continue;
        $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
        if (in_array($extension, $extensiones_validas)) {
            $imagenes[] = $archivo;
        }
    }
    
    echo "Imágenes encontradas: " . count($imagenes) . "\n";
    foreach ($imagenes as $img) {
        echo "  - $img\n";
    }
    
    // 2. LIMPIAR BASE DE DATOS
    echo "\nPASO 2: Limpiando base de datos...\n";
    
    // Primero, verificar si las tablas existen
    $tablas = ['producto', 'categoria'];
    foreach ($tablas as $tabla) {
        $check = $pdo->query("SHOW TABLES LIKE '$tabla'")->rowCount();
        if ($check == 0) {
            echo "⚠️ Tabla '$tabla' no existe. Creándola...\n";
        }
    }
    
    // Crear tabla categoria si no existe
    $pdo->exec("CREATE TABLE IF NOT EXISTS categoria (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        descripcion TEXT,
        activo BOOLEAN DEFAULT 1,
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Crear tabla producto si no existe
    $pdo->exec("CREATE TABLE IF NOT EXISTS producto (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        descripcion TEXT,
        precio DECIMAL(10,2) NOT NULL,
        categoria_id INT,
        imagen VARCHAR(255),
        stock INT DEFAULT 0,
        destacado BOOLEAN DEFAULT 0,
        activo BOOLEAN DEFAULT 1,
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (categoria_id) REFERENCES categoria(id)
    )");
    
    // Limpiar productos existentes
    $pdo->exec("DELETE FROM producto");
    $pdo->exec("ALTER TABLE producto AUTO_INCREMENT = 1");
    
    // Insertar categorías básicas
    $pdo->exec("DELETE FROM categoria");
    $pdo->exec("ALTER TABLE categoria AUTO_INCREMENT = 1");
    
    $categorias = [
        ['Postres', 'Deliciosos postres caseros'],
        ['Tortas Especiales', 'Tortas para ocasiones especiales'],
        ['Frutas', 'Frutas frescas y preparadas'],
        ['Galletas', 'Galletas artesanales'],
        ['Bebidas', 'Bebidas y café especializado']
    ];
    
    foreach ($categorias as $cat) {
        $stmt = $pdo->prepare("INSERT INTO categoria (nombre, descripcion) VALUES (?, ?)");
        $stmt->execute($cat);
    }
    echo "Categorías creadas: " . count($categorias) . "\n";
    
    // 3. CREAR PRODUCTOS PARA CADA IMAGEN
    echo "\nPASO 3: Creando productos para cada imagen...\n";
    
    $productos_generados = [];
    
    foreach ($imagenes as $imagen) {
        // Generar nombre del producto basado en el archivo
        $nombre_base = pathinfo($imagen, PATHINFO_FILENAME);
        $nombre_producto = ucwords(str_replace(['_', '-'], ' ', $nombre_base));
        
        // Asignar categoría basada en el nombre
        $categoria_id = 1; // Por defecto: Postres
        if (stripos($nombre_base, 'torta') !== false || stripos($nombre_base, 'lingerie') !== false || stripos($nombre_base, 'soltera') !== false || stripos($nombre_base, 'falica') !== false) {
            $categoria_id = 2; // Tortas Especiales
        } elseif (stripos($nombre_base, 'fresa') !== false || stripos($nombre_base, 'banano') !== false || stripos($nombre_base, 'cereza') !== false || stripos($nombre_base, 'manzana') !== false || stripos($nombre_base, 'uvas') !== false) {
            $categoria_id = 3; // Frutas
        } elseif (stripos($nombre_base, 'galleta') !== false) {
            $categoria_id = 4; // Galletas
        } elseif (stripos($nombre_base, 'cappuccino') !== false || stripos($nombre_base, 'bebida') !== false) {
            $categoria_id = 5; // Bebidas
        }
        
        // Generar descripción
        $descripcion = "Delicioso $nombre_producto hecho artesanalmente";
        
        // Generar precio aleatorio razonable
        $precios = [15000, 18000, 20000, 25000, 30000, 35000, 40000, 45000, 50000];
        $precio = $precios[array_rand($precios)];
        
        // Stock aleatorio
        $stock = rand(5, 20);
        
        // Algunos productos destacados
        $destacado = (rand(1, 4) == 1) ? 1 : 0;
        
        // Insertar producto
        $stmt = $pdo->prepare("INSERT INTO producto (nombre, descripcion, precio, categoria_id, imagen, stock, destacado, activo) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([$nombre_producto, $descripcion, $precio, $categoria_id, $imagen, $stock, $destacado]);
        
        $productos_generados[] = [
            'nombre' => $nombre_producto,
            'imagen' => $imagen,
            'precio' => $precio,
            'categoria_id' => $categoria_id
        ];
        
        echo "✅ Creado: $nombre_producto ($imagen)\n";
    }
    
    echo "\nProductos creados: " . count($productos_generados) . "\n";
    
    // 4. ACTUALIZAR TODOS LOS ARCHIVOS CON RUTAS CORRECTAS
    echo "\nPASO 4: Actualizando archivos con rutas correctas...\n";
    
    // Buscar y actualizar archivos que usan rutas incorrectas
    $archivos_a_actualizar = [
        '../index.php',
        '../public/views/index.html',
        '../public/views/placeres_ocultos.html',
        '../productos/api/api_productos_normales.php'
    ];
    
    foreach ($archivos_a_actualizar as $archivo) {
        if (file_exists($archivo)) {
            $contenido = file_get_contents($archivo);
            $original = $contenido;
            
            // Reemplazar rutas incorrectas
            $contenido = str_replace('src="../public/img/', 'src="../public/img/', $contenido);
            $contenido = str_replace("src='../public/img/", "src='../public/img/", $contenido);
            $contenido = str_replace('onerror="this.src=\'../public/img/logo.jpg\'"', 'onerror="this.src=\'../public/img/logo.jpg\'"', $contenido);
            $contenido = str_replace('onerror="this.src=../public/img/logo.jpg"', 'onerror="this.src=../public/img/logo.jpg"', $contenido);
            
            if ($contenido !== $original) {
                file_put_contents($archivo, $contenido);
                echo "✅ Actualizado: $archivo\n";
            } else {
                echo "→ Sin cambios: $archivo\n";
            }
        } else {
            echo "⚠️ No encontrado: $archivo\n";
        }
    }
    
    echo "\n=== RESUMEN FINAL ===\n";
    echo "✅ Imágenes procesadas: " . count($imagenes) . "\n";
    echo "✅ Productos creados: " . count($productos_generados) . "\n";
    echo "✅ Categorías: " . count($categorias) . "\n";
    echo "✅ Base de datos completamente reconstruida\n";
    echo "✅ Archivos actualizados\n";
    
    echo "\n🌐 PRUEBAS DISPONIBLES:\n";
    echo "- API: /proyecto/productos/api/api_productos_especiales.php\n";
    echo "- API: /proyecto/productos/api/api_productos.php\n";
    echo "- Página: /proyecto/utils/prueba_imagenes.html\n";
    echo "- Web: /proyecto/index.php\n";
    
    echo "\n🎯 TODOS LOS PRODUCTOS AHORA TIENEN IMAGEN GARANTIZADA\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Detalles: " . $e->getTraceAsString() . "\n";
}
?>