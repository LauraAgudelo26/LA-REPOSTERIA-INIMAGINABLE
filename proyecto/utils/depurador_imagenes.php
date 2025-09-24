<?php
/**
 * DEPURADOR COMPLETO DE IMÁGENES
 * Analiza por qué solo donas, churros y bananos funcionan
 */

header('Content-Type: text/html; charset=UTF-8');

echo "<html><head><meta charset='UTF-8'><title>Depurador de Imágenes</title>";
echo "<style>
body { font-family: Arial; margin: 20px; }
.debug-section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; }
.success { color: green; font-weight: bold; }
.error { color: red; font-weight: bold; }
.warning { color: orange; font-weight: bold; }
table { border-collapse: collapse; width: 100%; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
.img-test { max-width: 100px; max-height: 100px; }
</style></head><body>";

echo "<h1>🔍 DEPURADOR COMPLETO DE IMÁGENES</h1>";

try {
    require_once '../config/config_db.php';
    
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();

    echo "<div class='debug-section'>";
    echo "<h2>1. ANÁLISIS DE ARCHIVOS FÍSICOS</h2>";
    
    $directorio_img = __DIR__ . '/../public/img/';
    $archivos_fisicos = scandir($directorio_img);
    
    $imagenes_fisicas = [];
    foreach ($archivos_fisicos as $archivo) {
        if ($archivo != '.' && $archivo != '..' && strtolower(substr($archivo, -4)) == '.jpg') {
            $ruta_completa = $directorio_img . $archivo;
            $imagenes_fisicas[$archivo] = [
                'existe' => file_exists($ruta_completa),
                'tamaño' => file_exists($ruta_completa) ? filesize($ruta_completa) : 0,
                'legible' => file_exists($ruta_completa) ? is_readable($ruta_completa) : false,
                'modificado' => file_exists($ruta_completa) ? date('Y-m-d H:i:s', filemtime($ruta_completa)) : 'N/A'
            ];
        }
    }
    
    echo "<p><strong>Archivos físicos encontrados:</strong> " . count($imagenes_fisicas) . "</p>";
    echo "<table>";
    echo "<tr><th>Archivo</th><th>Existe</th><th>Tamaño</th><th>Legible</th><th>Modificado</th></tr>";
    
    foreach ($imagenes_fisicas as $archivo => $info) {
        $color_fila = $info['existe'] && $info['legible'] ? 'success' : 'error';
        echo "<tr class='$color_fila'>";
        echo "<td>$archivo</td>";
        echo "<td>" . ($info['existe'] ? '✅' : '❌') . "</td>";
        echo "<td>" . number_format($info['tamaño']) . " bytes</td>";
        echo "<td>" . ($info['legible'] ? '✅' : '❌') . "</td>";
        echo "<td>{$info['modificado']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    
    echo "<div class='debug-section'>";
    echo "<h2>2. ANÁLISIS DE BASE DE DATOS</h2>";
    
    $stmt = $pdo->query("SELECT id, nombre, imagen, categoria_id, activo FROM producto ORDER BY nombre");
    $productos_bd = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p><strong>Productos en BD:</strong> " . count($productos_bd) . "</p>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Imagen BD</th><th>Archivo Existe</th><th>Categoría</th><th>Activo</th><th>Problema</th></tr>";
    
    foreach ($productos_bd as $producto) {
        $archivo_imagen = $producto['imagen'];
        $ruta_imagen = $directorio_img . $archivo_imagen;
        $existe_archivo = file_exists($ruta_imagen);
        
        $problema = '';
        if (!$existe_archivo) {
            $problema = 'Archivo no existe';
        } elseif (!is_readable($ruta_imagen)) {
            $problema = 'Archivo no legible';
        } elseif (filesize($ruta_imagen) == 0) {
            $problema = 'Archivo vacío';
        }
        
        $color_fila = $existe_archivo && empty($problema) ? 'success' : 'error';
        
        echo "<tr class='$color_fila'>";
        echo "<td>{$producto['id']}</td>";
        echo "<td>{$producto['nombre']}</td>";
        echo "<td>{$archivo_imagen}</td>";
        echo "<td>" . ($existe_archivo ? '✅' : '❌') . "</td>";
        echo "<td>{$producto['categoria_id']}</td>";
        echo "<td>" . ($producto['activo'] ? '✅' : '❌') . "</td>";
        echo "<td>$problema</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    
    echo "<div class='debug-section'>";
    echo "<h2>3. ANÁLISIS DE CONTROLADOR DE PRODUCTOS</h2>";
    
    // Incluir el controlador sin mostrar warnings
    ob_start();
    try {
        require_once '../productos/controllers/controlador_productos.php';
        $controlador = new ProductoController();
        ob_end_clean();
        
        echo "<p class='success'>✅ Controlador cargado correctamente</p>";
        
        echo "<h3>3.1 Pruebas de Mapeo</h3>";
        echo "<table>";
        echo "<tr><th>Nombre Producto</th><th>URL Generada</th><th>Imagen Final</th><th>Estado</th></tr>";
        
        // Probar los 3 que funcionan
        $productos_test = ['donas', 'churros', 'bananos'];
        echo "<tr><td colspan='4'><strong>PRODUCTOS QUE FUNCIONAN:</strong></td></tr>";
        
        foreach ($productos_test as $nombre) {
            $url_generada = $controlador->obtenerImagenProducto($nombre);
            $imagen_final = basename($url_generada);
            $ruta_final = $directorio_img . $imagen_final;
            $estado = file_exists($ruta_final) ? '✅ OK' : '❌ NO EXISTE';
            
            echo "<tr class='success'>";
            echo "<td>$nombre</td>";
            echo "<td>$url_generada</td>";
            echo "<td>$imagen_final</td>";
            echo "<td>$estado</td>";
            echo "</tr>";
        }
        
        // Probar algunos que NO funcionan
        $productos_problema = ['soltera', 'fresas', 'galletas', 'torta'];
        echo "<tr><td colspan='4'><strong>PRODUCTOS CON PROBLEMA:</strong></td></tr>";
        
        foreach ($productos_problema as $nombre) {
            $url_generada = $controlador->obtenerImagenProducto($nombre);
            $imagen_final = basename($url_generada);
            $ruta_final = $directorio_img . $imagen_final;
            $estado = file_exists($ruta_final) ? '✅ OK' : '❌ NO EXISTE';
            
            $color = file_exists($ruta_final) ? 'success' : 'error';
            
            echo "<tr class='$color'>";
            echo "<td>$nombre</td>";
            echo "<td>$url_generada</td>";
            echo "<td>$imagen_final</td>";
            echo "<td>$estado</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "<p class='error'>❌ Error cargando controlador: " . $e->getMessage() . "</p>";
    }
    echo "</div>";
    
    echo "<div class='debug-section'>";
    echo "<h2>4. PRUEBA VISUAL DE IMÁGENES</h2>";
    echo "<p>Probando carga directa de imágenes:</p>";
    
    // Seleccionar algunas imágenes para probar
    $imagenes_prueba = ['donas.jpg', 'churros.jpg', 'banano.jpg', 'torta_soltera.jpg', 'fresas_crema.jpg'];
    
    echo "<table>";
    echo "<tr><th>Imagen</th><th>Ruta</th><th>Vista</th><th>Estado</th></tr>";
    
    foreach ($imagenes_prueba as $imagen) {
        $ruta_relativa = "../public/img/$imagen";
        $ruta_absoluta = $directorio_img . $imagen;
        $existe = file_exists($ruta_absoluta);
        
        echo "<tr>";
        echo "<td>$imagen</td>";
        echo "<td>$ruta_relativa</td>";
        echo "<td>";
        if ($existe) {
            echo "<img src='$ruta_relativa' class='img-test' onerror=\"this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iI2RkZCIvPjx0ZXh0IHg9IjUwIiB5PSI1NSIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjEyIiBmaWxsPSIjOTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5FUlJPUjwvdGV4dD48L3N2Zz4='; this.style.border='2px solid red';\">";
        } else {
            echo "NO EXISTE";
        }
        echo "</td>";
        echo "<td>" . ($existe ? '✅ EXISTE' : '❌ NO EXISTE') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    
    echo "<div class='debug-section'>";
    echo "<h2>5. DIAGNÓSTICO FINAL</h2>";
    
    $total_archivos = count($imagenes_fisicas);
    $archivos_ok = 0;
    foreach ($imagenes_fisicas as $info) {
        if ($info['existe'] && $info['legible'] && $info['tamaño'] > 0) {
            $archivos_ok++;
        }
    }
    
    echo "<p><strong>Resumen:</strong></p>";
    echo "<ul>";
    echo "<li>Archivos físicos: $total_archivos</li>";
    echo "<li>Archivos OK: $archivos_ok</li>";
    echo "<li>Productos en BD: " . count($productos_bd) . "</li>";
    echo "</ul>";
    
    if ($archivos_ok < $total_archivos) {
        echo "<p class='error'>❌ PROBLEMA: Algunos archivos no son accesibles</p>";
    }
    
    echo "<p><strong>Próximos pasos sugeridos:</strong></p>";
    echo "<ol>";
    echo "<li>Verificar permisos de archivos</li>";
    echo "<li>Revisar mapeo del controlador</li>";
    echo "<li>Unificar APIs</li>";
    echo "<li>Corregir rutas problemáticas</li>";
    echo "</ol>";
    
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error general: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>