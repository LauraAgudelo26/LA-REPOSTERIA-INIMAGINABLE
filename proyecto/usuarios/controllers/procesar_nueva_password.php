<?php
require_once '../../config/config_db.php';

function logToConsole($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    file_put_contents('correos_log.txt', $logMessage, FILE_APPEND | LOCK_EX);
    logearActividad($message);
}

// Verificar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logToConsole("❌ Acceso inválido a procesar_nueva_password.php");
    header('Location: recuperar_contra.html');
    exit;
}

$token = $_POST['token'] ?? '';
$nueva_password = $_POST['nueva_password'] ?? '';
$confirmar_password = $_POST['confirmar_password'] ?? '';

// Validaciones básicas
if (empty($token) || empty($nueva_password) || empty($confirmar_password)) {
    logToConsole("❌ Datos incompletos en el formulario de nueva contraseña");
    header('Location: restablecer_contraseña.php?token=' . urlencode($token) . '&error=incomplete');
    exit;
}

// Verificar que las contraseñas coincidan
if ($nueva_password !== $confirmar_password) {
    logToConsole("❌ Las contraseñas no coinciden para el token: $token");
    header('Location: restablecer_contraseña.php?token=' . urlencode($token) . '&error=passwords_mismatch');
    exit;
}

// Validar requisitos de contraseña
$hasUpperCase = preg_match('/[A-Z]/', $nueva_password);
$hasLowerCase = preg_match('/[a-z]/', $nueva_password);
$hasNumbers = preg_match('/\d/', $nueva_password);
$isLongEnough = strlen($nueva_password) >= 8;

if (!$hasUpperCase || !$hasLowerCase || !$hasNumbers || !$isLongEnough) {
    logToConsole("❌ Contraseña débil proporcionada para el token: $token");
    header('Location: restablecer_contraseña.php?token=' . urlencode($token) . '&error=weak_password');
    exit;
}

try {
    $db = DatabaseConnection::getInstance();
    
    // Verificar que el token sea válido antes de actualizar
    $resultado = $db->verificarToken($token);
    
    if (!$resultado['valido']) {
        logToConsole("❌ Token inválido al intentar actualizar contraseña: $token");
        header('Location: restablecer_contraseña.php?token=' . urlencode($token) . '&error=invalid_token');
        exit;
    }
    
    // Actualizar la contraseña
    $actualizado = $db->actualizarPassword($token, $nueva_password);
    
    if ($actualizado) {
        logToConsole("✅ Contraseña actualizada exitosamente para el token: $token");
        logToConsole("🔑 Token limpiado de la base de datos");
        header('Location: restablecer_contraseña.php?success=1');
    } else {
        logToConsole("❌ Error al actualizar la contraseña para el token: $token");
        header('Location: restablecer_contraseña.php?token=' . urlencode($token) . '&error=update_failed');
    }
    
} catch (Exception $e) {
    logToConsole("❌ Excepción al procesar nueva contraseña: " . $e->getMessage());
    header('Location: restablecer_contraseña.php?token=' . urlencode($token) . '&error=system_error');
}

exit;
?>
