<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña - Seguro</title>
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
            max-width: 500px;
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
            margin-top: 10px;
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
        .codigo-input {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .metodo-tabs {
            margin-bottom: 20px;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .nav-tabs .nav-link {
            color: #90E0EF;
        }
        .nav-tabs .nav-link.active {
            background-color: #90E0EF;
            color: white;
            border-color: #90E0EF;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Restablecer Contraseña</h2>
        
        <div id="messages"></div>
        
        <?php
        require_once 'enviar_correo_seguro.php';
        
        $token = $_GET['token'] ?? '';
        $mostrar_formulario = false;
        $registro_valido = null;
        
        if ($token) {
            // Verificar token desde URL
            $registro_valido = verificarCodigoArchivo($token);
            
            if ($registro_valido) {
                $mostrar_formulario = true;
                logToConsole("✅ Token válido verificado desde URL: $token");
            } else {
                echo '<div class="alert alert-danger">❌ Token inválido o expirado</div>';
                echo '<a href="recuperar_contra.html" class="btn btn-primary">Solicitar nuevo enlace</a>';
                logToConsole("❌ Token inválido desde URL: $token");
            }
        } else {
            // Mostrar formulario para ingresar código manualmente
            echo '<div class="metodo-tabs">
                    <ul class="nav nav-tabs" id="metodosTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="codigo-tab" data-bs-toggle="tab" data-bs-target="#codigo-content" type="button">Por Código</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="enlace-tab" data-bs-toggle="tab" data-bs-target="#enlace-content" type="button">Solicitar Enlace</button>
                        </li>
                    </ul>
                </div>';
        }
        ?>
        
        <?php if (!$token): ?>
        <!-- Tabs para diferentes métodos -->
        <div class="tab-content" id="metodosTabContent">
            <!-- Verificar por código -->
            <div class="tab-content active" id="codigo-content">
                <p>Ingresa el código de 6 caracteres que recibiste:</p>
                <form action="verificar_codigo.php" method="POST">
                    <div class="mb-3">
                        <input type="text" name="codigo" class="form-control codigo-input" placeholder="AB1C2D" maxlength="6" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="correo" class="form-control" placeholder="Tu correo electrónico" required>
                    </div>
                    <button type="submit">Verificar Código</button>
                </form>
            </div>
            
            <!-- Solicitar nuevo enlace -->
            <div class="tab-content" id="enlace-content">
                <p>¿No tienes un código? Solicita uno nuevo:</p>
                <form action="enviar_correo_seguro.php" method="POST">
                    <div class="mb-3">
                        <input type="email" name="correo" class="form-control" placeholder="Tu correo electrónico" required>
                    </div>
                    <button type="submit">Enviar Código</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($mostrar_formulario): ?>
        <!-- Formulario de nueva contraseña -->
        <div class="alert alert-success">
            ✅ Código verificado para: <strong><?php echo htmlspecialchars($registro_valido['correo']); ?></strong>
        </div>
        
        <form action="procesar_nueva_password_seguro.php" method="POST" id="resetForm">
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
        
        <div class="mt-3">
            <small class="text-muted">
                💡 <strong>Modo Desarrollo:</strong> Revisa los archivos <code>correos_log.txt</code> y <code>codigos_recuperacion.json</code>
            </small>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Manejo de tabs
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('#metodosTab button');
            const contents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remover active de todos
                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));
                    
                    // Agregar active al clickeado
                    this.classList.add('active');
                    const target = this.getAttribute('data-bs-target').replace('#', '').replace('-content', '-content');
                    document.getElementById(target).classList.add('active');
                });
            });
        });
        
        // Validación del formulario de contraseña
        document.getElementById('resetForm')?.addEventListener('submit', function(e) {
            const password = document.getElementById('nueva_password').value;
            const confirm = document.getElementById('confirmar_password').value;
            const messagesDiv = document.getElementById('messages');
            
            messagesDiv.innerHTML = '';
            
            if (password !== confirm) {
                e.preventDefault();
                messagesDiv.innerHTML = '<div class="alert alert-danger">❌ Las contraseñas no coinciden</div>';
                return;
            }
            
            const hasUpperCase = /[A-Z]/.test(password);
            const hasLowerCase = /[a-z]/.test(password);
            const hasNumbers = /\d/.test(password);
            const isLongEnough = password.length >= 8;
            
            if (!hasUpperCase || !hasLowerCase || !hasNumbers || !isLongEnough) {
                e.preventDefault();
                messagesDiv.innerHTML = '<div class="alert alert-danger">❌ La contraseña no cumple con los requisitos mínimos</div>';
                return;
            }
            
            messagesDiv.innerHTML = '<div class="alert alert-info">🔄 Procesando...</div>';
        });
        
        // Formatear código automáticamente
        document.querySelector('input[name="codigo"]')?.addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        });
        
        // Mostrar mensajes de URL
        const urlParams = new URLSearchParams(window.location.search);
        const messagesDiv = document.getElementById('messages');
        
        if (urlParams.get('success')) {
            messagesDiv.innerHTML = '<div class="alert alert-success">✅ ¡Contraseña actualizada exitosamente!</div>';
        }
        
        if (urlParams.get('error') === 'codigo_invalido') {
            messagesDiv.innerHTML = '<div class="alert alert-danger">❌ Código inválido o expirado</div>';
        }
        
        if (urlParams.get('error') === 'correo_invalido') {
            messagesDiv.innerHTML = '<div class="alert alert-danger">❌ Correo electrónico inválido</div>';
        }
    </script>
</body>
</html>
