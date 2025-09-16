<?php
// Incluir configuración de base de datos
require_once '../../config/config_db.php';

// Función para registrar en consola/log
function logToConsole($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    
    // Escribir en archivo de log
    file_put_contents('correos_log.txt', $logMessage, FILE_APPEND | LOCK_EX);
    
    // También usar la función de logging del sistema
    logearActividad($message);
    
    // Mostrar en pantalla también
    echo "<script>console.log('$message');</script>";
    
    // Si se ejecuta desde línea de comandos
    if (php_sapi_name() === 'cli') {
        echo $logMessage;
    }
}

// Función para enviar correo con Gmail SMTP gratuito
function enviarCorreoGmail($destinatario) {
    // CONFIGURACIÓN GMAIL GRATUITA
    $gmail_user = "tu_correo@gmail.com"; // Tu Gmail
    $gmail_password = "tu_contraseña_app"; // Contraseña de aplicación
    
    $to = $destinatario;
    $subject = "Recuperación de Contraseña";
    $token = bin2hex(random_bytes(32));
    $enlace = "http://localhost/PAGINA/restablecer_contraseña.php?token=$token";
    
    $message = "
    <html>
    <body>
        <h2>Recuperación de Contraseña</h2>
        <p>Haz clic aquí para restablecer tu contraseña:</p>
        <a href='$enlace' style='background: #90E0EF; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Restablecer</a>
        <p>Token: $token</p>
    </body>
    </html>
    ";
    
    $headers = "From: $gmail_user\r\n";
    $headers .= "Reply-To: $gmail_user\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // Intentar envío
    if (mail($to, $subject, $message, $headers)) {
        logToConsole("✅ ÉXITO: Correo enviado a $destinatario");
        logToConsole("📧 Token generado: $token");
        logToConsole("🔗 Enlace: $enlace");
        return true;
    } else {
        logToConsole("❌ ERROR: No se pudo enviar correo a $destinatario");
        return false;
    }
}

// VERSIÓN SUPER SIMPLE - TODO A CONSOLA CON BASE DE DATOS
function procesoRecuperacionConsola($correo) {
    $db = DatabaseConnection::getInstance();
    
    // Verificar si el email existe en la base de datos
    if (!emailExiste($correo)) {
        logToConsole("❌ ERROR: El correo $correo no está registrado en el sistema");
        return false;
    }
    
    $token = bin2hex(random_bytes(16)); // Token más corto
    $enlace = "http://localhost/PAGINA/restablecer_contraseña.php?token=$token";
    
    // Guardar token en la base de datos
    try {
        $db->generarTokenRecuperacion($correo, $token);
        logToConsole("✅ Token guardado en base de datos para $correo");
    } catch (Exception $e) {
        logToConsole("❌ ERROR: No se pudo guardar el token en la base de datos: " . $e->getMessage());
        return false;
    }
    
    logToConsole("=== PROCESO DE RECUPERACIÓN INICIADO ===");
    logToConsole("📧 Correo solicitante: $correo");
    logToConsole("🔑 Token generado: $token");
    logToConsole("🔗 Enlace de recuperación: $enlace");
    logToConsole("⏰ Válido hasta: " . date('Y-m-d H:i:s', strtotime('+24 hours')));
    logToConsole("💾 Token guardado en base de datos");
    logToConsole("=========================================");
    
    // Simular envío de correo
    logToConsole("📨 SIMULANDO ENVÍO DE CORREO...");
    logToConsole("Para: $correo");
    logToConsole("Asunto: Recuperación de Contraseña");
    logToConsole("Contenido del correo:");
    logToConsole("---");
    logToConsole("Hola,");
    logToConsole("Usa este enlace para recuperar tu contraseña:");
    logToConsole($enlace);
    logToConsole("---");
    logToConsole("✅ Correo 'enviado' exitosamente (modo consola)");
    
    return $token;
}

// Procesar solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = filter_var($_POST['correo'] ?? '', FILTER_VALIDATE_EMAIL);
    
    if (!$correo) {
        logToConsole("❌ ERROR: Correo inválido");
        header('Location: recuperar_contraseña.html?error=correo_invalido');
        exit;
    }
    
    // MODO SIMPLE - SOLO CONSOLA (descomenta esta línea para usarlo)
    $token = procesoRecuperacionConsola($correo);
    header('Location: recuperar_contraseña.html?success=1&modo=consola');
    
    // MODO GMAIL (comenta la línea de arriba y descomenta estas para usar Gmail)
    // if (enviarCorreoGmail($correo)) {
    //     header('Location: recuperar_contraseña.html?success=1&modo=gmail');
    // } else {
    //     header('Location: recuperar_contraseña.html?error=no_enviado');
    // }
    
    exit;
}

// Acceso directo
logToConsole("⚠️ Acceso directo sin datos POST");
header('Location: recuperar_contraseña.html');
?>