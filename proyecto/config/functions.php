<?php
/**
 * Funciones auxiliares para el sistema
 * functions.php
 */

/**
 * Función para logging de actividades
 */
function logearActividad($mensaje, $nivel = 'INFO') {
    $fecha = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'N/A';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'N/A';
    
    $log_entry = "[$fecha] [$nivel] [$ip] $mensaje - User Agent: $user_agent" . PHP_EOL;
    
    // Crear directorio de logs si no existe
    $log_dir = __DIR__ . '/logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $log_file = $log_dir . '/sistema_' . date('Y-m-d') . '.log';
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

/**
 * Función para limpiar datos de entrada
 */
function limpiarDatos($dato) {
    return htmlspecialchars(strip_tags(trim($dato)));
}

/**
 * Función para validar email
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Función para validar contraseña
 */
function validarPassword($password) {
    // Mínimo 8 caracteres, al menos una letra y un número
    return strlen($password) >= 8 && preg_match('/[A-Za-z]/', $password) && preg_match('/[0-9]/', $password);
}

/**
 * Función para generar hash de contraseña
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Función para verificar contraseña
 */
function verificarPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Función para generar token aleatorio
 */
function generarToken($longitud = 32) {
    return bin2hex(random_bytes($longitud));
}

/**
 * Función para validar token CSRF
 */
function generarTokenCSRF() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = generarToken(16);
    }
    return $_SESSION['csrf_token'];
}

/**
 * Función para verificar token CSRF
 */
function verificarTokenCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Función para redireccionar con mensaje
 */
function redirigirConMensaje($url, $mensaje, $tipo = 'info') {
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['mensaje_tipo'] = $tipo;
    header("Location: $url");
    exit;
}

/**
 * Función para mostrar mensajes de sesión
 */
function mostrarMensaje() {
    if (isset($_SESSION['mensaje'])) {
        $mensaje = $_SESSION['mensaje'];
        $tipo = $_SESSION['mensaje_tipo'] ?? 'info';
        
        $clases = [
            'error' => 'alert-danger',
            'success' => 'alert-success',
            'warning' => 'alert-warning',
            'info' => 'alert-info'
        ];
        
        $clase = $clases[$tipo] ?? 'alert-info';
        
        echo "<div class='alert $clase alert-dismissible fade show' role='alert'>
                $mensaje
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
              </div>";
        
        unset($_SESSION['mensaje'], $_SESSION['mensaje_tipo']);
    }
}

/**
 * Función para proteger contra ataques de fuerza bruta
 */
function verificarIntentosFallidos($email) {
    $max_intentos = 5;
    $tiempo_bloqueo = 900; // 15 minutos
    
    if (!isset($_SESSION['intentos_fallidos'])) {
        $_SESSION['intentos_fallidos'] = [];
    }
    
    $ahora = time();
    $intentos = $_SESSION['intentos_fallidos'];
    
    // Limpiar intentos antiguos
    foreach ($intentos as $email_intento => $datos) {
        if ($ahora - $datos['ultimo_intento'] > $tiempo_bloqueo) {
            unset($_SESSION['intentos_fallidos'][$email_intento]);
        }
    }
    
    // Verificar si está bloqueado
    if (isset($intentos[$email])) {
        $datos_usuario = $intentos[$email];
        if ($datos_usuario['cantidad'] >= $max_intentos && 
            ($ahora - $datos_usuario['ultimo_intento']) < $tiempo_bloqueo) {
            $tiempo_restante = $tiempo_bloqueo - ($ahora - $datos_usuario['ultimo_intento']);
            return [
                'bloqueado' => true,
                'tiempo_restante' => $tiempo_restante,
                'mensaje' => "Demasiados intentos fallidos. Inténtalo en " . ceil($tiempo_restante / 60) . " minutos."
            ];
        }
    }
    
    return ['bloqueado' => false];
}

/**
 * Función para registrar intento fallido
 */
function registrarIntentoFallido($email) {
    if (!isset($_SESSION['intentos_fallidos'])) {
        $_SESSION['intentos_fallidos'] = [];
    }
    
    if (!isset($_SESSION['intentos_fallidos'][$email])) {
        $_SESSION['intentos_fallidos'][$email] = [
            'cantidad' => 0,
            'ultimo_intento' => 0
        ];
    }
    
    $_SESSION['intentos_fallidos'][$email]['cantidad']++;
    $_SESSION['intentos_fallidos'][$email]['ultimo_intento'] = time();
}

