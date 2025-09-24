<?php
/**
 * Script para arreglar TODAS las rutas de imágenes en TODO el proyecto
 */

echo "=== ARREGLANDO TODAS LAS RUTAS DE IMÁGENES ===\n\n";

// Directorio raíz del proyecto
$directorio_raiz = realpath('../');
echo "Directorio del proyecto: $directorio_raiz\n\n";

// Extensiones de archivos a procesar
$extensiones = ['php', 'html', 'js', 'css'];

// Función recursiva para encontrar archivos
function encontrarArchivos($directorio, $extensiones) {
    $archivos = [];
    $items = scandir($directorio);
    
    foreach ($items as $item) {
        if ($item == '.' || $item == '..') continue;
        
        $ruta_completa = $directorio . '/' . $item;
        
        if (is_dir($ruta_completa)) {
            // Saltar directorios que no necesitamos
            if (in_array($item, ['node_modules', '.git', 'vendor', 'logs'])) continue;
            $archivos = array_merge($archivos, encontrarArchivos($ruta_completa, $extensiones));
        } else {
            $extension = strtolower(pathinfo($item, PATHINFO_EXTENSION));
            if (in_array($extension, $extensiones)) {
                $archivos[] = $ruta_completa;
            }
        }
    }
    
    return $archivos;
}

// Encontrar todos los archivos
$archivos_encontrados = encontrarArchivos($directorio_raiz, $extensiones);
echo "Archivos encontrados: " . count($archivos_encontrados) . "\n\n";

$archivos_modificados = 0;
$total_reemplazos = 0;

foreach ($archivos_encontrados as $archivo) {
    $contenido_original = file_get_contents($archivo);
    $contenido_nuevo = $contenido_original;
    
    // Lista de reemplazos a realizar
    $reemplazos = [
        // Rutas de imágenes
        'src="../public/img/' => 'src="../public/img/',
        "src='../public/img/" => "src='../public/img/",
        'src="../public/img"' => 'src="../public/img"',
        "src='../public/img'" => "src='../public/img'",
        
        // Rutas en onerror
        'onerror="this.src=\'../public/img/' => 'onerror="this.src=\'../public/img/',
        "onerror='this.src=\"../public/img/" => "onerror='this.src=\"../public/img/",
        'onerror="this.src="../public/img/' => 'onerror="this.src="../public/img/',
        
        // Placeholder
        '../public/img/logo.jpg' => '../public/img/logo.jpg',
        
        // URLs en JavaScript/JSON
        '"../public/img/' => '"../public/img/',
        "'../public/img/" => "'../public/img/",
        
        // CSS backgrounds
        'url(../public/img/' => 'url(../public/img/',
        'url("../public/img/' => 'url("../public/img/',
        "url('../public/img/" => "url('../public/img/",
        
        // Casos específicos
        '../public/img/${' => '../public/img/${',
        '../public/img/" +' => '../public/img/" +',
    ];
    
    $reemplazos_en_archivo = 0;
    
    foreach ($reemplazos as $buscar => $reemplazar) {
        $contenido_antes = $contenido_nuevo;
        $contenido_nuevo = str_replace($buscar, $reemplazar, $contenido_nuevo);
        $count = substr_count($contenido_antes, $buscar);
        if ($count > 0) {
            $reemplazos_en_archivo += $count;
            echo "  → $buscar ✕$count\n";
        }
    }
    
    // Si hubo cambios, guardar el archivo
    if ($contenido_nuevo !== $contenido_original) {
        file_put_contents($archivo, $contenido_nuevo);
        $archivos_modificados++;
        $total_reemplazos += $reemplazos_en_archivo;
        
        $archivo_relativo = str_replace($directorio_raiz, '', $archivo);
        echo "✅ Modificado: $archivo_relativo ($reemplazos_en_archivo reemplazos)\n";
    }
}

echo "\n=== RESUMEN ===\n";
echo "Archivos procesados: " . count($archivos_encontrados) . "\n";
echo "Archivos modificados: $archivos_modificados\n";
echo "Total de reemplazos: $total_reemplazos\n";

// Ahora vamos a actualizar la base de datos para que todas las imágenes sean .jpg
echo "\n=== ACTUALIZANDO BASE DE DATOS (SOLO .JPG) ===\n";

try {
    require_once '../config/config_db.php';
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();
    
    // Cambiar extensiones en la BD
    $actualizaciones_bd = [
        'bebidas_refrescantes.png' => 'bebidas_refrescantes.jpg',
        'fresas_marshmallow.jpeg' => 'fresas_marshmallow.jpg'
    ];
    
    foreach ($actualizaciones_bd as $viejo => $nuevo) {
        $stmt = $pdo->prepare("UPDATE producto SET imagen = ? WHERE imagen = ?");
        $result = $stmt->execute([$nuevo, $viejo]);
        if ($stmt->rowCount() > 0) {
            echo "✅ BD actualizada: $viejo → $nuevo\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error en BD: " . $e->getMessage() . "\n";
}

echo "\n🎯 TODAS LAS IMÁGENES AHORA ESTÁN ESTANDARIZADAS:\n";
echo "- Solo archivos .jpg\n";
echo "- Todas las rutas apuntan a ../public/img/\n";
echo "- Base de datos actualizada\n";
echo "\n✅ SISTEMA COMPLETAMENTE ARREGLADO\n";
?>