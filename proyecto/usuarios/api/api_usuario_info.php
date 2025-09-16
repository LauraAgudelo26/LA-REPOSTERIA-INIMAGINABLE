<?php
/**
 * API para obtener información del usuario logueado
 * api_usuario_info.php
 */

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/config_db.php';

try {
    // Verificar si hay una sesión activa
    if (!isset($_SESSION['cliente_id']) || empty($_SESSION['cliente_id'])) {
        echo json_encode([
            'success' => false,
            'error' => 'Usuario no autenticado',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }

    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();
    
    // Obtener información del usuario
    $sql = "SELECT id, nombre, apellido, nombre_usuario, email, telefono, rol, fecha_registro FROM cliente WHERE id = ? AND activo = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['cliente_id']]);
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        // Usuario no encontrado o inactivo
        session_destroy();
        echo json_encode([
            'success' => false,
            'error' => 'Usuario no válido',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
    
    // Actualizar último acceso
    $updateSql = "UPDATE cliente SET ultimo_acceso = NOW() WHERE id = ?";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([$usuario['id']]);
    
    echo json_encode([
        'success' => true,
        'usuario' => [
            'id' => $usuario['id'],
            'nombre' => $usuario['nombre'],
            'apellido' => $usuario['apellido'],
            'nombre_usuario' => $usuario['nombre_usuario'],
            'email' => $usuario['email'],
            'telefono' => $usuario['telefono'],
            'rol' => $usuario['rol'],
            'fecha_registro' => $usuario['fecha_registro']
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error del servidor',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