/**
 * Función para limpiar intentos fallidos (después de login exitoso)
 */
function limpiarIntentosFallidos($email) {
    if (isset($_SESSION['intentos_fallidos'][$email])) {
        unset($_SESSION['intentos_fallidos'][$email]);
    }
}

/**
 * Función para validar conexión a base de datos
 */
function validarConexionDB() {
    try {
        $db = DatabaseConnection::getInstance();
        $connection = $db->getConnection();
        
        // Test simple de conexión
        $stmt = $connection->query("SELECT 1");
        return $stmt !== false;
    } catch (Exception $e) {
        logearActividad("Error de conexión a BD: " . $e->getMessage(), 'ERROR');
        return false;
    }
}

/**
 * Función para obtener configuración del sistema
 */
function obtenerConfiguracion() {
    return [
        'nombre_sitio' => 'Placeres Ocultos',
        'version' => '1.0.0',
        'email_soporte' => 'soporte@placeresocultos.com',
        'timezone' => 'America/Bogota',
        'sesion_duracion' => 7200, // 2 horas
        'recordar_duracion' => 2592000, // 30 días
    ];
}

/**
 * Función para formatear fecha
 */
function formatearFecha($fecha, $formato = 'd/m/Y H:i') {
    if ($fecha instanceof DateTime) {
        return $fecha->format($formato);
    }
    
    try {
        $dt = new DateTime($fecha);
        return $dt->format($formato);
    } catch (Exception $e) {
        return $fecha;
    }
}

/**
 * Función para verificar si está en modo desarrollo
 */
function esDesarrollo() {
    return defined('DESARROLLO') && DESARROLLO === true;
}

/**
 * Función para mostrar errores solo en desarrollo
 */
function mostrarErrorDesarrollo($error) {
    if (esDesarrollo()) {
        echo "<div class='alert alert-warning'>
                <strong>Error de Desarrollo:</strong> $error
              </div>";
    }
}

/**
 * Función para validar entrada de archivos subidos
 */
function validarArchivoSubido($archivo, $tipos_permitidos = ['jpg', 'jpeg', 'png', 'gif'], $tamaño_max = 5242880) {
    $errores = [];
    
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        $errores[] = 'Error al subir el archivo';
        return $errores;
    }
    
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $tipos_permitidos)) {
        $errores[] = 'Tipo de archivo no permitido. Tipos permitidos: ' . implode(', ', $tipos_permitidos);
    }
    
    if ($archivo['size'] > $tamaño_max) {
        $errores[] = 'El archivo es demasiado grande. Tamaño máximo: ' . ($tamaño_max / 1024 / 1024) . 'MB';
    }
    
    return $errores;
}

/**
 * Función para obtener IP real del usuario
 */
function obtenerIPReal() {
    $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

/**
 * Función para enviar email (configurar con tu proveedor de email)
 */
function enviarEmail($destinatario, $asunto, $mensaje, $es_html = true) {
    // Esta función debería implementarse con tu proveedor de email preferido
    // Por ejemplo: PHPMailer, SendGrid, etc.
    
    logearActividad("Email enviado a $destinatario con asunto: $asunto");
    
    // Por ahora solo registramos en el log
    return true;
}

/**
 * Función para verificar si un email ya existe en la base de datos
 */
function emailExiste($email) {
    try {
        $db = DatabaseConnection::getInstance();
        $pdo = $db->getConnection();
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cliente WHERE email = ?");
        $stmt->execute([$email]);
        
        return $stmt->fetchColumn() > 0;
    } catch (Exception $e) {
        logearActividad("Error verificando email existente: " . $e->getMessage(), 'ERROR');
        return false;
    }
}

/**
 * Función para verificar si un nombre de usuario ya existe en la base de datos
 */
function nombreUsuarioExiste($nombre_usuario) {
    try {
        $db = DatabaseConnection::getInstance();
        $pdo = $db->getConnection();
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cliente WHERE nombre_usuario = ?");
        $stmt->execute([$nombre_usuario]);
        
        return $stmt->fetchColumn() > 0;
    } catch (Exception $e) {
        logearActividad("Error verificando nombre de usuario existente: " . $e->getMessage(), 'ERROR');
        return false;
    }
}
?>
