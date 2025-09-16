<?php
// Configuración de conexión a la base de datos
$host = "localhost";
$dbname = "tu_base_de_datos";
$user = "tu_usuario";
$pass = "tu_contraseña";

try {
    // Crear conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Error de conexión a la base de datos.");
}

// Obtener token desde la URL
$token = $_GET['token'] ?? '';

if (!$token) {
    die("Token inválido.");
}

// Consultar token en la base de datos
$stmt = $pdo->prepare("SELECT usuario_id, expiracion, usado FROM tokens_recuperacion WHERE token = ?");
$stmt->execute([$token]);
$tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

// Aquí continuarías con la lógica para validar expiración y uso del token
?>