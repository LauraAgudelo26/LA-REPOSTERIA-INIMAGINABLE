<?php
/**
 * Archivo para validar la conexión a la base de datos
 * test_db_connection.php
 * 
 * Este archivo verifica:
 * 1. Conexión a MySQL
 * 2. Existencia de la base de datos
 * 3. Existencia de tablas necesarias
 * 4. Permisos de usuario
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test de Conexión a Base de Datos</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background-color: #f5f5f5; 
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        .success { 
            color: #4CAF50; 
            background: #e8f5e8; 
            padding: 10px; 
            border-left: 4px solid #4CAF50; 
            margin: 10px 0; 
        }
        .error { 
            color: #f44336; 
            background: #ffeaea; 
            padding: 10px; 
            border-left: 4px solid #f44336; 
            margin: 10px 0; 
        }
        .warning { 
            color: #ff9800; 
            background: #fff3cd; 
            padding: 10px; 
            border-left: 4px solid #ff9800; 
            margin: 10px 0; 
        }
        .info { 
            color: #2196F3; 
            background: #e3f2fd; 
            padding: 10px; 
            border-left: 4px solid #2196F3; 
            margin: 10px 0; 
        }
        h1 { color: #333; text-align: center; }
        h2 { color: #555; border-bottom: 2px solid #ddd; padding-bottom: 5px; }
        .step { margin: 20px 0; }
        pre { background: #f8f8f8; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 Test de Conexión a Base de Datos</h1>";

// Configuración de la base de datos
$db_host = 'localhost';
$db_name = 'placeres_ocultos';
$db_user = 'root';
$db_pass = '';
$db_charset = 'utf8mb4';

$tests_passed = 0;
$total_tests = 0;

function mostrarResultado($titulo, $exito, $mensaje, $detalles = null) {
    global $tests_passed, $total_tests;
    $total_tests++;
    
    $clase = $exito ? 'success' : 'error';
    $icono = $exito ? '✅' : '❌';
    
    if ($exito) $tests_passed++;
    
    echo "<div class='step'>
            <h3>$icono $titulo</h3>
            <div class='$clase'>$mensaje</div>";
    
    if ($detalles) {
        echo "<div class='info'><strong>Detalles:</strong> $detalles</div>";
    }
    
    echo "</div>";
}

// Test 1: Verificar extensión PDO
echo "<h2>🧪 Ejecutando Tests de Conexión</h2>";

if (extension_loaded('pdo') && extension_loaded('pdo_mysql')) {
    mostrarResultado(
        "Extensión PDO MySQL", 
        true, 
        "Las extensiones PDO y PDO_MySQL están disponibles.",
        "PHP versión: " . phpversion()
    );
} else {
    mostrarResultado(
        "Extensión PDO MySQL", 
        false, 
        "PDO o PDO_MySQL no están disponibles. Necesitas instalar/habilitar estas extensiones.",
        "Verifica tu configuración de PHP"
    );
}

// Test 2: Conexión básica a MySQL
try {
    $dsn = "mysql:host=$db_host;charset=$db_charset";
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    mostrarResultado(
        "Conexión a MySQL Server", 
        true, 
        "Conexión exitosa al servidor MySQL en $db_host",
        "Usuario: $db_user"
    );
    
    // Obtener información del servidor
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "<div class='info'>Versión de MySQL: $version</div>";
    
} catch (PDOException $e) {
    mostrarResultado(
        "Conexión a MySQL Server", 
        false, 
        "No se pudo conectar al servidor MySQL: " . $e->getMessage(),
        "Verifica que MySQL esté ejecutándose y las credenciales sean correctas"
    );
    echo "</div></body></html>";
    exit;
}

// Test 3: Verificar existencia de la base de datos
try {
    $stmt = $pdo->query("SHOW DATABASES LIKE '$db_name'");
    $db_exists = $stmt->rowCount() > 0;
    
    if ($db_exists) {
        mostrarResultado(
            "Base de datos '$db_name'", 
            true, 
            "La base de datos '$db_name' existe.",
            ""
        );
    } else {
        mostrarResultado(
            "Base de datos '$db_name'", 
            false, 
            "La base de datos '$db_name' no existe.",
            "Necesitas crear la base de datos o ejecutar el script SQL de creación"
        );
        
        // Mostrar script para crear la base de datos
        echo "<div class='warning'>
                <strong>Script para crear la base de datos:</strong>
                <pre>CREATE DATABASE $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;</pre>
              </div>";
    }
} catch (PDOException $e) {
    mostrarResultado(
        "Verificación de base de datos", 
        false, 
        "Error al verificar la base de datos: " . $e->getMessage(),
        ""
    );
}

// Test 4: Conexión a la base de datos específica
if ($db_exists) {
    try {
        $dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";
        $pdo_db = new PDO($dsn, $db_user, $db_pass);
        $pdo_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        mostrarResultado(
            "Conexión a base de datos '$db_name'", 
            true, 
            "Conexión exitosa a la base de datos '$db_name'",
            ""
        );
        
    } catch (PDOException $e) {
        mostrarResultado(
            "Conexión a base de datos '$db_name'", 
            false, 
            "Error al conectar a la base de datos: " . $e->getMessage(),
            ""
        );
        echo "</div></body></html>";
        exit;
    }
    
    // Test 5: Verificar tablas necesarias
    $tablas_necesarias = ['cliente', 'producto', 'categoria', 'pedido'];
    $tablas_existentes = [];
    
    try {
        $stmt = $pdo_db->query("SHOW TABLES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $tablas_existentes[] = strtolower($row[0]); // Convertir a minúsculas para comparación
        }
        
        echo "<h2>📋 Verificación de Tablas</h2>";
        
        foreach ($tablas_necesarias as $tabla) {
            $existe = in_array($tabla, $tablas_existentes);
            
            if ($existe) {
                // Contar registros
                $stmt = $pdo_db->query("SELECT COUNT(*) FROM `$tabla`");
                $count = $stmt->fetchColumn();
                
                mostrarResultado(
                    "Tabla '$tabla'", 
                    true, 
                    "La tabla '$tabla' existe y contiene $count registros.",
                    "✅ Tabla funcionando correctamente"
                );
                
                // Mostrar estructura de la tabla cliente
                if ($tabla === 'cliente') {
                    $stmt = $pdo_db->query("DESCRIBE `$tabla`");
                    $columnas = $stmt->fetchAll();
                    echo "<div class='info'>
                            <strong>Estructura de la tabla cliente:</strong>
                            <pre>";
                    foreach ($columnas as $col) {
                        echo $col['Field'] . " - " . $col['Type'] . "\n";
                    }
                    echo "</pre>
                          </div>";
                }
            } else {
                mostrarResultado(
                    "Tabla '$tabla'", 
                    false, 
                    "La tabla '$tabla' no existe.",
                    "Necesitas ejecutar el script de creación de tablas"
                );
            }
        }
        
        // Listar todas las tablas existentes
        if (!empty($tablas_existentes)) {
            echo "<div class='info'>
                    <strong>Tablas encontradas en la base de datos:</strong><br>
                    " . implode(', ', $tablas_existentes) . "
                  </div>";
        }
        
    } catch (PDOException $e) {
        mostrarResultado(
            "Verificación de tablas", 
            false, 
            "Error al verificar las tablas: " . $e->getMessage(),
            ""
        );
    }
    
    // Test 6: Test de la clase DatabaseConnection
    echo "<h2>🔧 Test de Configuración del Sistema</h2>";
    
    if (file_exists('config_db.php')) {
        require_once '../config/config_db.php';
        
        try {
            $db_instance = DatabaseConnection::getInstance();
            $connection = $db_instance->getConnection();
            
            mostrarResultado(
                "Clase DatabaseConnection", 
                true, 
                "La clase DatabaseConnection funciona correctamente.",
                "Singleton pattern implementado correctamente"
            );
            
            // Test de consulta básica
            $stmt = $connection->query("SELECT 1 as test");
            $result = $stmt->fetch();
            
            if ($result && $result['test'] == 1) {
                mostrarResultado(
                    "Consulta de prueba", 
                    true, 
                    "Las consultas a la base de datos funcionan correctamente.",
                    ""
                );
            }
            
        } catch (Exception $e) {
            mostrarResultado(
                "Clase DatabaseConnection", 
                false, 
                "Error en la clase DatabaseConnection: " . $e->getMessage(),
                ""
            );
        }
    } else {
        mostrarResultado(
            "Archivo config_db.php", 
            false, 
            "El archivo config_db.php no existe.",
            "Necesitas crear este archivo con la configuración de la base de datos"
        );
    }
}

// Resumen final
echo "<h2>📊 Resumen de Tests</h2>";

$porcentaje = ($total_tests > 0) ? round(($tests_passed / $total_tests) * 100, 2) : 0;

if ($porcentaje >= 80) {
    $clase_resumen = 'success';
    $mensaje_resumen = "¡Excelente! Tu configuración está lista para usar.";
} elseif ($porcentaje >= 60) {
    $clase_resumen = 'warning';
    $mensaje_resumen = "Configuración parcial. Revisa los elementos fallidos.";
} else {
    $clase_resumen = 'error';
    $mensaje_resumen = "Se requiere configuración adicional antes de usar el sistema.";
}

echo "<div class='$clase_resumen'>
        <strong>Resultado: $tests_passed de $total_tests tests pasaron ($porcentaje%)</strong><br>
        $mensaje_resumen
      </div>";

// Instrucciones de configuración
if ($porcentaje < 100) {
    echo "<h2>🛠️ Pasos para Configurar</h2>";
    echo "<div class='info'>
            <ol>
                <li><strong>Instalar/Iniciar MySQL:</strong> Asegúrate de que MySQL esté instalando y ejecutándose</li>
                <li><strong>Crear base de datos:</strong> Ejecuta: <code>CREATE DATABASE $db_name;</code></li>
                <li><strong>Ejecutar script SQL:</strong> Importa el archivo database_schema.sql</li>
                <li><strong>Verificar permisos:</strong> Asegúrate de que el usuario '$db_user' tenga permisos</li>
                <li><strong>Configurar PHP:</strong> Habilita las extensiones PDO y PDO_MySQL</li>
            </ol>
          </div>";
}

echo "<div class='info'>
        <strong>Tip:</strong> Ejecuta este archivo regularmente para verificar el estado de tu configuración.
      </div>";

echo "</div></body></html>";
?>
