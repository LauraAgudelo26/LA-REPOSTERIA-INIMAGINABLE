<?php
/**
 * Script para crear usuario de prueba
 * crear_usuario_prueba.php
 */

require_once '../config/config_db.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Crear Usuario de Prueba</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background-color: #f5f5f5; 
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        .success { 
            color: #4CAF50; 
            background: #e8f5e8; 
            padding: 15px; 
            border-left: 4px solid #4CAF50; 
            margin: 10px 0; 
            border-radius: 5px;
        }
        .error { 
            color: #f44336; 
            background: #ffeaea; 
            padding: 15px; 
            border-left: 4px solid #f44336; 
            margin: 10px 0; 
            border-radius: 5px;
        }
        .info { 
            color: #2196F3; 
            background: #e3f2fd; 
            padding: 15px; 
            border-left: 4px solid #2196F3; 
            margin: 10px 0; 
            border-radius: 5px;
        }
        h1 { color: #333; text-align: center; }
        .user-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border: 1px solid #dee2e6;
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin: 10px 5px;
            border: none;
            cursor: pointer;
        }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #1e7e34; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Crear Usuario de Prueba</h1>";

try {
    $db = DatabaseConnection::getInstance();
    
    // Datos del usuario de prueba
    $usuarios_prueba = [
        [
            'nombre' => 'Admin',
            'apellido' => 'Sistema',
            'email' => 'admin@placeresocultos.com',
            'password' => 'admin123',
            'direccion' => 'Dirección de Administrador',
            'telefono' => '123-456-7890'
        ],
        [
            'nombre' => 'Usuario',
            'apellido' => 'Prueba',
            'email' => 'usuario@prueba.com',
            'password' => 'prueba123',
            'direccion' => 'Dirección de Prueba',
            'telefono' => '098-765-4321'
        ]
    ];
    
    echo "<div class='info'>
            <strong>Creando usuarios de prueba...</strong><br>
            Este script creará usuarios de ejemplo para probar el sistema de login.
          </div>";
    
    foreach ($usuarios_prueba as $usuario_data) {
        // Verificar si el usuario ya existe
        $stmt = $db->getConnection()->prepare("SELECT COUNT(*) FROM cliente WHERE Correo_Electronico = ?");
        $stmt->execute([$usuario_data['email']]);
        
        if ($stmt->fetchColumn() > 0) {
            echo "<div class='info'>
                    <strong>👤 Usuario ya existe:</strong><br>
                    Email: {$usuario_data['email']}<br>
                    Estado: Ya registrado en el sistema
                  </div>";
            continue;
        }
        
        // Crear hash de la contraseña
        $password_hash = password_hash($usuario_data['password'], PASSWORD_DEFAULT);
        
        // Insertar usuario
        $resultado = $db->insertarCliente(
            $usuario_data['nombre'],
            $usuario_data['apellido'],
            $usuario_data['direccion'],
            $usuario_data['telefono'],
            $usuario_data['email'],
            $password_hash
        );
        
        if ($resultado['success']) {
            echo "<div class='success'>
                    <strong>✅ Usuario creado exitosamente:</strong><br>
                    <div class='user-card'>
                        <strong>Nombre:</strong> {$usuario_data['nombre']} {$usuario_data['apellido']}<br>
                        <strong>Email:</strong> {$usuario_data['email']}<br>
                        <strong>Contraseña:</strong> {$usuario_data['password']}<br>
                        <strong>ID:</strong> {$resultado['cliente_id']}
                    </div>
                  </div>";
        } else {
            echo "<div class='error'>
                    <strong>❌ Error al crear usuario:</strong><br>
                    Email: {$usuario_data['email']}<br>
                    Error: {$resultado['message']}
                  </div>";
        }
    }
    
    // Mostrar resumen de la base de datos
    echo "<h2>📊 Resumen de la Base de Datos</h2>";
    
    $tablas_info = [
        'cliente' => 'Clientes registrados',
        'producto' => 'Productos disponibles',
        'categoria' => 'Categorías de productos',
        'pedido' => 'Pedidos realizados'
    ];
    
    foreach ($tablas_info as $tabla => $descripcion) {
        try {
            $stmt = $db->getConnection()->query("SELECT COUNT(*) FROM `$tabla`");
            $count = $stmt->fetchColumn();
            
            echo "<div class='info'>
                    <strong>📋 $descripcion:</strong> $count registros en la tabla '$tabla'
                  </div>";
        } catch (Exception $e) {
            echo "<div class='error'>
                    <strong>❌ Error al consultar $tabla:</strong> {$e->getMessage()}
                  </div>";
        }
    }
    
    echo "<h2>🚀 Probar el Sistema</h2>";
    echo "<div class='info'>
            <strong>Usuarios de prueba creados. Ahora puedes:</strong><br><br>
            <a href='iniciar_sesion.html' class='btn btn-success'>🔐 Probar Login</a>
            <a href='test_db_connection.php' class='btn'>🔧 Test de Conexión</a>
            <a href='index.html' class='btn'>🏠 Página Principal</a>
          </div>";
    
    echo "<div class='user-card'>
            <strong>💡 Credenciales de prueba:</strong><br><br>
            <strong>Administrador:</strong><br>
            Email: admin@placeresocultos.com<br>
            Contraseña: admin123<br><br>
            <strong>Usuario normal:</strong><br>
            Email: usuario@prueba.com<br>
            Contraseña: prueba123
          </div>";
    
} catch (Exception $e) {
    echo "<div class='error'>
            <strong>❌ Error del sistema:</strong><br>
            {$e->getMessage()}
          </div>";
    
    echo "<div class='info'>
            <strong>🔧 Posibles soluciones:</strong><br>
            1. Verifica que la base de datos esté funcionando<br>
            2. Ejecuta el test de conexión<br>
            3. Revisa la configuración en config_db.php
          </div>";
}

echo "</div></body></html>";
?>
