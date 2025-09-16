<?php
require_once '../../config/config_db.php';
require_once '../../config/functions.php';

session_start();

// Si es una solicitud POST, procesar el registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $tipo_documento = trim($_POST['tipo_documento'] ?? '');
    $numero_documento = trim($_POST['numero_documento'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmar_password = $_POST['confirmar_password'] ?? '';
    
    $errores = [];
    
    // Validaciones
    if (empty($nombre)) $errores[] = "El nombre es obligatorio";
    if (empty($apellido)) $errores[] = "El apellido es obligatorio";
    if (empty($nombre_usuario)) $errores[] = "El nombre de usuario es obligatorio";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Email inválido";
    }
    if (empty($telefono)) $errores[] = "El teléfono es obligatorio";
    if (empty($tipo_documento)) $errores[] = "El tipo de documento es obligatorio";
    if (empty($numero_documento)) $errores[] = "El número de documento es obligatorio";
    if (empty($password) || strlen($password) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres";
    }
    if ($password !== $confirmar_password) {
        $errores[] = "Las contraseñas no coinciden";
    }
    
    // Verificar si el email ya existe
    if (empty($errores) && emailExiste($email)) {
        $errores[] = "Este email ya está registrado";
    }
    
    // Verificar si el nombre de usuario ya existe
    if (empty($errores) && nombreUsuarioExiste($nombre_usuario)) {
        $errores[] = "Este nombre de usuario ya está en uso";
    }
    
    if (empty($errores)) {
        try {
            $db = DatabaseConnection::getInstance();
            $pdo = $db->getConnection();
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO cliente (nombre, apellido, nombre_usuario, email, telefono, direccion, tipo_documento, numero_documento, password, rol) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'cliente')";
            $stmt = $pdo->prepare($sql);
            $resultado = $stmt->execute([$nombre, $apellido, $nombre_usuario, $email, $telefono, $direccion, $tipo_documento, $numero_documento, $password_hash]);
            
            if ($resultado) {
                logearActividad("Usuario registrado exitosamente: $email");
                
                // Iniciar sesión automáticamente después del registro
                $_SESSION['usuario_logueado'] = true;
                $_SESSION['nombre_usuario'] = $nombre_usuario;
                $_SESSION['nombre_completo'] = $nombre . ' ' . $apellido;
                $_SESSION['rol'] = 'cliente';
                $_SESSION['mensaje_exito'] = "¡Bienvenido! Tu cuenta ha sido creada exitosamente.";
                
                header('Location: ../../public/views/index.html');
                exit;
            } else {
                $errores[] = "Error al registrar el usuario";
            }
        } catch (Exception $e) {
            logearActividad("Error al registrar usuario: " . $e->getMessage(), 'ERROR');
            $errores[] = "Error del sistema. Inténtalo más tarde.";
        }
    }
    
    if (!empty($errores)) {
        $_SESSION['errores_registro'] = $errores;
        $_SESSION['datos_anteriores'] = $_POST;
        header('Location: ../views/registrarse.html');
        exit;
    }
}

// Si llega aquí sin POST, redireccionar al formulario
header('Location: ../views/registrarse.html');
exit;
?>
