<?php
/**
 * Generador automático de mapeo de imágenes
 * Este script escanea la carpeta de imágenes y genera automáticamente
 * el mapeo para el controlador de productos
 */

// Configuración
$directorio_imagenes = '../public/img/';
$ruta_completa = realpath($directorio_imagenes);

if (!is_dir($ruta_completa)) {
    die("Error: No se puede acceder al directorio de imágenes: $ruta_completa\n");
}

echo "=== GENERADOR AUTOMÁTICO DE MAPEO DE IMÁGENES ===\n";
echo "Directorio: $ruta_completa\n\n";

// Escanear archivos de imagen
$extensiones_validas = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'jfif'];
$archivos_imagen = [];

$archivos = scandir($ruta_completa);
foreach ($archivos as $archivo) {
    if ($archivo != '.' && $archivo != '..') {
        $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
        if (in_array($extension, $extensiones_validas)) {
            $archivos_imagen[] = $archivo;
        }
    }
}

echo "Imágenes encontradas (" . count($archivos_imagen) . "):\n";
foreach ($archivos_imagen as $imagen) {
    echo "- $imagen\n";
}

// Generar mapeo automático basado en los nombres de archivo
$mapeo_automatico = [];

// Función para generar nombres de productos basados en el nombre del archivo
function generarNombresProducto($nombre_archivo) {
    $nombre_base = pathinfo($nombre_archivo, PATHINFO_FILENAME);
    
    // Reemplazar guiones bajos y guiones por espacios
    $nombre_limpio = str_replace(['_', '-'], ' ', $nombre_base);
    
    // Capitalizar cada palabra
    $nombre_capitalizado = ucwords($nombre_limpio);
    
    $nombres = [$nombre_capitalizado];
    
    // Agregar variaciones comunes
    $nombres[] = $nombre_limpio; // versión sin capitalizar
    $nombres[] = strtolower($nombre_limpio); // versión en minúsculas
    
    // Variaciones específicas basadas en el nombre
    $variaciones_especiales = [
        'torta_soltera' => ['Despedida Soltera', 'Torta Despedida Soltera', 'Soltera'],
        'cheesecake_fresa' => ['Cheesecake de Fresa Delicioso', 'Cheesecake de Fresa'],
        'fresas_crema' => ['Fresas con Crema'],
        'fresas_marshmallow' => ['Fresas con Marshmallow', 'Fresas con Masmelo'],
        'galletas_chocolate' => ['Galletas con Chips de Chocolate', 'Galletas con Chips'],
        'postre_gelatina' => ['Postre de Gelatina'],
        'postre_limon' => ['Postre de Limón', 'Postre de Limon'],
        'postre_oreo' => ['Postre de Oreo'],
        'rollos_canela' => ['Rollos de Canela'],
        'torta_cumpleanos' => ['Funny Birthday Cake', 'Birthday Cake', 'Torta Cumpleanos'],
        'mini_tarta_frutas' => ['Mini Tarta de Frutas'],
        'galletas_especiales' => ['Galletas de Sujetadores y Tangas', 'Galletas Especiales'],
        'torta_lingerie' => ['Lingerie Cake', 'Torta Lingerie'],
        'torta_falica' => ['Torta Fálica', 'Torta Falica'],
        'bebidas_refrescantes' => ['Bebidas Refrescantes'],
        'cappuccino' => ['Cappuccino Artesanal'],
        'churros' => ['Churros Tradicionales'],
        'donas' => ['Donas Glaseadas'],
        'cuca' => ['Cuca Tradicional'],
        'banano' => ['Banano Fresco'],
        'cereza' => ['Cerezas Selectas'],
        'manzana' => ['Manzanas Rojas'],
        'uvas' => ['Uvas Frescas']
    ];
    
    $nombre_base_key = strtolower($nombre_base);
    if (isset($variaciones_especiales[$nombre_base_key])) {
        $nombres = array_merge($nombres, $variaciones_especiales[$nombre_base_key]);
    }
    
    return array_unique($nombres);
}

// Generar mapeo
foreach ($archivos_imagen as $imagen) {
    $nombres_producto = generarNombresProducto($imagen);
    foreach ($nombres_producto as $nombre) {
        if (!empty(trim($nombre))) {
            $mapeo_automatico[trim($nombre)] = $imagen;
        }
    }
}

