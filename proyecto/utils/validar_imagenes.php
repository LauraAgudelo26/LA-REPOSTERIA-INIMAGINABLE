<?php
/**
 * Script de validación de imágenes
 * Verifica que todas las imágenes referenciadas en el sistema sean accesibles
 */

// Incluir archivos necesarios con rutas corregidas
require_once '../config/config_db.php';
// Crear una instancia simplificada para validación
class ValidadorImagenes {
    
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
            'Fresas Marshmallow' => 'fresas_marshmallow.jpeg',
            'Fresas con Marshmallow' => 'fresas_marshmallow.jpeg',
            'Fresas con Masmelo' => 'fresas_marshmallow.jpeg',
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
            'Bebidas Refrescantes' => 'bebidas_refrescantes.png',
            'Bebidas' => 'bebidas_refrescantes.png',
            'Cappuccino' => 'cappuccino.jpg',
            'Cappuccino Artesanal' => 'cappuccino.jpg',
            
            // Otros
            'Pichos' => 'pichos.jpg'
        ];

        // Buscar imagen exacta
        if (isset($mapeo_imagenes[$nombre_producto])) {
            return 'public/img/' . $mapeo_imagenes[$nombre_producto];
        }

        // Buscar coincidencias parciales (búsqueda inteligente)
        foreach ($mapeo_imagenes as $producto => $imagen) {
            if (stripos($producto, $nombre_producto) !== false ||
                stripos($nombre_producto, $producto) !== false) {
                return 'public/img/' . $imagen;
            }
        }

        // Búsqueda por similitud de palabras clave
        $palabras_producto = explode(' ', strtolower($nombre_producto));
        foreach ($mapeo_imagenes as $producto => $imagen) {
            $palabras_mapeo = explode(' ', strtolower($producto));
            $coincidencias = array_intersect($palabras_producto, $palabras_mapeo);
            if (count($coincidencias) >= 1) {
                return 'public/img/' . $imagen;
            }
        }

        // Imagen por defecto
        return 'public/img/logo.jpg';
    }
}

echo "=== VALIDACIÓN DE SISTEMA DE IMÁGENES ===\n\n";

// Productos de ejemplo para validar (simularemos algunos productos comunes)
$productos_ejemplo = [
    ['Producto' => 'Cheesecake de Fresa Delicioso'],
    ['Producto' => 'Churros'],
    ['Producto' => 'Soltera'],
    ['Producto' => 'Donas'],
    ['Producto' => 'Cappuccino'],
    ['Producto' => 'Banano'],
    ['Producto' => 'Galletas con Chips de Chocolate'],
    ['Producto' => 'Torta Fálica'],
    ['Producto' => 'Fresas con Crema'],
    ['Producto' => 'Lingerie Cake']
];

$validador = new ValidadorImagenes();

try {
    // Usar productos de ejemplo para validación
    $productos = $productos_ejemplo;
    
    echo "Productos para validar: " . count($productos) . "\n\n";
    
    $imagenes_encontradas = [];
    $imagenes_faltantes = [];
    
    foreach ($productos as $producto) {
        $nombre_producto = $producto['Producto'];
        $ruta_imagen = $validador->obtenerImagenProducto($nombre_producto);
        
        // Convertir la ruta relativa a ruta absoluta para verificación
        $ruta_completa = realpath('../' . $ruta_imagen);
        
        if (file_exists($ruta_completa)) {
            $imagenes_encontradas[] = [
                'producto' => $nombre_producto,
                'imagen' => $ruta_imagen,
                'archivo' => basename($ruta_imagen)
            ];
            echo "✓ {$nombre_producto} → " . basename($ruta_imagen) . "\n";
        } else {
            $imagenes_faltantes[] = [
                'producto' => $nombre_producto,
                'imagen_esperada' => $ruta_imagen,
                'archivo' => basename($ruta_imagen)
            ];
            echo "✗ {$nombre_producto} → " . basename($ruta_imagen) . " (NO ENCONTRADA)\n";
        }
    }
    
    echo "\n=== RESUMEN DE VALIDACIÓN ===\n";
    echo "Imágenes encontradas: " . count($imagenes_encontradas) . "\n";
    echo "Imágenes faltantes: " . count($imagenes_faltantes) . "\n";
    
    if (!empty($imagenes_faltantes)) {
        echo "\n=== IMÁGENES FALTANTES ===\n";
        foreach ($imagenes_faltantes as $faltante) {
            echo "- Producto: {$faltante['producto']}\n";
            echo "  Archivo esperado: {$faltante['archivo']}\n";
            echo "  Ruta: {$faltante['imagen_esperada']}\n\n";
        }
        
        echo "RECOMENDACIONES:\n";
        echo "1. Verifique que los archivos de imagen existan en la carpeta public/img/\n";
        echo "2. Revise el mapeo en el controlador si los nombres no coinciden\n";
        echo "3. Use nombres de archivo simples sin espacios ni caracteres especiales\n";
    } else {
        echo "\n¡ÉXITO! Todas las imágenes fueron encontradas correctamente.\n";
    }
    
    // Verificar archivos de imagen no utilizados
    echo "\n=== VERIFICANDO ARCHIVOS NO UTILIZADOS ===\n";
    $directorio_imagenes = realpath('../public/img/');
    $archivos_imagen = [];
    
    if (is_dir($directorio_imagenes)) {
        $extensiones_validas = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'jfif'];
        $archivos = scandir($directorio_imagenes);
        
        foreach ($archivos as $archivo) {
            if ($archivo != '.' && $archivo != '..') {
                $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                if (in_array($extension, $extensiones_validas)) {
                    $archivos_imagen[] = $archivo;
                }
            }
        }
        
        $archivos_utilizados = array_map(function($img) {
            return basename($img['imagen']);
        }, $imagenes_encontradas);
        
        $archivos_no_utilizados = array_diff($archivos_imagen, $archivos_utilizados);
        
        echo "Archivos de imagen totales: " . count($archivos_imagen) . "\n";
        echo "Archivos utilizados: " . count($archivos_utilizados) . "\n";
        echo "Archivos no utilizados: " . count($archivos_no_utilizados) . "\n";
        
        if (!empty($archivos_no_utilizados)) {
            echo "\nArchivos no utilizados:\n";
            foreach ($archivos_no_utilizados as $archivo) {
                echo "- $archivo\n";
            }
            echo "\nEstos archivos pueden ser eliminados o asignados a productos.\n";
        }
    }
    
    // Generar sugerencias de nombres de productos basados en archivos
    if (!empty($archivos_no_utilizados)) {
        echo "\n=== SUGERENCIAS DE PRODUCTOS PARA ARCHIVOS NO UTILIZADOS ===\n";
        foreach ($archivos_no_utilizados as $archivo) {
            $nombre_base = pathinfo($archivo, PATHINFO_FILENAME);
            $sugerencia = ucwords(str_replace(['_', '-'], ' ', $nombre_base));
            echo "Archivo: $archivo → Posible producto: '$sugerencia'\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== VALIDACIÓN COMPLETADA ===\n";
?>