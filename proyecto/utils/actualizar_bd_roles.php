<?php
/**
 * Script para actualizar la base de datos con sistema de roles
 * actualizar_bd_roles.php
 */

require_once '../config/config_db.php';

try {
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();
    
    echo "<h2>Actualizando Base de Datos con Sistema de Roles</h2>";
    
    // Verificar si la columna 'rol' ya existe
    $checkRol = $pdo->query("SHOW COLUMNS FROM cliente LIKE 'rol'");
    if ($checkRol->rowCount() == 0) {
        echo "<p>Agregando columna 'rol' a la tabla cliente...</p>";
        $pdo->exec("ALTER TABLE cliente ADD COLUMN rol ENUM('admin', 'cliente') DEFAULT 'cliente' AFTER password");
        echo "<p style='color: green;'>✅ Columna 'rol' agregada exitosamente</p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ La columna 'rol' ya existe</p>";
    }
    
    // Verificar si la columna 'nombre_usuario' ya existe
    $checkUsuario = $pdo->query("SHOW COLUMNS FROM cliente LIKE 'nombre_usuario'");
    if ($checkUsuario->rowCount() == 0) {
        echo "<p>Agregando columna 'nombre_usuario' a la tabla cliente...</p>";
        $pdo->exec("ALTER TABLE cliente ADD COLUMN nombre_usuario VARCHAR(50) UNIQUE AFTER apellido");
        echo "<p style='color: green;'>✅ Columna 'nombre_usuario' agregada exitosamente</p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ La columna 'nombre_usuario' ya existe</p>";
    }
    
    // Verificar si existen campos adicionales
    $columnas_adicionales = ['tipo_documento', 'numero_documento', 'activo', 'fecha_registro', 'ultimo_acceso'];
    
    foreach ($columnas_adicionales as $columna) {
        $check = $pdo->query("SHOW COLUMNS FROM cliente LIKE '$columna'");
        if ($check->rowCount() == 0) {
            switch ($columna) {
                case 'tipo_documento':
                    $pdo->exec("ALTER TABLE cliente ADD COLUMN tipo_documento ENUM('cc', 'ti', 'dni', 'pasaporte', 'otro') DEFAULT 'cc' AFTER direccion");
                    break;
                case 'numero_documento':
                    $pdo->exec("ALTER TABLE cliente ADD COLUMN numero_documento VARCHAR(50) AFTER tipo_documento");
                    break;
                case 'activo':
                    $pdo->exec("ALTER TABLE cliente ADD COLUMN activo BOOLEAN DEFAULT TRUE AFTER rol");
                    break;
                case 'fecha_registro':
                    $pdo->exec("ALTER TABLE cliente ADD COLUMN fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER activo");
                    break;
                case 'ultimo_acceso':
                    $pdo->exec("ALTER TABLE cliente ADD COLUMN ultimo_acceso TIMESTAMP NULL AFTER fecha_registro");
                    break;
            }
            echo "<p style='color: green;'>✅ Columna '$columna' agregada exitosamente</p>";
        } else {
            echo "<p style='color: blue;'>ℹ️ La columna '$columna' ya existe</p>";
        }
    }
    
    // Actualizar usuarios existentes con rol admin
    echo "<p>Actualizando roles de usuarios existentes...</p>";
    
    // Verificar si el admin ya existe
    $checkAdmin = $pdo->prepare("SELECT id FROM cliente WHERE email = ?");
    $checkAdmin->execute(['admin@placeresocultos.com']);
    
    if ($checkAdmin->rowCount() == 0) {
        // Crear usuario admin
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $insertAdmin = $pdo->prepare("INSERT INTO cliente (nombre, apellido, nombre_usuario, email, telefono, direccion, password, rol) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insertAdmin->execute(['Administrador', 'Sistema', 'admin', 'admin@placeresocultos.com', '1234567890', 'Dirección Admin', $adminPassword, 'admin']);
        echo "<p style='color: green;'>✅ Usuario administrador creado</p>";
    } else {
        // Actualizar usuario existente a admin
        $updateAdmin = $pdo->prepare("UPDATE cliente SET rol = 'admin', nombre_usuario = 'admin' WHERE email = ?");
        $updateAdmin->execute(['admin@placeresocultos.com']);
        echo "<p style='color: green;'>✅ Usuario admin actualizado</p>";
    }
    
    // Verificar si el cliente de prueba ya existe
    $checkCliente = $pdo->prepare("SELECT id FROM cliente WHERE email = ?");
    $checkCliente->execute(['cliente@test.com']);
    
    if ($checkCliente->rowCount() == 0) {
        // Crear usuario cliente
        $clientePassword = password_hash('cliente123', PASSWORD_DEFAULT);
        $insertCliente = $pdo->prepare("INSERT INTO cliente (nombre, apellido, nombre_usuario, email, telefono, direccion, password, rol) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insertCliente->execute(['Cliente', 'Prueba', 'cliente', 'cliente@test.com', '0987654321', 'Dirección Cliente', $clientePassword, 'cliente']);
        echo "<p style='color: green;'>✅ Usuario cliente de prueba creado</p>";
    } else {
        // Actualizar usuario existente a cliente
        $updateCliente = $pdo->prepare("UPDATE cliente SET rol = 'cliente', nombre_usuario = 'cliente' WHERE email = ?");
        $updateCliente->execute(['cliente@test.com']);
        echo "<p style='color: green;'>✅ Usuario cliente de prueba actualizado</p>";
    }
    
    // Verificar estructura de la tabla producto
    echo "<h3>Verificando tabla de productos...</h3>";
    
    $tables = $pdo->query("SHOW TABLES LIKE 'producto'")->fetchAll();
    if (empty($tables)) {
        echo "<p>Creando tabla de productos...</p>";
        $createProducto = "
        CREATE TABLE producto (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(200) NOT NULL,
            descripcion TEXT,
            precio DECIMAL(10,2) NOT NULL,
            categoria_id INT,
            imagen VARCHAR(255),
            stock INT DEFAULT 0,
            activo BOOLEAN DEFAULT TRUE,
            destacado BOOLEAN DEFAULT FALSE,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_nombre (nombre),
            INDEX idx_precio (precio),
            INDEX idx_activo (activo),
            INDEX idx_destacado (destacado)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($createProducto);
        echo "<p style='color: green;'>✅ Tabla producto creada</p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ Tabla producto ya existe</p>";
    }
    
    // Verificar tabla categoria
    $tables = $pdo->query("SHOW TABLES LIKE 'categoria'")->fetchAll();
    if (empty($tables)) {
        echo "<p>Creando tabla de categorías...</p>";
        $createCategoria = "
        CREATE TABLE categoria (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL UNIQUE,
            descripcion TEXT,
            imagen VARCHAR(255),
            activa BOOLEAN DEFAULT TRUE,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            orden_mostrar INT DEFAULT 0,
            INDEX idx_nombre (nombre)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($createCategoria);
        echo "<p style='color: green;'>✅ Tabla categoria creada</p>";
        
        // Insertar categorías de ejemplo
        $categorias = [
            ['Postres Clásicos', 'Deliciosos postres tradicionales', 1],
            ['Tortas Especiales', 'Tortas únicas y creativas', 2],
            ['Bebidas', 'Bebidas refrescantes y calientes', 3],
            ['Frutas Frescas', 'Frutas de temporada y preparaciones', 4],
            ['Dulces Artesanales', 'Dulces hechos en casa', 5]
        ];
        
        $insertCategoria = $pdo->prepare("INSERT INTO categoria (nombre, descripcion, orden_mostrar) VALUES (?, ?, ?)");
        foreach ($categorias as $cat) {
            $insertCategoria->execute($cat);
        }
        echo "<p style='color: green;'>✅ Categorías de ejemplo insertadas</p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ Tabla categoria ya existe</p>";
    }
    
    // Insertar productos de ejemplo si no existen
    $countProductos = $pdo->query("SELECT COUNT(*) FROM producto")->fetchColumn();
    if ($countProductos == 0) {
        echo "<p>Insertando productos de ejemplo...</p>";
        
        $productos = [
            ['Cheesecake de Fresa', 'Delicioso cheesecake con fresas frescas', 25000.00, 1, 'Cheesecake de Fresa Delicioso.jpg', 10, 1],
            ['Torta de Chocolate', 'Rica torta de chocolate con cobertura', 30000.00, 2, 'funny birthday cake.jpg', 8, 1],
            ['Cappuccino Especial', 'Cappuccino cremoso con arte latte', 8000.00, 3, 'Cappuccino .jpg', 50, 0],
            ['Fresas con Crema', 'Fresas frescas con crema batida', 12000.00, 4, 'fresas con crema.jpg', 20, 1],
            ['Churros Caseros', 'Churros recién hechos con azúcar', 6000.00, 5, 'churros.jpg', 30, 0],
            ['Mini Tarta de Frutas', 'Pequeña tarta con frutas mixtas', 15000.00, 1, 'mini tarta de frutas.jpg', 15, 0],
            ['Postre de Oreo', 'Cremoso postre con galletas Oreo', 18000.00, 1, 'postre de oreo.jfif', 12, 1],
            ['Galletas con Chips', 'Galletas caseras con chips de chocolate', 5000.00, 5, 'galletas con chips de chocolate.jpg', 40, 0]
        ];
        
        $insertProducto = $pdo->prepare("INSERT INTO producto (nombre, descripcion, precio, categoria_id, imagen, stock, destacado) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($productos as $prod) {
            $insertProducto->execute($prod);
        }
        echo "<p style='color: green;'>✅ Productos de ejemplo insertados</p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ Ya existen productos en la base de datos ($countProductos productos)</p>";
    }
    
    echo "<h3 style='color: green;'>🎉 ¡Base de datos actualizada exitosamente!</h3>";
    echo "<p><strong>Usuarios disponibles:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Admin:</strong> admin@placeresocultos.com / admin123</li>";
    echo "<li><strong>Cliente:</strong> cliente@test.com / cliente123</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
