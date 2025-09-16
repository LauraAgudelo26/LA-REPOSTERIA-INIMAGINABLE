<?php
/**
 * Procesador de Login Mejorado
 * Maneja la autenticación de usuarios con validaciones robustas
 * Archivo: procesar_login.php
 */

session_start();

// Incluir archivos necesarios
require_once __DIR__ . '/../../config/config_db.php';

// Verificar si la conexión a la base de datos está funcionando
$conexion_test = verificarConexionRapida();
if (!$conexion_test['success']) {
    if (DESARROLLO) {
        die("
        <div style='background:#ffebee;color:#c62828;padding:20px;margin:20px;border-left:4px solid #c62828;font-family:Arial,sans-serif;'>
            <h3>🚨 Error de Conexión a Base de Datos</h3>
            <p><strong>Mensaje:</strong> " . htmlspecialchars($conexion_test['message']) . "</p>
            <hr>
            <p><strong>Solución:</strong> Ejecuta <a href='test_db_connection.php' target='_blank'>test_db_connection.php</a> para diagnóstico completo.</p>
        </div>
        ");
    } else {
        logearActividad("Error crítico de BD en login: " . $conexion_test['message'], 'CRITICAL');
        redirigirConMensaje('iniciar_sesion.html', 'Error del sistema. Contacta al administrador.', 'error');
    }
}

// Generar token CSRF si no existe
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = generarTokenCSRF();
}

// API para verificar estado de sesión (AJAX)
if (isset($_GET['api']) && $_GET['api'] === 'sesion') {
    header('Content-Type: application/json');
    echo json_encode([
        'logueado' => usuarioLogueado(),
        'usuario' => obtenerUsuarioActual(),
        'tiempo_restante' => isset($_SESSION['tiempo_login']) ? 
            (7200 - (time() - $_SESSION['tiempo_login'])) : 0
    ]);
    exit;
}

// Manejar logout
if (isset($_GET['logout'])) {
    manejarLogout();
}

// Verificar login automático con cookie
verificarLoginAutomatico();

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    procesarLogin();
}

// Si llegamos aquí sin POST, redireccionar al login
header('Location: iniciar_sesion.html');
exit;

/**
 * Función principal para procesar el login
 */
