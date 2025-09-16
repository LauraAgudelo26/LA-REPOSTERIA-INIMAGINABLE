<?php
// Incluir configuración de base de datos (opcional - solo para logging)
require_once '../../config/config_db.php';

// Función para registrar en consola/log
function logToConsole($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    
    // Escribir en archivo de log
    file_put_contents('correos_log.txt', $logMessage, FILE_APPEND | LOCK_EX);
    
    // También usar la función de logging del sistema si está disponible
    if (function_exists('logearActividad')) {
        logearActividad($message);
    }
    
    // Mostrar en pantalla también
    echo "<script>console.log('$message');</script>";
    
    // Si se ejecuta desde línea de comandos
    if (php_sapi_name() === 'cli') {
        echo $logMessage;
    }
}

// Función para generar y almacenar código de recuperación
function generarCodigoRecuperacion($correo) {
    $codigo = strtoupper(bin2hex(random_bytes(3))); // Código de 6 caracteres
    $token = bin2hex(random_bytes(16)); // Token largo para URL
    $enlace = "http://localhost/PAGINA/restablecer_contraseña.php?token=$token";
    $timestamp = date('Y-m-d H:i:s');
    $expiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
    
    // Crear registro completo para almacenar
    $registro = [
        'correo' => $correo,
        'codigo' => $codigo,
        'token' => $token,
        'enlace' => $enlace,
        'fecha_generacion' => $timestamp,
        'fecha_expiry' => $expiry,
        'usado' => false
    ];
    
    // Almacenar en archivo JSON
    $archivo_codigos = 'codigos_recuperacion.json';
    $codigos_existentes = [];
    
    if (file_exists($archivo_codigos)) {
        $contenido = file_get_contents($archivo_codigos);
        $codigos_existentes = json_decode($contenido, true) ?: [];
    }
    
    // Agregar nuevo código
    $codigos_existentes[] = $registro;
    
    // Limpiar códigos expirados (opcional)
    $codigos_existentes = array_filter($codigos_existentes, function($item) {
        return strtotime($item['fecha_expiry']) > time();
    });
    
    // Guardar archivo actualizado
    file_put_contents($archivo_codigos, json_encode($codigos_existentes, JSON_PRETTY_PRINT));
    
    return $registro;
}

// Función para verificar si el correo existe en BD (si está disponible)
function verificarCorreoEnBD($correo) {
    try {
        if (function_exists('emailExiste')) {
            return emailExiste($correo);
        }
        
        // Intentar conexión directa si la función no existe
        $db = DatabaseConnection::getInstance();
        $sql = "SELECT COUNT(*) FROM Cliente WHERE Correo_Electronico = ?";
        $stmt = $db->getConnection()->prepare($sql);
        $stmt->execute([$correo]);
        return $stmt->fetchColumn() > 0;
    } catch (Exception $e) {
        logToConsole("INFO: No se pudo verificar BD, continuando sin verificación: " . $e->getMessage());
        return null; // No se pudo verificar, pero continuamos
    }
}

// Función para guardar token en BD si es posible
function guardarTokenEnBD($correo, $token) {
    try {
        $db = DatabaseConnection::getInstance();
        return $db->generarTokenRecuperacion($correo, $token);
    } catch (Exception $e) {
        logToConsole("INFO: No se pudo guardar token en BD: " . $e->getMessage());
        return false;
    }
}

