<?php
/**
 * Script para renombrar imágenes con nombres problemáticos 
 * y actualizar referencias en el sistema
 */

// Configuración de rutas
$directorio_imagenes = '../public/img/';
$ruta_completa = realpath($directorio_imagenes);

echo "=== RENOMBRANDO IMÁGENES PROBLEMÁTICAS ===\n";
echo "Directorio: $ruta_completa\n\n";

// Mapeo de nombres actuales a nombres simples
$renombrados = [
    'Cappuccino .jpg' => 'cappuccino.jpg',
    'Cheesecake de Fresa Delicioso.jpg' => 'cheesecake_fresa.jpg',
    'churros.jpg' => 'churros.jpg', // Ya está bien
    'bebidas refrescantes.png' => 'bebidas_refrescantes.png',
    'fresas con crema.jpg' => 'fresas_crema.jpg',
    'fresas con masmelo.jpeg' => 'fresas_marshmallow.jpeg',
    'funny birthday cake.jpg' => 'torta_cumpleanos.jpg',
    'galletas con chips de chocolate.jpg' => 'galletas_chocolate.jpg',
    'Galletas de Sujetadores y Tangas.jpg' => 'galletas_especiales.jpg',
    'Lingerie Cake.jpg' => 'torta_lingerie.jpg',
    'mini tarta de frutas.jpg' => 'mini_tarta_frutas.jpg',
    'logo.crdownload.jpg' => 'logo.jpg',
    'postre de gelatina.jpg' => 'postre_gelatina.jpg',
    'postre de limon.jpg' => 'postre_limon.jpg',
    'postre de oreo.jfif' => 'postre_oreo.jpg',
    'rollos de canela.jpg' => 'rollos_canela.jpg',
    'soltera.jpg' => 'torta_soltera.jpg',
    'banano.jpg' => 'banano.jpg', // Ya está bien
    'cereza.jpeg' => 'cereza.jpg',
    'manzana.jpeg' => 'manzana.jpg',
    'uvas.jpeg' => 'uvas.jpg',
    'donas.jpg' => 'donas.jpg', // Ya está bien
    'churros.jpg' => 'churros.jpg', // Ya está bien
    'cuca.jfif' => 'cuca.jpg',
    'pichos.jpeg' => 'pichos.jpg',
    'pene.jpg' => 'torta_falica.jpg'
];

$archivos_renombrados = [];
$errores = [];

// Verificar que el directorio existe
if (!is_dir($ruta_completa)) {
    die("Error: No se puede acceder al directorio de imágenes: $ruta_completa\n");
}

// Escanear archivos actuales
$archivos_actuales = scandir($ruta_completa);
echo "Archivos encontrados en el directorio:\n";
foreach ($archivos_actuales as $archivo) {
    if ($archivo != '.' && $archivo != '..') {
        echo "- $archivo\n";
    }
}
echo "\n";

// Procesar renombrados
foreach ($renombrados as $nombre_actual => $nombre_nuevo) {
    $ruta_actual = $ruta_completa . '/' . $nombre_actual;
    $ruta_nueva = $ruta_completa . '/' . $nombre_nuevo;
    
    if (file_exists($ruta_actual)) {
        if ($nombre_actual !== $nombre_nuevo) {
            // Solo renombrar si el nombre ha cambiado
            if (rename($ruta_actual, $ruta_nueva)) {
                $archivos_renombrados[$nombre_actual] = $nombre_nuevo;
                echo "✓ Renombrado: '$nombre_actual' → '$nombre_nuevo'\n";
            } else {
                $errores[] = "Error al renombrar '$nombre_actual' a '$nombre_nuevo'";
                echo "✗ Error al renombrar: '$nombre_actual'\n";
            }
        } else {
            echo "→ Sin cambios: '$nombre_actual'\n";
        }
    } else {
        echo "⚠ Archivo no encontrado: '$nombre_actual'\n";
    }
}

echo "\n=== RESULTADOS ===\n";
echo "Archivos renombrados: " . count($archivos_renombrados) . "\n";
echo "Errores: " . count($errores) . "\n";

