<?php
/**
 * Debug de rutas de imágenes
 */

echo "=== DEBUG DE RUTAS DE IMÁGENES ===\n\n";

// Probar diferentes rutas
$imagen = 'torta_soltera.jpg';

echo "Archivo objetivo: $imagen\n\n";

// Desde api/
$ruta1 = __DIR__ . '/../public/img/' . $imagen;
echo "Ruta 1 (desde api): $ruta1\n";
echo "Existe: " . (file_exists($ruta1) ? "✅ SÍ" : "❌ NO") . "\n\n";

$ruta2 = __DIR__ . '/../../public/img/' . $imagen;
echo "Ruta 2 (desde api): $ruta2\n";
echo "Existe: " . (file_exists($ruta2) ? "✅ SÍ" : "❌ NO") . "\n\n";

// Ruta absoluta
$ruta3 = realpath(__DIR__ . '/../../public/img/') . '/' . $imagen;
echo "Ruta 3 (absoluta): $ruta3\n";
echo "Existe: " . (file_exists($ruta3) ? "✅ SÍ" : "❌ NO") . "\n\n";

// Listar contenido del directorio
$dir = __DIR__ . '/../../public/img/';
echo "Contenido de $dir:\n";
if (is_dir($dir)) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "  - $file\n";
        }
    }
} else {
    echo "❌ Directorio no existe\n";
}

// Probar con imagen_url completa
$imagen_url = '../public/img/torta_soltera.jpg';
$ruta4 = __DIR__ . '/' . $imagen_url;
echo "\nRuta 4 (con imagen_url): $ruta4\n";
echo "Existe: " . (file_exists($ruta4) ? "✅ SÍ" : "❌ NO") . "\n";

?>