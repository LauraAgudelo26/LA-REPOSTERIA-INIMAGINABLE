<?php
/**
 * Script para crear automáticamente la base de datos placeres_ocultos
 * crear_bd_automatico.php
 */

// Configuración de conexión sin especificar base de datos
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    echo "<h2>🚀 Creando Base de Datos 'placeres_ocultos'</h2>";
    
    // Conectar a MySQL sin especificar base de datos
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✅ Conectado a MySQL Server</p>";
    
    // Crear base de datos si no existe
    $sql = "CREATE DATABASE IF NOT EXISTS placeres_ocultos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $pdo->exec($sql);
    echo "<p>✅ Base de datos 'placeres_ocultos' creada/verificada</p>";
    
    // Seleccionar la base de datos
    $pdo->exec("USE placeres_ocultos");
    echo "<p>✅ Base de datos seleccionada</p>";
    
    // Leer y ejecutar el script SQL
    $sqlFile = __DIR__ . '/BASE_DE_DATOS_AUTO.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        
        // Dividir en consultas individuales
        $queries = array_filter(array_map('trim', explode(';', $sql)));
        
        $ejecutadas = 0;
        foreach ($queries as $query) {
            if (!empty($query) && !preg_match('/^(--|#)/', $query)) {
                try {
                    // Saltar comandos que no necesitamos
                    if (stripos($query, 'CREATE DATABASE') !== false || 
                        stripos($query, 'USE placeres_ocultos') !== false) {
                        continue;
                    }
                    
                    $pdo->exec($query);
                    $ejecutadas++;
                } catch (PDOException $e) {
                    if (stripos($e->getMessage(), 'already exists') === false &&
                        stripos($e->getMessage(), 'Duplicate entry') === false) {
                        echo "<p>⚠️ Error en consulta: " . htmlspecialchars($e->getMessage()) . "</p>";
                    }
                }
            }
        }
        
        echo "<p>✅ Ejecutadas $ejecutadas consultas SQL</p>";
        
        // Verificar que se crearon las tablas
        $result = $pdo->query("SHOW TABLES");
        $tables = $result->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<h3>📊 Tablas creadas:</h3>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>✅ $table</li>";
        }
        echo "</ul>";
        
        // Verificar usuarios creados
        $result = $pdo->query("SELECT id, nombre, email, rol FROM cliente");
        $usuarios = $result->fetchAll();
        
        echo "<h3>👥 Usuarios creados:</h3>";
        echo "<ul>";
        foreach ($usuarios as $usuario) {
            echo "<li>✅ {$usuario['nombre']} ({$usuario['email']}) - Rol: {$usuario['rol']}</li>";
        }
        echo "</ul>";
        
        echo "<h2>🎉 ¡Base de datos creada exitosamente!</h2>";
        echo "<p><a href='test_db_connection.php'>🔍 Verificar conexión</a></p>";
        echo "<p><a href='iniciar_sesion.html'>🔐 Probar login</a></p>";
        
    } else {
        echo "<p>❌ No se encontró el archivo BASE_DE_DATOS_AUTO.sql</p>";
    }
    
} catch (PDOException $e) {
    echo "<h3>❌ Error:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
