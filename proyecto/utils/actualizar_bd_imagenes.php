<?php
/**
 * Script para actualizar referencias de imágenes en la base de datos
 */

require_once '../config/config_db.php';

echo "=== ACTUALIZANDO REFERENCIAS DE IMÁGENES EN LA BD ===\n\n";

try {
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();
    
    // Mapeo de nombres antiguos a nuevos
    $mapeo_imagenes = [
        'soltera.jpg' => 'torta_soltera.jpg',
        'Cappuccino .jpg' => 'cappuccino.jpg',
        'Cheesecake de Fresa Delicioso.jpg' => 'cheesecake_fresa.jpg',
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
        'cereza.jpeg' => 'cereza.jpg',
        'manzana.jpeg' => 'manzana.jpg',
        'uvas.jpeg' => 'uvas.jpg',
        'cuca.jfif' => 'cuca.jpg',
        'pichos.jpeg' => 'pichos.jpg',
        'pene.jpg' => 'torta_falica.jpg'
    ];
    
    $actualizados = 0;
    
    // Verificar qué productos tienen imágenes que necesitan actualización
    $sql = "SELECT id, nombre, imagen FROM producto WHERE imagen IS NOT NULL AND imagen != ''";
    $stmt = $pdo->query($sql);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Productos con imágenes encontrados: " . count($productos) . "\n\n";
    
    foreach ($productos as $producto) {
        $imagen_actual = $producto['imagen'];
        
        if (isset($mapeo_imagenes[$imagen_actual])) {
            $nueva_imagen = $mapeo_imagenes[$imagen_actual];
            
            // Actualizar en la base de datos
            $update_sql = "UPDATE producto SET imagen = ? WHERE id = ?";
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->execute([$nueva_imagen, $producto['id']]);
            
            echo "✅ Actualizado: '{$producto['nombre']}'\n";
            echo "   Imagen antigua: $imagen_actual\n";
            echo "   Imagen nueva: $nueva_imagen\n\n";
            
            $actualizados++;
        } else {
            echo "→ Sin cambios: '{$producto['nombre']}' → $imagen_actual\n";
        }
    }
    
    echo "\n=== RESUMEN ===\n";
    echo "Productos actualizados: $actualizados\n";
    echo "Productos sin cambios: " . (count($productos) - $actualizados) . "\n";
    
    if ($actualizados > 0) {
        echo "\n✅ Base de datos actualizada correctamente!\n";
    } else {
        echo "\n→ No se necesitaron actualizaciones.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>