echo "\n=== MAPEO GENERADO ===\n";
echo "Se generaron " . count($mapeo_automatico) . " mapeos automáticamente.\n\n";

// Generar código PHP para el controlador
echo "<?php\n";
echo "// Mapeo generado automáticamente el " . date('Y-m-d H:i:s') . "\n";
echo "\$mapeo_imagenes = [\n";

// Agrupar por categorías para mejor organización
$categorias = [
    'Postres' => ['cheesecake', 'churros', 'donas', 'postre', 'rollos', 'cuca'],
    'Frutas' => ['fresas', 'banano', 'cereza', 'manzana', 'uvas'],
    'Pasteles Especiales' => ['torta', 'lingerie', 'falica', 'cumpleanos', 'mini'],
    'Galletas' => ['galletas'],
    'Bebidas' => ['cappuccino', 'bebidas'],
    'Otros' => []
];

foreach ($categorias as $categoria => $palabras_clave) {
    $items_categoria = [];
    
    foreach ($mapeo_automatico as $producto => $imagen) {
        $encontrado = false;
        foreach ($palabras_clave as $palabra) {
            if (stripos($producto, $palabra) !== false || stripos($imagen, $palabra) !== false) {
                $items_categoria[$producto] = $imagen;
                $encontrado = true;
                break;
            }
        }
        
        if (!$encontrado && $categoria == 'Otros') {
            $items_categoria[$producto] = $imagen;
        }
    }
    
    if (!empty($items_categoria)) {
        echo "    // $categoria\n";
        foreach ($items_categoria as $producto => $imagen) {
            echo "    '$producto' => '$imagen',\n";
        }
        echo "\n";
    }
}

echo "];\n";
echo "?>\n";

echo "\n=== INSTRUCCIONES DE USO ===\n";
echo "1. Copie el mapeo generado arriba\n";
echo "2. Reemplace el array \$mapeo_imagenes en el archivo:\n";
echo "   proyecto/productos/controllers/controlador_productos.php\n";
echo "3. El sistema ahora reconocerá automáticamente las imágenes\n\n";

// Generar método mejorado para el controlador
echo "=== MÉTODO MEJORADO PARA EL CONTROLADOR ===\n\n";
echo "<?php\n";
echo "/**\n";
echo " * Método mejorado para obtener imagen del producto\n";
echo " * Incluye fallback automático y búsqueda inteligente\n";
echo " */\n";
echo "public function obtenerImagenProducto(\$nombre_producto) {\n";
echo "    // [Insertar aquí el mapeo generado arriba]\n\n";
echo "    // Buscar imagen exacta\n";
echo "    if (isset(\$mapeo_imagenes[\$nombre_producto])) {\n";
echo "        return '../public/img/' . \$mapeo_imagenes[\$nombre_producto];\n";
echo "    }\n\n";
echo "    // Buscar coincidencias parciales (búsqueda inteligente)\n";
echo "    foreach (\$mapeo_imagenes as \$producto => \$imagen) {\n";
echo "        if (stripos(\$producto, \$nombre_producto) !== false || \n";
echo "            stripos(\$nombre_producto, \$producto) !== false) {\n";
echo "            return '../public/img/' . \$imagen;\n";
echo "        }\n";
echo "    }\n\n";
echo "    // Búsqueda por similitud de palabras clave\n";
echo "    \$palabras_producto = explode(' ', strtolower(\$nombre_producto));\n";
echo "    foreach (\$mapeo_imagenes as \$producto => \$imagen) {\n";
echo "        \$palabras_mapeo = explode(' ', strtolower(\$producto));\n";
echo "        \$coincidencias = array_intersect(\$palabras_producto, \$palabras_mapeo);\n";
echo "        if (count(\$coincidencias) >= 1) {\n";
echo "            return '../public/img/' . \$imagen;\n";
echo "        }\n";
echo "    }\n\n";
echo "    // Imagen por defecto\n";
echo "    return '../public/img/logo.jpg';\n";
echo "}\n";
echo "?>\n\n";

echo "=== SCRIPT COMPLETADO ===\n";
echo "El mapeo se ha generado automáticamente basándose en los archivos disponibles.\n";
?>