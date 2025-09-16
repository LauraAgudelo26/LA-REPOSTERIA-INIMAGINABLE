<?php
/**
 * Archivo para obtener datos de sesión para el frontend
 * obtener_datos_sesion.php
 */

session_start();
header('Content-Type: application/json');

// Función para limpiar y obtener datos de sesión
function obtenerDatosSesion() {
    $datos = [
        'usuario_logueado' => isset($_SESSION['cliente_id']) && !empty($_SESSION['cliente_id']),
        'errores' => [],
        'mensaje_exito' => null,
        'email_anterior' => '',
        'csrf_token' => ''
    ];
    
    // Obtener errores de login si existen
    if (isset($_SESSION['errores_login']) && !empty($_SESSION['errores_login'])) {
        $datos['errores'] = $_SESSION['errores_login'];
        unset($_SESSION['errores_login']); // Limpiar después de obtener
    }
    
    // Obtener mensaje de éxito si existe
    if (isset($_SESSION['mensaje_exito'])) {
        $datos['mensaje_exito'] = $_SESSION['mensaje_exito'];
        unset($_SESSION['mensaje_exito']); // Limpiar después de obtener
    }
    
    // Obtener email anterior si existe
    if (isset($_SESSION['email_anterior'])) {
        $datos['email_anterior'] = $_SESSION['email_anterior'];
        unset($_SESSION['email_anterior']); // Limpiar después de obtener
    }
    
    // Generar token CSRF si no existe
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $datos['csrf_token'] = $_SESSION['csrf_token'];
    
    return $datos;
}

// Manejar parámetros de URL para mensajes especiales
function procesarParametrosURL() {
    $mensajes = [];
    
    if (isset($_GET['expired']) && $_GET['expired'] === '1') {
        $mensajes[] = 'Tu sesión ha expirado. Por favor inicia sesión nuevamente.';
    }
    
    if (isset($_GET['logout']) && $_GET['logout'] === '1') {
        $_SESSION['mensaje_exito'] = 'Has cerrado sesión exitosamente.';
    }
    
    if (isset($_GET['registro']) && $_GET['registro'] === '1') {
        $_SESSION['mensaje_exito'] = 'Registro completado. Ya puedes iniciar sesión.';
    }
    
    if (isset($_GET['recuperacion']) && $_GET['recuperacion'] === '1') {
        $_SESSION['mensaje_exito'] = 'Contraseña actualizada. Inicia sesión con tu nueva contraseña.';
    }
    
    if (!empty($mensajes)) {
        $_SESSION['errores_login'] = $mensajes;
    }
}

try {
    // Procesar parámetros de URL
    procesarParametrosURL();
    
    // Obtener datos de sesión
    $datos = obtenerDatosSesion();
    
    // Agregar información adicional útil
    $datos['timestamp'] = time();
    $datos['usuario_logueado'] = isset($_SESSION['usuario_logueado']) && $_SESSION['usuario_logueado'] === true;
    
    // Si el usuario ya está logueado, incluir sus datos
    if ($datos['usuario_logueado']) {
        $datos['usuario'] = [
            'id' => $_SESSION['usuario_id'] ?? null,
            'nombre' => $_SESSION['usuario_nombre'] ?? '',
            'apellido' => $_SESSION['usuario_apellido'] ?? '',
            'email' => $_SESSION['usuario_email'] ?? '',
            'nombre_completo' => ($_SESSION['usuario_nombre'] ?? '') . ' ' . ($_SESSION['usuario_apellido'] ?? '')
        ];
    }
    
    // Enviar respuesta JSON
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // En caso de error, enviar respuesta mínima
    echo json_encode([
        'errores' => ['Error del sistema. Por favor recarga la página.'],
        'mensaje_exito' => null,
        'email_anterior' => '',
        'csrf_token' => bin2hex(random_bytes(32)),
        'error' => true
    ], JSON_UNESCAPED_UNICODE);
}
?>
