<?php
/**
 * Script de verificación de rutas de controladores
 */
echo "<h1>Verificación de Rutas del Sistema</h1>";

$rutas_a_probar = [
    'APIs de Productos' => [
        'http://localhost/proyecto/productos/api/api_productos.php',
        'http://localhost/proyecto/productos/api/api_productos_especiales.php',
        'http://localhost/proyecto/productos/api/api_productos_normales.php'
    ],
    'APIs de Categorías' => [
        'http://localhost/proyecto/categorias/api/api_categorias.php'
    ],
    'APIs de Usuarios' => [
        'http://localhost/proyecto/usuarios/api/api_usuario_info.php'
    ],
    'Controladores de Usuarios' => [
        'http://localhost/proyecto/usuarios/controllers/guardar_usuario.php',
        'http://localhost/proyecto/usuarios/controllers/procesar_login.php',
        'http://localhost/proyecto/usuarios/controllers/logout.php'
    ],
    'Vistas Principales' => [
        'http://localhost/proyecto/public/views/index.html',
        'http://localhost/proyecto/public/views/placeres_ocultos.html'
    ],
    'Vistas de Usuario' => [
        'http://localhost/proyecto/usuarios/views/iniciar_sesion.html',
        'http://localhost/proyecto/usuarios/views/registrarse.html'
    ]
];

foreach ($rutas_a_probar as $categoria => $urls) {
    echo "<h2>$categoria</h2>";
    echo "<ul>";
    
    foreach ($urls as $url) {
        echo "<li>";
        echo "<strong>$url</strong> - ";
        
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'method' => 'GET'
                ]
            ]);
            
            $headers = @get_headers($url, 1, $context);
            
            if ($headers && strpos($headers[0], '200') !== false) {
                echo "<span style='color: green;'>✅ OK (200)</span>";
            } elseif ($headers && strpos($headers[0], '404') !== false) {
                echo "<span style='color: red;'>❌ Not Found (404)</span>";
            } elseif ($headers) {
                echo "<span style='color: orange;'>⚠️ " . $headers[0] . "</span>";
            } else {
                echo "<span style='color: red;'>❌ No Response</span>";
            }
        } catch (Exception $e) {
            echo "<span style='color: red;'>❌ Error: " . $e->getMessage() . "</span>";
        }
        
        echo "</li>";
    }
    
    echo "</ul>";
}

echo "<h2>Rutas de Imágenes de Productos Especiales</h2>";
echo "<ul>";

$imagenes_especiales = [
    'Lingerie Cake.jpg',
    'Galletas de Sujetadores y Tangas.jpg', 
    'torta para despedida de soltera___.jpg',
    'pene.jpg',
    'pichos.jpeg',
    'fresas con masmelo.jpeg',
    'funny birthday cake.jpg'
];

foreach ($imagenes_especiales as $imagen) {
    $url_imagen = "http://localhost/proyecto/public/img/" . urlencode($imagen);
    echo "<li>";
    echo "<strong>$imagen</strong> - ";
    
    $headers = @get_headers($url_imagen);
    if ($headers && strpos($headers[0], '200') !== false) {
        echo "<span style='color: green;'>✅ Disponible</span>";
    } else {
        echo "<span style='color: red;'>❌ No encontrada</span>";
    }
    echo "</li>";
}

echo "</ul>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2 { color: #333; }
ul { margin-bottom: 20px; }
li { margin: 5px 0; }
</style>