if (!empty($errores)) {
    echo "\nErrores encontrados:\n";
    foreach ($errores as $error) {
        echo "- $error\n";
    }
}

// Crear mapeo actualizado para el controlador
echo "\n=== MAPEO ACTUALIZADO PARA EL CONTROLADOR ===\n";
echo "Copie este código en el método obtenerImagenProducto():\n\n";

$mapeo_nuevo = [
    // Postres
    'Cheesecake de Fresa Delicioso' => 'cheesecake_fresa.jpg',
    'Cheesecake de Fresa' => 'cheesecake_fresa.jpg',
    'Churros Tradicionales' => 'churros.jpg',
    'Churros' => 'churros.jpg',
    'Donas Glaseadas' => 'donas.jpg',
    'Donas' => 'donas.jpg',
    'Postre de Gelatina' => 'postre_gelatina.jpg',
    'Postre de Limón' => 'postre_limon.jpg',
    'Postre de Limon' => 'postre_limon.jpg',
    'Postre de Oreo' => 'postre_oreo.jpg',
    'Rollos de Canela' => 'rollos_canela.jpg',
    'Cuca Tradicional' => 'cuca.jpg',
    'Cuca' => 'cuca.jpg',
    
    // Frutas
    'Fresas con Crema' => 'fresas_crema.jpg',
    'Fresas con Marshmallow' => 'fresas_marshmallow.jpeg',
    'Fresas con Masmelo' => 'fresas_marshmallow.jpeg',
    'Banano Fresco' => 'banano.jpg',
    'Banano' => 'banano.jpg',
    'Cerezas Selectas' => 'cereza.jpg',
    'Cereza' => 'cereza.jpg',
    'Manzanas Rojas' => 'manzana.jpg',
    'Manzana' => 'manzana.jpg',
    'Uvas Frescas' => 'uvas.jpg',
    'Uvas' => 'uvas.jpg',
    
    // Pasteles Especiales
    'Torta para Despedida de Soltera' => 'torta_soltera.jpg',
    'Torta Despedida Soltera' => 'torta_soltera.jpg',
    'Despedida Soltera' => 'torta_soltera.jpg',
    'Soltera' => 'torta_soltera.jpg',
    'Mini Tarta de Frutas' => 'mini_tarta_frutas.jpg',
    'Funny Birthday Cake' => 'torta_cumpleanos.jpg',
    'Torta Cumpleanos' => 'torta_cumpleanos.jpg',
    'Birthday Cake' => 'torta_cumpleanos.jpg',
    'Galletas de Sujetadores y Tangas' => 'galletas_especiales.jpg',
    'Galletas Especiales' => 'galletas_especiales.jpg',
    'Lingerie Cake' => 'torta_lingerie.jpg',
    'Torta Lingerie' => 'torta_lingerie.jpg',
    'Torta Fálica' => 'torta_falica.jpg',
    'Torta Falica' => 'torta_falica.jpg',
    
    // Galletas
    'Galletas con Chips de Chocolate' => 'galletas_chocolate.jpg',
    'Galletas con Chips' => 'galletas_chocolate.jpg',
    'Galletas Chocolate' => 'galletas_chocolate.jpg',
    
    // Bebidas
    'Cappuccino Artesanal' => 'cappuccino.jpg',
    'Cappuccino' => 'cappuccino.jpg',
    'Bebidas Refrescantes' => 'bebidas_refrescantes.png',
    'Bebidas' => 'bebidas_refrescantes.png',
    
    // Otros
    'Pichos' => 'pichos.jpg'
];

echo "<?php\n";
echo "\$mapeo_imagenes = [\n";
foreach ($mapeo_nuevo as $producto => $imagen) {
    echo "    '$producto' => '$imagen',\n";
}
echo "];\n";
echo "?>\n";

echo "\n=== PROCESO COMPLETADO ===\n";
echo "Recuerde actualizar el controlador de productos con el nuevo mapeo.\n";
?>