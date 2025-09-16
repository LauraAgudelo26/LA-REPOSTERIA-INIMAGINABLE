<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #fff0f5, #90E0EF, #f5e0e7);
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .container {
            max-width: 400px;
            margin: auto;
            padding: 30px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        button {
            background-color: #90E0EF;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px;
            width: 100%;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #ff69b4;
        }
        .alert {
            margin-bottom: 20px;
        }
        .password-requirements {
            font-size: 12px;
            color: #6c757d;
            margin-top: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Restablecer Contraseña</h2>
        
        <div id="messages"></div>
        
        <?php
        require_once '../../config/config_db.php';
        
        $token = $_GET['token'] ?? '';
        $mostrar_formulario = false;
        
        if ($token) {
            try {
                $db = DatabaseConnection::getInstance();
                $resultado = $db->verificarToken($token);
                
                if ($resultado['valido']) {
                    $mostrar_formulario = true;
                    logearActividad("Token válido verificado: $token");
                } else {
                    echo '<div class="alert alert-danger">❌ Token inválido o expirado</div>';
                    echo '<a href="recuperar_contra.html" class="btn btn-primary">Solicitar nuevo enlace</a>';
                    logearActividad("Token inválido o expirado: $token");
                }
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">❌ Error al verificar el token</div>';
                logearActividad("Error al verificar token: " . $e->getMessage());
            }
        } else {
            echo '<div class="alert alert-warning">⚠️ Token no proporcionado</div>';
            echo '<a href="recuperar_contra.html" class="btn btn-primary">Ir a recuperar contraseña</a>';
        }
        
        if ($mostrar_formulario):
        ?>
        
        <form action="procesar_nueva_password.php" method="POST" id="resetForm">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <div class="mb-3">
                <label for="nueva_password" class="form-label">Nueva Contraseña:</label>
                <input type="password" name="nueva_password" id="nueva_password" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label for="confirmar_password" class="form-label">Confirmar Contraseña:</label>
                <input type="password" name="confirmar_password" id="confirmar_password" class="form-control" required>
            </div>
            
            <div class="password-requirements">
                <strong>Requisitos de la contraseña:</strong>
                <ul>
                    <li>Mínimo 8 caracteres</li>
                    <li>Al menos una letra mayúscula</li>
                    <li>Al menos una letra minúscula</li>
                    <li>Al menos un número</li>
                </ul>
            </div>
            
            <button type="submit">Actualizar Contraseña</button>
        </form>
        
        <?php endif; ?>
    </div>
    
    <script>
        // Validación del formulario
        document.getElementById('resetForm')?.addEventListener('submit', function(e) {
            const password = document.getElementById('nueva_password').value;
            const confirm = document.getElementById('confirmar_password').value;
            const messagesDiv = document.getElementById('messages');
            
            // Limpiar mensajes previos
            messagesDiv.innerHTML = '';
            
            // Validar que las contraseñas coincidan
            if (password !== confirm) {
                e.preventDefault();
                messagesDiv.innerHTML = '<div class="alert alert-danger">❌ Las contraseñas no coinciden</div>';
                return;
            }
            
            // Validar requisitos de contraseña
            const hasUpperCase = /[A-Z]/.test(password);
            const hasLowerCase = /[a-z]/.test(password);
            const hasNumbers = /\d/.test(password);
            const isLongEnough = password.length >= 8;
            
            if (!hasUpperCase || !hasLowerCase || !hasNumbers || !isLongEnough) {
                e.preventDefault();
                messagesDiv.innerHTML = '<div class="alert alert-danger">❌ La contraseña no cumple con los requisitos mínimos</div>';
                return;
            }
            
            // Si todo está bien, mostrar mensaje de procesamiento
            messagesDiv.innerHTML = '<div class="alert alert-info">🔄 Procesando...</div>';
        });
        
        // Mostrar mensajes de URL
        const urlParams = new URLSearchParams(window.location.search);
        const messagesDiv = document.getElementById('messages');
        
        if (urlParams.get('success')) {
            messagesDiv.innerHTML = '<div class="alert alert-success">✅ ¡Contraseña actualizada exitosamente! Puedes iniciar sesión con tu nueva contraseña.</div>';
        }
        
        if (urlParams.get('error') === 'passwords_mismatch') {
            messagesDiv.innerHTML = '<div class="alert alert-danger">❌ Las contraseñas no coinciden</div>';
        }
        
        if (urlParams.get('error') === 'invalid_token') {
            messagesDiv.innerHTML = '<div class="alert alert-danger">❌ Token inválido o expirado</div>';
        }
        
        if (urlParams.get('error') === 'weak_password') {
            messagesDiv.innerHTML = '<div class="alert alert-danger">❌ La contraseña no cumple con los requisitos</div>';
        }
    </script>
</body>
</html>