function procesarLogin() {
    // Verificar token CSRF
    $token_csrf = $_POST['csrf_token'] ?? '';
    if (!verificarTokenCSRF($token_csrf)) {
        logearActividad("Intento de login con token CSRF inválido desde IP: " . obtenerIPReal(), 'WARNING');
        redirigirConError(['Token de seguridad inválido. Recarga la página e inténtalo nuevamente.']);
    }
    
    // Obtener y limpiar datos
    $email = limpiarDatos($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $recordar = isset($_POST['recordar']);
    
    $errores = [];
    
    // Validaciones básicas
    if (empty($email)) {
        $errores[] = "El email es obligatorio";
    } elseif (!validarEmail($email)) {
        $errores[] = "El formato del email no es válido";
    }
    
    if (empty($password)) {
        $errores[] = "La contraseña es obligatoria";
    } elseif (strlen($password) < 4) {
        $errores[] = "La contraseña debe tener al menos 4 caracteres";
    }
    
    // Verificar intentos fallidos (protección contra fuerza bruta)
    $verificacion_intentos = verificarIntentosFallidos($email);
    if ($verificacion_intentos['bloqueado']) {
        logearActividad("Usuario bloqueado por intentos fallidos: $email", 'WARNING');
        redirigirConError([$verificacion_intentos['mensaje']]);
    }
    
    // Si hay errores básicos, salir
    if (!empty($errores)) {
        redirigirConError($errores, $email);
    }
    
    // Intentar autenticación
    try {
        $db = DatabaseConnection::getInstance();
        
        if (!$db->isConnected()) {
            throw new Exception('No hay conexión a la base de datos');
        }
        
        $usuario = $db->verificarLogin($email, $password);
        
        if ($usuario) {
            // Login exitoso
            iniciarSesionUsuario($usuario, $email, $recordar);
            
        } else {
            // Login fallido
            registrarIntentoFallido($email);
            logearActividad("Intento de login fallido para: $email desde IP: " . obtenerIPReal(), 'WARNING');
            
            $errores[] = "Email o contraseña incorrectos";
            redirigirConError($errores, $email);
        }
        
    } catch (Exception $e) {
        logearActividad("Error en procesamiento de login: " . $e->getMessage(), 'ERROR');
        $errores[] = "Error del sistema. Por favor inténtalo más tarde.";
        redirigirConError($errores, $email);
    }
}

/**
 * Función para iniciar sesión del usuario
 */
function iniciarSesionUsuario($usuario, $email, $recordar = false) {
    // Limpiar intentos fallidos
    limpiarIntentosFallidos($email);
    
    // Establecer variables de sesión
    $_SESSION['usuario_id'] = $usuario['idCliente'];
    $_SESSION['usuario_nombre'] = $usuario['Nombre'];
    $_SESSION['usuario_apellido'] = $usuario['Apellido'];
    $_SESSION['usuario_email'] = $email;
    $_SESSION['usuario_logueado'] = true;
    $_SESSION['tiempo_login'] = time();
    $_SESSION['ip_login'] = obtenerIPReal();
    
    // Regenerar ID de sesión para seguridad
    session_regenerate_id(true);
    
    // Manejar "recordar usuario"
    if ($recordar) {
        manejarRecordarUsuario($usuario['idCliente']);
    }
    
    logearActividad("Login exitoso para usuario: $email (ID: {$usuario['idCliente']}) desde IP: " . obtenerIPReal());
    
    // Redireccionar al dashboard
    $_SESSION['mensaje_exito'] = "¡Bienvenido {$usuario['Nombre']}! Has iniciado sesión correctamente.";
    header('Location: index-dashboar.html');
    exit;
}

/**
 * Función para manejar "recordar usuario"
 */
function manejarRecordarUsuario($cliente_id) {
    try {
        $db = DatabaseConnection::getInstance();
        $token_recordar = generarToken(32);
        
        // Guardar token en la base de datos
        if ($db->actualizarTokenRecordar($cliente_id, $token_recordar)) {
            // Crear cookie segura
            $opciones_cookie = [
                'expires' => time() + (86400 * 30), // 30 días
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']), // Solo HTTPS si está disponible
                'httponly' => true, // Solo accesible via HTTP (no JavaScript)
                'samesite' => 'Lax'
            ];
            
            setcookie('recordar_token', $token_recordar, $opciones_cookie);
            logearActividad("Token de recordar configurado para cliente ID: $cliente_id");
        }
    } catch (Exception $e) {
        logearActividad("Error al configurar token de recordar: " . $e->getMessage(), 'ERROR');
    }
}

/**
 * Función para verificar login automático con cookie
 */
function verificarLoginAutomatico() {
    // Si ya está logueado, no hacer nada
    if (usuarioLogueado()) {
        return;
    }
    
    // Verificar si existe cookie de recordar
    if (!isset($_COOKIE['recordar_token'])) {
        return;
    }
    
    try {
        $db = DatabaseConnection::getInstance();
        $token = $_COOKIE['recordar_token'];
        
        $usuario = $db->obtenerClientePorToken($token);
        
        if ($usuario) {
            // Login automático exitoso
            $_SESSION['usuario_id'] = $usuario['idCliente'];
            $_SESSION['usuario_nombre'] = $usuario['Nombre'];
            $_SESSION['usuario_apellido'] = $usuario['Apellido'];
            $_SESSION['usuario_email'] = $usuario['Correo_Electronico'];
            $_SESSION['usuario_logueado'] = true;
            $_SESSION['tiempo_login'] = time();
            $_SESSION['ip_login'] = obtenerIPReal();
            $_SESSION['login_automatico'] = true;
            
            session_regenerate_id(true);
            
            logearActividad("Login automático exitoso para usuario: {$usuario['Correo_Electronico']} (ID: {$usuario['idCliente']})");
            
            // Redireccionar al dashboard
            header('Location: index-dashboar.html');
            exit;
        } else {
            // Token inválido, eliminar cookie
            setcookie('recordar_token', '', time() - 3600, '/');
            logearActividad("Token de recordar inválido eliminado", 'WARNING');
        }
    } catch (Exception $e) {
        logearActividad("Error en login automático: " . $e->getMessage(), 'ERROR');
        setcookie('recordar_token', '', time() - 3600, '/');
    }
}

/**
 * Función para manejar logout
 */
function manejarLogout() {
    if (isset($_SESSION['usuario_id'])) {
        try {
            $db = DatabaseConnection::getInstance();
            $db->limpiarTokenRecordar($_SESSION['usuario_id']);
        } catch (Exception $e) {
            logearActividad("Error al limpiar token en logout: " . $e->getMessage(), 'ERROR');
        }
        
        logearActividad("Usuario cerró sesión: {$_SESSION['usuario_email']} (ID: {$_SESSION['usuario_id']})");
    }
    
    // Eliminar cookie
    setcookie('recordar_token', '', time() - 3600, '/');
    
    // Limpiar sesión
    session_destroy();
    
    // Redireccionar con mensaje
    header('Location: iniciar_sesion.html?logout=1');
    exit;
}

/**
 * Función para redireccionar con errores
 */
function redirigirConError($errores, $email = '') {
    $_SESSION['errores_login'] = $errores;
    if (!empty($email)) {
        $_SESSION['email_anterior'] = $email;
    }
    header('Location: iniciar_sesion.html');
    exit;
}

/**
 * Función para verificar si el usuario está logueado
 */
function usuarioLogueado() {
    return isset($_SESSION['usuario_logueado']) && $_SESSION['usuario_logueado'] === true;
}

/**
 * Función para obtener datos del usuario logueado
 */
function obtenerUsuarioActual() {
    if (usuarioLogueado()) {
        return [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario_nombre'],
            'apellido' => $_SESSION['usuario_apellido'],
            'email' => $_SESSION['usuario_email'],
            'nombre_completo' => $_SESSION['usuario_nombre'] . ' ' . $_SESSION['usuario_apellido'],
            'tiempo_login' => $_SESSION['tiempo_login'] ?? 0,
            'ip_login' => $_SESSION['ip_login'] ?? 'unknown',
            'login_automatico' => $_SESSION['login_automatico'] ?? false
        ];
    }
    return null;
}

/**
 * Función para proteger páginas (usar en páginas que requieren login)
 */
function requerirLogin($redireccionar = 'iniciar_sesion.html') {
    if (!usuarioLogueado()) {
        header("Location: $redireccionar");
        exit;
    }
    
    // Verificar tiempo de sesión
    verificarTiempoSesion();
}

/**
 * Verificar tiempo de sesión (opcional - para auto-logout después de inactividad)
 */
function verificarTiempoSesion($tiempo_maximo = 7200) { // 2 horas por defecto
    if (isset($_SESSION['tiempo_login'])) {
        if (time() - $_SESSION['tiempo_login'] > $tiempo_maximo) {
            logearActividad("Sesión expirada para usuario: {$_SESSION['usuario_email']}");
            session_destroy();
            header('Location: iniciar_sesion.html?expired=1');
            exit;
        }
        // Actualizar tiempo de última actividad
        $_SESSION['tiempo_login'] = time();
    }
}

/**
 * Función para obtener información de sesión (para uso en otras páginas)
 */
function obtenerInfoSesion() {
    return [
        'logueado' => usuarioLogueado(),
        'usuario' => obtenerUsuarioActual(),
        'tiempo_restante' => isset($_SESSION['tiempo_login']) ? 
            (7200 - (time() - $_SESSION['tiempo_login'])) : 0,
        'csrf_token' => $_SESSION['csrf_token'] ?? ''
    ];
}

/**
 * Función para middleware de autenticación (para APIs)
 */
function verificarAutenticacionAPI() {
    header('Content-Type: application/json');
    
    if (!usuarioLogueado()) {
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado', 'codigo' => 401]);
        exit;
    }
    
    verificarTiempoSesion();
    return obtenerUsuarioActual();
}
?>