// VERSIÓN SEGURA - SIEMPRE GENERA CÓDIGO
function procesoRecuperacionSeguro($correo) {
    logToConsole("=== PROCESO DE RECUPERACIÓN INICIADO ===");
    logToConsole("📧 Correo solicitante: $correo");
    
    // SIEMPRE generar código, sin importar si existe o no
    $registro = generarCodigoRecuperacion($correo);
    
    // Verificar si el correo existe en BD (pero no revelar el resultado)
    $existe_en_bd = verificarCorreoEnBD($correo);
    
    if ($existe_en_bd === true) {
        logToConsole("✅ Correo encontrado en BD, guardando token");
        $guardado_bd = guardarTokenEnBD($correo, $registro['token']);
        if ($guardado_bd) {
            logToConsole("💾 Token guardado en base de datos");
        } else {
            logToConsole("⚠️ Token no se pudo guardar en BD, solo disponible en archivo");
        }
    } elseif ($existe_en_bd === false) {
        logToConsole("ℹ️ Correo NO encontrado en BD, pero código generado igual");
    } else {
        logToConsole("⚠️ No se pudo verificar BD, código generado solo en archivo");
    }
    
    logToConsole("🔑 Código generado: " . $registro['codigo']);
    logToConsole("🎫 Token generado: " . $registro['token']);
    logToConsole("🔗 Enlace de recuperación: " . $registro['enlace']);
    logToConsole("⏰ Válido hasta: " . $registro['fecha_expiry']);
    logToConsole("💾 Datos guardados en: codigos_recuperacion.json");
    logToConsole("=========================================");
    
    // Simular envío de correo (SIEMPRE se muestra como enviado)
    logToConsole("📨 SIMULANDO ENVÍO DE CORREO...");
    logToConsole("Para: $correo");
    logToConsole("Asunto: Código de Recuperación de Contraseña");
    logToConsole("Contenido del correo:");
    logToConsole("---");
    logToConsole("Hola,");
    logToConsole("Tu código de recuperación es: " . $registro['codigo']);
    logToConsole("O usa este enlace directo:");
    logToConsole($registro['enlace']);
    logToConsole("Este código expira el: " . $registro['fecha_expiry']);
    logToConsole("---");
    logToConsole("✅ Correo 'enviado' exitosamente (modo consola)");
    logToConsole("👀 NOTA: Código generado independientemente de si el correo existe");
    
    return $registro;
}

// Función para verificar código desde archivo
function verificarCodigoArchivo($codigo_o_token, $correo = null) {
    $archivo_codigos = 'codigos_recuperacion.json';
    
    if (!file_exists($archivo_codigos)) {
        return false;
    }
    
    $contenido = file_get_contents($archivo_codigos);
    $codigos = json_decode($contenido, true) ?: [];
    
    foreach ($codigos as $index => $registro) {
        // Verificar por código o token
        $coincide_codigo = (strtoupper($codigo_o_token) === $registro['codigo']);
        $coincide_token = ($codigo_o_token === $registro['token']);
        
        if (($coincide_codigo || $coincide_token) && !$registro['usado']) {
            // Verificar si no ha expirado
            if (strtotime($registro['fecha_expiry']) > time()) {
                // Verificar correo si se proporciona
                if ($correo === null || $registro['correo'] === $correo) {
                    return $registro;
                }
            }
        }
    }
    
    return false;
}

// Función para marcar código como usado
function marcarCodigoUsado($token) {
    $archivo_codigos = 'codigos_recuperacion.json';
    
    if (!file_exists($archivo_codigos)) {
        return false;
    }
    
    $contenido = file_get_contents($archivo_codigos);
    $codigos = json_decode($contenido, true) ?: [];
    
    foreach ($codigos as $index => $registro) {
        if ($registro['token'] === $token) {
            $codigos[$index]['usado'] = true;
            $codigos[$index]['fecha_uso'] = date('Y-m-d H:i:s');
            file_put_contents($archivo_codigos, json_encode($codigos, JSON_PRETTY_PRINT));
            logToConsole("✅ Código marcado como usado: $token");
            return true;
        }
    }
    
    return false;
}

// Procesar la solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = filter_var($_POST['correo'] ?? '', FILTER_VALIDATE_EMAIL);
    
    if (!$correo) {
        logToConsole("❌ ERROR: Correo electrónico inválido proporcionado");
        header('Location: recuperar_contra.html?error=correo_invalido');
        exit;
    }
    
    logToConsole("🚀 SOLICITUD: Procesando recuperación para $correo");
    
    // SIEMPRE procesar la solicitud
    $resultado = procesoRecuperacionSeguro($correo);
    
    if ($resultado) {
        logToConsole("✅ INFO: Proceso completado para $correo");
        header('Location: recuperar_contra.html?success=1&modo=seguro');
    } else {
        logToConsole("❌ ERROR CRÍTICO: Fallo en el proceso para $correo");
        header('Location: recuperar_contra.html?error=sistema');
    }
    exit;
}

// Si se accede directamente al archivo
logToConsole("⚠️ ADVERTENCIA: Acceso directo a enviar_correo_seguro.php sin datos POST");
header('Location: recuperar_contra.html');
exit;
?>
