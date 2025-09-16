<?php
require_once 'enviar_correo_seguro.php';

function logToConsole($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    file_put_contents('correos_log.txt', $logMessage, FILE_APPEND | LOCK_EX);
    if (function_exists('logearActividad')) {
        logearActividad($message);
    }
}

// Verificar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logToConsole("❌ Acceso inválido a procesar_nueva_password_seguro.php");
    header('Location: restablecer_contraseña_seguro.php');
    exit;
}

$token = $_POST['token'] ?? '';
$nueva_password = $_POST['nueva_password'] ?? '';
$confirmar_password = $_POST['confirmar_password'] ?? '';

// Validaciones básicas
if (empty($token) || empty($nueva_password) || empty($confirmar_password)) {
    logToConsole("❌ Datos incompletos en el formulario de nueva contraseña");
    header('Location: restablecer_contraseña_seguro.php?token=' . urlencode($token) . '&error=incomplete');
    exit;
}

// Verificar que las contraseñas coincidan
if ($nueva_password !== $confirmar_password) {
    logToConsole("❌ Las contraseñas no coinciden para el token: $token");
    header('Location: restablecer_contraseña_seguro.php?token=' . urlencode($token) . '&error=passwords_mismatch');
    exit;
}

// Validar requisitos de contraseña
$hasUpperCase = preg_match('/[A-Z]/', $nueva_password);
$hasLowerCase = preg_match('/[a-z]/', $nueva_password);
$hasNumbers = preg_match('/\d/', $nueva_password);
$isLongEnough = strlen($nueva_password) >= 8;

if (!$hasUpperCase || !$hasLowerCase || !$hasNumbers || !$isLongEnough) {
    logToConsole("❌ Contraseña débil proporcionada para el token: $token");
    header('Location: restablecer_contraseña_seguro.php?token=' . urlencode($token) . '&error=weak_password');
    exit;
}

// Verificar token en archivo
$registro = verificarCodigoArchivo($token);

if (!$registro) {
    logToConsole("❌ Token inválido al intentar actualizar contraseña: $token");
    header('Location: restablecer_contraseña_seguro.php?error=invalid_token');
    exit;
}

$correo = $registro['correo'];
logToConsole("🔄 Procesando cambio de contraseña para: $correo");

$exito_bd = false;
$exito_archivo = false;

// Intentar actualizar en base de datos si es posible
try {
    if (function_exists('verificarCorreoEnBD')) {
        $existe_en_bd = verificarCorreoEnBD($correo);
        
        if ($existe_en_bd) {
            if (function_exists('DatabaseConnection')) {
                $db = DatabaseConnection::getInstance();
                $exito_bd = $db->actualizarPassword($token, $nueva_password);
                
                if ($exito_bd) {
                    logToConsole("✅ Contraseña actualizada en base de datos para: $correo");
                } else {
                    logToConsole("⚠️ No se pudo actualizar en BD, pero continuando...");
                }
            }
        } else {
            logToConsole("ℹ️ Correo no existe en BD, solo marcando código como usado");
        }
    }
} catch (Exception $e) {
    logToConsole("⚠️ Error con BD, continuando solo con archivo: " . $e->getMessage());
}

// Marcar código como usado en archivo (SIEMPRE)
$exito_archivo = marcarCodigoUsado($token);

if ($exito_archivo) {
    logToConsole("✅ Código marcado como usado en archivo");
    logToConsole("🔑 Proceso de cambio de contraseña completado para: $correo");
    
    // Almacenar información adicional del cambio
    $info_cambio = [
        'correo' => $correo,
        'token_usado' => $token,
        'fecha_cambio' => date('Y-m-d H:i:s'),
        'actualizado_bd' => $exito_bd,
        'ip_usuario' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];
    
    $archivo_cambios = 'cambios_password.json';
    $cambios_existentes = [];
    
    if (file_exists($archivo_cambios)) {
        $contenido = file_get_contents($archivo_cambios);
        $cambios_existentes = json_decode($contenido, true) ?: [];
    }
    
    $cambios_existentes[] = $info_cambio;
    file_put_contents($archivo_cambios, json_encode($cambios_existentes, JSON_PRETTY_PRINT));
    
    logToConsole("📝 Información del cambio guardada en: cambios_password.json");
    
    if ($exito_bd) {
        header('Location: restablecer_contraseña_seguro.php?success=1&tipo=completo');
    } else {
        header('Location: restablecer_contraseña_seguro.php?success=1&tipo=archivo');
    }
} else {
    logToConsole("❌ Error al marcar código como usado: $token");
    header('Location: restablecer_contraseña_seguro.php?token=' . urlencode($token) . '&error=update_failed');
}

exit;
?>
