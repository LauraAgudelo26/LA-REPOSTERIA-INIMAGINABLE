<?php
/**
 * Script para insertar productos especiales en la base de datos
 * Usar con precaución - solo ejecutar una vez
 */

require_once '../config/config_db.php';

try {
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();
    
    echo "<h1>Insertando Productos Especiales para Placeres Ocultos</h1>";
    
    // Array de productos especiales a insertar
    $productosEspeciales = [
        [
            'nombre' => 'Torta Lingerie Sensual',
            'descripcion' => 'Deliciosa torta temática para momentos íntimos especiales',
            'precio' => 85000,
            'stock' => 5,
            'imagen' => 'Lingerie Cake.jpg',
            'destacado' => 1
        ],
        [
            'nombre' => 'Galletas Sugestivas',
            'descripcion' => 'Galletas artesanales con diseños atrevidos y divertidos',
            'precio' => 35000,
            'stock' => 10,
            'imagen' => 'Galletas de Sujetadores y Tangas.jpg',
            'destacado' => 1
        ],
        [
            'nombre' => 'Torta Despedida de Soltera',
            'descripcion' => 'Espectacular torta temática para despedidas inolvidables',
            'precio' => 120000,
            'stock' => 3,
            'imagen' => 'torta para despedida de soltera___.jpg',
            'destacado' => 1
        ],
        [
            'nombre' => 'Postre Afrodisíaco',
            'descripcion' => 'Exótico postre con ingredientes especiales y formas sugestivas',
            'precio' => 45000,
            'stock' => 8,
            'imagen' => 'pene.jpg',
            'destacado' => 0
        ],
        [
            'nombre' => 'Delicia Tropical Sensual',
            'descripcion' => 'Combinación única de frutas exóticas con presentación atrevida',
            'precio' => 55000,
            'stock' => 6,
            'imagen' => 'pichos.jpeg',
            'destacado' => 0
        ],
        [
            'nombre' => 'Fresas Románticas',
            'descripcion' => 'Fresas cubiertas con malvaviscos en presentación romántica',
            'precio' => 25000,
            'stock' => 12,
            'imagen' => 'fresas con masmelo.jpeg',
            'destacado' => 0
        ]
    ];
    
    $sql = "INSERT INTO producto (nombre, descripcion, precio, stock, imagen, categoria_id, destacado, activo) 
            VALUES (?, ?, ?, ?, ?, 2, ?, 1)";
    $stmt = $pdo->prepare($sql);
    
    $insertados = 0;
    foreach ($productosEspeciales as $producto) {
        // Verificar si el producto ya existe
        $checkSql = "SELECT COUNT(*) FROM producto WHERE nombre = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$producto['nombre']]);
        
        if ($checkStmt->fetchColumn() == 0) {
            $resultado = $stmt->execute([
                $producto['nombre'],
                $producto['descripcion'],
                $producto['precio'],
                $producto['stock'],
                $producto['imagen'],
                $producto['destacado']
            ]);
            
            if ($resultado) {
                echo "<p style='color: green;'>✅ Insertado: {$producto['nombre']}</p>";
                $insertados++;
            } else {
                echo "<p style='color: red;'>❌ Error insertando: {$producto['nombre']}</p>";
            }
        } else {
            echo "<p style='color: orange;'>⚠️ Ya existe: {$producto['nombre']}</p>";
        }
    }
    
    echo "<h2>Resumen:</h2>";
    echo "<p><strong>Productos insertados: {$insertados}</strong></p>";
    
    // Mostrar productos especiales actuales
    echo "<h2>Productos Especiales en la Base de Datos:</h2>";
    $listSql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM producto p 
                JOIN categoria c ON p.categoria_id = c.id 
                WHERE p.categoria_id = 2 AND p.activo = 1
                ORDER BY p.destacado DESC, p.nombre ASC";
    $listStmt = $pdo->prepare($listSql);
    $listStmt->execute();
    $productos = $listStmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Precio</th><th>Stock</th><th>Imagen</th><th>Destacado</th></tr>";
    
    foreach ($productos as $producto) {
        $destacado = $producto['destacado'] ? '⭐' : '';
        echo "<tr>";
        echo "<td>{$producto['id']}</td>";
        echo "<td>{$producto['nombre']} {$destacado}</td>";
        echo "<td>$" . number_format($producto['precio'], 0, ',', '.') . "</td>";
        echo "<td>{$producto['stock']}</td>";
        echo "<td>{$producto['imagen']}</td>";
        echo "<td>" . ($producto['destacado'] ? 'Sí' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { margin-top: 20px; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
</style>