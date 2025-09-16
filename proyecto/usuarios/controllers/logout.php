<?php
/**
 * Script para cerrar sesión de usuario
 * logout.php
 */

session_start();

// Limpiar todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la sesión completamente, eliminar también la cookie de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Redireccionar al inicio con mensaje
header('Location: ../../public/views/index.html?logout=success');
exit;
?>
