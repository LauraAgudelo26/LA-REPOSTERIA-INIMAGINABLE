<?php
/**
 * Script mejorado para crear la base de datos paso a paso
 * crear_bd_paso_a_paso.php
 */

// Configuración de conexión
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'placeres_ocultos';

try {
    echo "<h2>🚀 Creando Base de Datos 'placeres_ocultos' Paso a Paso</h2>";
    
    // PASO 1: Conectar sin especificar base de datos
    $pdo = new PDO("mysql:host=$host", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
    ]);
    echo "<p>✅ Conectado a MySQL Server</p>";
    
    // PASO 2: Crear base de datos
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p>✅ Base de datos '$dbname' creada/verificada</p>";
    
    // PASO 3: Conectar a la base de datos específica
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
    ]);
    echo "<p>✅ Conectado a base de datos '$dbname'</p>";
    
    // PASO 4: Eliminar tablas existentes (si existen)
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    $tablasAEliminar = ['pedido_detalle', 'pedido', 'producto', 'categoria', 'cliente'];
    foreach ($tablasAEliminar as $tabla) {
        try {
            $pdo->exec("DROP TABLE IF EXISTS $tabla");
            echo "<p>🗑️ Tabla '$tabla' eliminada (si existía)</p>";
        } catch (Exception $e) {
            echo "<p>⚠️ No se pudo eliminar tabla '$tabla': " . $e->getMessage() . "</p>";
        }
    }
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "<p>✅ Tablas anteriores limpiadas</p>";
    
    // PASO 5: Crear tabla cliente
    $sqlCliente = "
        CREATE TABLE cliente (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            apellido VARCHAR(100) NOT NULL,
            nombre_usuario VARCHAR(50) UNIQUE,
            email VARCHAR(150) UNIQUE NOT NULL,
            telefono VARCHAR(20),
            direccion TEXT,
            tipo_documento ENUM('cc', 'ti', 'dni', 'pasaporte', 'otro') DEFAULT 'cc',
            numero_documento VARCHAR(50),
            password VARCHAR(255) NOT NULL,
            rol ENUM('admin', 'cliente') DEFAULT 'cliente',
            activo TINYINT(1) DEFAULT 1,
            fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            ultimo_acceso TIMESTAMP NULL,
            INDEX idx_email (email),
            INDEX idx_usuario (nombre_usuario),
            INDEX idx_rol (rol)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    $pdo->exec($sqlCliente);
    echo "<p>✅ Tabla 'cliente' creada</p>";
    
    // PASO 6: Crear tabla categoria
    $sqlCategoria = "
        CREATE TABLE categoria (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL UNIQUE,
            descripcion TEXT,
            imagen VARCHAR(255),
            activa TINYINT(1) DEFAULT 1,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            orden_mostrar INT(11) DEFAULT 0,
            INDEX idx_nombre (nombre)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    $pdo->exec($sqlCategoria);
    echo "<p>✅ Tabla 'categoria' creada</p>";
    
    // PASO 7: Crear tabla producto
    $sqlProducto = "
        CREATE TABLE producto (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(200) NOT NULL,
            descripcion TEXT,
            precio DECIMAL(10,2) NOT NULL,
            categoria_id INT(11),
            imagen VARCHAR(255),
            stock INT(11) DEFAULT 0,
            activo TINYINT(1) DEFAULT 1,
            destacado TINYINT(1) DEFAULT 0,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_nombre (nombre),
            INDEX idx_precio (precio),
            INDEX idx_categoria (categoria_id),
            INDEX idx_activo (activo),
            INDEX idx_destacado (destacado)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    $pdo->exec($sqlProducto);
    echo "<p>✅ Tabla 'producto' creada</p>";
    
    // PASO 8: Crear tabla pedido
    $sqlPedido = "
        CREATE TABLE pedido (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            cliente_id INT(11) NOT NULL,
            total DECIMAL(10,2) NOT NULL,
            estado ENUM('pendiente', 'confirmado', 'en_preparacion', 'listo', 'entregado', 'cancelado') DEFAULT 'pendiente',
            fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            fecha_entrega TIMESTAMP NULL,
            direccion_entrega TEXT,
            telefono_entrega VARCHAR(20),
            notas TEXT,
            INDEX idx_cliente (cliente_id),
            INDEX idx_estado (estado),
            INDEX idx_fecha (fecha_pedido)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    $pdo->exec($sqlPedido);
    echo "<p>✅ Tabla 'pedido' creada</p>";
    
    // PASO 9: Crear tabla pedido_detalle
    $sqlPedidoDetalle = "
        CREATE TABLE pedido_detalle (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            pedido_id INT(11) NOT NULL,
            producto_id INT(11) NOT NULL,
            cantidad INT(11) NOT NULL DEFAULT 1,
            precio_unitario DECIMAL(10,2) NOT NULL,
            subtotal DECIMAL(10,2) NOT NULL,
            INDEX idx_pedido (pedido_id),
            INDEX idx_producto (producto_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    $pdo->exec($sqlPedidoDetalle);
    echo "<p>✅ Tabla 'pedido_detalle' creada</p>";
    
    // PASO 10: Agregar relaciones de clave foránea
    $pdo->exec("ALTER TABLE producto ADD CONSTRAINT fk_producto_categoria FOREIGN KEY (categoria_id) REFERENCES categoria(id) ON DELETE SET NULL");
    echo "<p>🔗 Relación producto-categoria creada</p>";
    
    $pdo->exec("ALTER TABLE pedido ADD CONSTRAINT fk_pedido_cliente FOREIGN KEY (cliente_id) REFERENCES cliente(id) ON DELETE CASCADE");
    echo "<p>🔗 Relación pedido-cliente creada</p>";
    
    $pdo->exec("ALTER TABLE pedido_detalle ADD CONSTRAINT fk_detalle_pedido FOREIGN KEY (pedido_id) REFERENCES pedido(id) ON DELETE CASCADE");
    echo "<p>🔗 Relación pedido_detalle-pedido creada</p>";
    
    $pdo->exec("ALTER TABLE pedido_detalle ADD CONSTRAINT fk_detalle_producto FOREIGN KEY (producto_id) REFERENCES producto(id) ON DELETE CASCADE");
    echo "<p>🔗 Relación pedido_detalle-producto creada</p>";
    
    // PASO 11: Insertar usuarios por defecto
    $hashAdmin = '$2y$10$PJsNoIa2aw3VCB.q54/ykeMw/KvrYhTKD1ePJKt/zcMb/CL3jrwES';  // admin123
    $hashCliente = '$2y$10$0Bi.w9jqq0ylkmsxzMdSlO0SFi8Gpc1eXqicY2JHEQUCSLYrL022S';  // cliente123
    
    $sqlInsertUsuarios = "
        INSERT INTO cliente (nombre, apellido, nombre_usuario, email, telefono, direccion, password, rol) VALUES
        ('Administrador', 'Sistema', 'admin', 'admin@placeresocultos.com', '1234567890', 'Dirección Admin', ?, 'admin'),
        ('Cliente', 'Prueba', 'cliente', 'cliente@test.com', '0987654321', 'Dirección Cliente', ?, 'cliente')
    ";
    $stmt = $pdo->prepare($sqlInsertUsuarios);
    $stmt->execute([$hashAdmin, $hashCliente]);
    echo "<p>👥 Usuarios por defecto creados</p>";
    
    // PASO 12: Insertar categorías
    $sqlInsertCategorias = "
        INSERT INTO categoria (nombre, descripcion, orden_mostrar) VALUES
        ('Postres Clásicos', 'Deliciosos postres tradicionales', 1),
        ('Tortas Especiales', 'Tortas únicas y creativas', 2),
        ('Bebidas', 'Bebidas refrescantes y calientes', 3),
        ('Frutas Frescas', 'Frutas de temporada y preparaciones', 4),
        ('Dulces Artesanales', 'Dulces hechos en casa', 5)
    ";
    $pdo->exec($sqlInsertCategorias);
    echo "<p>📂 Categorías creadas</p>";
    
    // PASO 13: Insertar productos
    $sqlInsertProductos = "
        INSERT INTO producto (nombre, descripcion, precio, categoria_id, imagen, stock, destacado) VALUES
        ('Cheesecake de Fresa', 'Delicioso cheesecake con fresas frescas', 25000.00, 1, 'Cheesecake de Fresa Delicioso.jpg', 10, 1),
        ('Torta de Chocolate', 'Rica torta de chocolate con cobertura', 30000.00, 2, 'funny birthday cake.jpg', 8, 1),
        ('Cappuccino Especial', 'Cappuccino cremoso con arte latte', 8000.00, 3, 'Cappuccino .jpg', 50, 0),
        ('Fresas con Crema', 'Fresas frescas con crema batida', 12000.00, 4, 'fresas con crema.jpg', 20, 1),
        ('Churros Caseros', 'Churros recién hechos con azúcar', 6000.00, 5, 'churros.jpg', 30, 0),
        ('Mini Tarta de Frutas', 'Pequeña tarta con frutas mixtas', 15000.00, 1, 'mini tarta de frutas.jpg', 15, 0),
        ('Postre de Oreo', 'Cremoso postre con galletas Oreo', 18000.00, 1, 'postre de oreo.jfif', 12, 1),
        ('Galletas con Chips', 'Galletas caseras con chips de chocolate', 5000.00, 5, 'galletas con chips de chocolate.jpg', 40, 0),
        ('Rollos de Canela', 'Suaves rollos de canela recién horneados', 8000.00, 5, 'rollos de canela.jpg', 25, 0),
        ('Postre de Limón', 'Refrescante postre de limón', 14000.00, 1, 'postre de limon.jpg', 18, 0)
    ";
    $pdo->exec($sqlInsertProductos);
    echo "<p>🍰 Productos creados</p>";
    
    // PASO 14: Verificar resultados
    $result = $pdo->query("SHOW TABLES");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    echo "<h3>📊 Tablas creadas:</h3><ul>";
    foreach ($tables as $table) {
        echo "<li>✅ $table</li>";
    }
    echo "</ul>";
    
    // Verificar usuarios
    $result = $pdo->query("SELECT id, nombre, email, rol FROM cliente");
    $usuarios = $result->fetchAll();
    echo "<h3>👥 Usuarios creados:</h3><ul>";
    foreach ($usuarios as $usuario) {
        echo "<li>✅ {$usuario['nombre']} ({$usuario['email']}) - Rol: {$usuario['rol']}</li>";
    }
    echo "</ul>";
    
    // Verificar productos
    $result = $pdo->query("SELECT COUNT(*) as total FROM producto");
    $totalProductos = $result->fetch()['total'];
    echo "<h3>🍰 Productos: $totalProductos productos creados</h3>";
    
    echo "<h2>🎉 ¡Base de datos creada exitosamente!</h2>";
    echo "<p><a href='test_db_connection.php' class='btn btn-primary'>🔍 Verificar conexión</a></p>";
    echo "<p><a href='index.html' class='btn btn-success'>🏠 Ver página principal</a></p>";
    echo "<p><a href='iniciar_sesion.html' class='btn btn-info'>🔐 Probar login</a></p>";
    
} catch (PDOException $e) {
    echo "<h3>❌ Error de Base de Datos:</h3>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Código de error:</strong> " . $e->getCode() . "</p>";
} catch (Exception $e) {
    echo "<h3>❌ Error General:</h3>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
h3 { color: #555; margin-top: 30px; }
p { margin: 8px 0; }
.btn { display: inline-block; padding: 10px 20px; margin: 5px; text-decoration: none; border-radius: 5px; color: white; }
.btn-primary { background-color: #007bff; }
.btn-success { background-color: #28a745; }
.btn-info { background-color: #17a2b8; }
ul { margin: 10px 0; }
li { margin: 5px 0; }
</style>
