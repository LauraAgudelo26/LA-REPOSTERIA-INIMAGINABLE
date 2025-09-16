<?php
require_once 'enviar_correo_seguro.php';

// Verificar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logToConsole("❌ Acceso inválido a verificar_codigo.php");
    header('Location: restablecer_contraseña_seguro.php');
    exit;
}

$codigo = strtoupper(trim($_POST['codigo'] ?? ''));
$correo = trim($_POST['correo'] ?? '');

// Validaciones básicas
if (empty($codigo) || empty($correo)) {
    logToConsole("❌ Datos incompletos para verificar código");
    header('Location: restablecer_contraseña_seguro.php?error=datos_incompletos');
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    logToConsole("❌ Correo inválido proporcionado: $correo");
    header('Location: restablecer_contraseña_seguro.php?error=correo_invalido');
    exit;
}

logToConsole("🔍 Verificando código: $codigo para correo: $correo");

// Verificar código en archivo
$registro = verificarCodigoArchivo($codigo, $correo);

if ($registro) {
    logToConsole("✅ Código válido verificado: $codigo para $correo");
    logToConsole("🎫 Token asociado: " . $registro['token']);
    
    // Redirigir al formulario de nueva contraseña con el token
    header('Location: restablecer_contraseña_seguro.php?token=' . urlencode($registro['token']));
} else {
    logToConsole("❌ Código inválido o expirado: $codigo para $correo");
    header('Location: restablecer_contraseña_seguro.php?error=codigo_invalido');
}

exit;
?>
