<?php
/**
 * Configuración de la base de datos
 * Archivo: config_db.php
 * Versión mejorada con manejo robusto de errores
 */

// Habilitar el modo de desarrollo (cambiar a false en producción)
define('DESARROLLO', true);

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'placeres_ocultos');
define('DB_USER', 'root'); // Cambiar por tu usuario de MySQL
define('DB_PASS', ''); // Cambiar por tu contraseña de MySQL
define('DB_CHARSET', 'utf8mb4');

// Configurar manejo de errores
if (DESARROLLO) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Incluir funciones auxiliares
require_once __DIR__ . '/functions.php';

// Clase para manejar la conexión a la base de datos
class DatabaseConnection {
    private static $instance = null;
    private $connection;
    private $connected = false;
    
    private function __construct() {
        $this->conectar();
    }
    
    private function conectar() {
        try {
            // Verificar extensiones necesarias
            if (!extension_loaded('pdo') || !extension_loaded('pdo_mysql')) {
                throw new Exception('Las extensiones PDO y PDO_MySQL son requeridas');
            }
            
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $opciones = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET,
                PDO::ATTR_TIMEOUT => 10, // Timeout de conexión
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $opciones);
            $this->connected = true;
            
            logearActividad("Conexión a base de datos establecida exitosamente");
            
        } catch (PDOException $e) {
            $this->connected = false;
            $error_msg = "Error de conexión a la base de datos: " . $e->getMessage();
            
            logearActividad($error_msg, 'ERROR');
            
            if (DESARROLLO) {
                die("
                <div style='background:#ffebee;color:#c62828;padding:20px;margin:20px;border-left:4px solid #c62828;font-family:Arial,sans-serif;'>
                    <h3>🚨 Error de Conexión a Base de Datos</h3>
                    <p><strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
                    <p><strong>Código:</strong> " . $e->getCode() . "</p>
                    <hr>
                    <h4>🔧 Pasos para solucionar:</h4>
                    <ol>
                        <li>Verifica que MySQL esté ejecutándose</li>
                        <li>Confirma las credenciales en config_db.php</li>
                        <li>Asegúrate de que la base de datos '" . DB_NAME . "' exista</li>
                        <li>Ejecuta <a href='test_db_connection.php' target='_blank'>test_db_connection.php</a> para diagnóstico</li>
                    </ol>
                </div>
                ");
            } else {
                die("Error del sistema. Por favor contacta al administrador.");
            }
        } catch (Exception $e) {
            $this->connected = false;
            logearActividad("Error general: " . $e->getMessage(), 'ERROR');
            die("Error de sistema: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        if (!$this->connected) {
            $this->conectar();
        }
        return $this->connection;
    }
    
    public function isConnected() {
        return $this->connected;
    }
    
    // Función para probar la conexión
    public function testConnection() {
        try {
            if (!$this->connected) {
                return ['success' => false, 'message' => 'No hay conexión establecida'];
            }
            
            $stmt = $this->connection->query("SELECT 1 as test");
            $result = $stmt->fetch();
            
            if ($result && $result['test'] == 1) {
                return ['success' => true, 'message' => 'Conexión exitosa'];
            } else {
                return ['success' => false, 'message' => 'Error en consulta de prueba'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Función para insertar cliente con manejo de errores mejorado
    public function insertarCliente($nombre, $apellido, $direccion, $telefono, $email, $password_hash) {
        try {
            // Verificar si el email ya existe
            $stmt = $this->connection->prepare("SELECT COUNT(*) FROM cliente WHERE Correo_Electronico = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetchColumn() > 0) {
                return ['success' => false, 'message' => 'El email ya está registrado'];
            }
            
            // Insertar nuevo cliente
            $sql = "INSERT INTO cliente (Nombre, Apellido, Direccion, Telefono, Correo_Electronico, Password_Hash, Fecha_Registro) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $this->connection->prepare($sql);
            $result = $stmt->execute([$nombre, $apellido, $direccion, $telefono, $email, $password_hash]);
            
            if ($result) {
                $cliente_id = $this->connection->lastInsertId();
                logearActividad("Cliente registrado exitosamente: $email (ID: $cliente_id)");
                return ['success' => true, 'message' => 'Cliente registrado exitosamente', 'cliente_id' => $cliente_id];
            } else {
                return ['success' => false, 'message' => 'Error al registrar cliente'];
            }
            
        } catch (PDOException $e) {
            logearActividad("Error al insertar cliente: " . $e->getMessage(), 'ERROR');
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }
    
    // Función para verificar login con manejo de errores mejorado
    public function verificarLogin($email, $password) {
        try {
            if (!$this->connected) {
                throw new Exception('No hay conexión a la base de datos');
            }
            
            $sql = "SELECT id, nombre, apellido, email, password, rol, activo, ultimo_acceso 
                    FROM cliente 
                    WHERE email = ? AND activo = 1";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();
            
            if ($usuario && password_verify($password, $usuario['password'])) {
                // Actualizar último login
                $sql_update = "UPDATE cliente SET ultimo_acceso = NOW() WHERE id = ?";
                $stmt_update = $this->connection->prepare($sql_update);
                $stmt_update->execute([$usuario['id']]);
                
                // Formatear datos para compatibilidad con el resto del sistema
                $datosUsuario = [
                    'idCliente' => $usuario['id'],
                    'Nombre' => $usuario['nombre'],
                    'Apellido' => $usuario['apellido'],
                    'Correo_Electronico' => $usuario['email'],
                    'Rol' => $usuario['rol'],
                    'Estado' => $usuario['activo'],
                    'Fecha_Ultimo_Login' => $usuario['ultimo_acceso']
                ];
                
                logearActividad("Login exitoso para usuario: $email");
                return $datosUsuario;
            } else {
                logearActividad("Intento de login fallido para: $email", 'WARNING');
                return false;
            }
            
        } catch (PDOException $e) {
            logearActividad("Error en verificación de login: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
    
    // Función para obtener cliente por token de recordar
    public function obtenerClientePorToken($token) {
        try {
            $sql = "SELECT idCliente, Nombre, Apellido, Correo_Electronico, Estado 
                    FROM cliente 
                    WHERE Reset_Token = ? AND (Estado = 'Activo' OR Estado IS NULL)";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$token]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            logearActividad("Error al obtener cliente por token: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
    
    // Función para actualizar token de recordar
    public function actualizarTokenRecordar($cliente_id, $token) {
        try {
            $sql = "UPDATE cliente SET Reset_Token = ? WHERE idCliente = ?";
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute([$token, $cliente_id]);
        } catch (PDOException $e) {
            logearActividad("Error al actualizar token: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
    
    // Función para limpiar token de recordar
    public function limpiarTokenRecordar($cliente_id) {
        try {
            $sql = "UPDATE cliente SET Reset_Token = NULL WHERE idCliente = ?";
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute([$cliente_id]);
        } catch (PDOException $e) {
            logearActividad("Error al limpiar token: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
    
    // Función para obtener productos
    public function obtenerProductos($limite = null, $categoria_id = null) {
        try {
            $sql = "SELECT p.*, c.Nombre as Categoria_Nombre 
                    FROM producto p 
                    LEFT JOIN categoria c ON p.idCategoria = c.idCategoria 
                    WHERE p.Stock > 0";
            
            if ($categoria_id) {
                $sql .= " AND p.idCategoria = ?";
            }
            
            $sql .= " ORDER BY p.Nombre";
            
            if ($limite) {
                $sql .= " LIMIT ?";
            }
            
            $stmt = $this->connection->prepare($sql);
            
            $params = [];
            if ($categoria_id) $params[] = $categoria_id;
            if ($limite) $params[] = $limite;
            
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            logearActividad("Error al obtener productos: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }
    
    // Función para obtener categorías
    public function obtenerCategorias() {
        try {
            $sql = "SELECT * FROM categoria ORDER BY Nombre";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            logearActividad("Error al obtener categorías: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }
    
    // Función para obtener un producto por ID
    public function obtenerProductoPorId($producto_id) {
        try {
            $sql = "SELECT p.*, c.Nombre as Categoria_Nombre 
                    FROM producto p 
                    LEFT JOIN categoria c ON p.idCategoria = c.idCategoria 
                    WHERE p.idProducto = ?";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$producto_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            logearActividad("Error al obtener producto: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
}

// Función de utilidad para verificar conexión rápida
function verificarConexionRapida() {
    try {
        $db = DatabaseConnection::getInstance();
        return $db->testConnection();
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
