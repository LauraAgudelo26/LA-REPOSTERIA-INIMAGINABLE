<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login Responsive</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Íconos -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #fff0f5, #90E0EF, #f5e0e7);
      font-family: 'Segoe UI', sans-serif;
    }

    .login-container {
      max-width: 400px;
      margin: 80px auto;
      padding: 30px;
      background-color: #ffffff;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .login-container h2 {
      color: #ff69b4;
      margin-bottom: 20px;
    }

    .login-container input[type="text"],
    .login-container input[type="password"] {
      border-radius: 10px;
      border: 1px solid #ffb6c1;
      padding: 10px;
      margin: 10px 0;
      width: 100%;
    }

    .login-container input[type="submit"],
    .login-container .btn-registrarse {
      background-color: #90E0EF;
      color: white;
      border: none;
      border-radius: 10px;
      padding: 10px;
      margin-top: 10px;
      width: 100%;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .login-container input[type="submit"]:hover,
    .login-container .btn-registrarse:hover {
      background-color: #ff69b4;
    }

    .overlay {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background-color: rgba(0,0,0,0.4);
      z-index: 1;
    }

    .icono-login {
      position: absolute;
      top: 20px;
      right: 20px;
      font-size: 2rem;
      color: #ff69b4;
      cursor: pointer;
    }
  </style>
</head>
<body>

  <!-- Ícono de usuario -->
  <div class="icono-login" onclick="mostrarLogin()">
    <i class="fas fa-user-circle"></i>
  </div>

  <!-- Fondo oscuro al abrir login -->
  <div class="overlay" onclick="cerrarLogin()"></div>

  <div class="login-container" id="formularioLogin">
    <h2>Iniciar Sesión</h2>
    
    <?php
    session_start();
    // Mostrar errores si existen
    if (isset($_SESSION['errores_login'])) {
        echo '<div class="alert alert-danger">';
        foreach ($_SESSION['errores_login'] as $error) {
            echo '<p>' . htmlspecialchars($error) . '</p>';
        }
        echo '</div>';
        unset($_SESSION['errores_login']);
    }
    
    // Recuperar email anterior si existía
    $email_anterior = $_SESSION['email_anterior'] ?? '';
    unset($_SESSION['email_anterior']);
    ?>
    
    <form action="procesar_login.php" method="POST">
      <input type="email" name="email" placeholder="Correo electrónico" value="<?php echo htmlspecialchars($email_anterior); ?>" required>
      <input type="password" name="password" placeholder="Contraseña" required>
      <div class="form-check" style="margin: 10px 0; text-align: left;">
        <input type="checkbox" name="recordar" id="recordar" class="form-check-input">
        <label for="recordar" class="form-check-label" style="font-size: 14px;">Recordarme</label>
      </div>
      <input type="submit" value="Entrar">
    </form>

    <form action="recuperar_contra.html" method="GET">
      <button type="submit" class="btn-registrarse">Recuperar Contraseña</button>
    </form>
    <form id="formRecuperar" action="enviar_recuperacion.php" method="POST">
    <input type="email" name="email" placeholder="Correo electrónico" required>
      <button type="submit" class="btn-registrarse">Recuperar Contraseña</button>
    </form>

    <p>¿No tienes cuenta?</p>
    <!-- Este es el formulario de registro que redirige automáticamente -->
    <form id="formRegistro" action="guardar_usuario.php" method="POST">
      <button type="submit" class="btn-registrarse">Registrarse</button>
    </form>
  </div>

  <script>
    function mostrarLogin() {
      document.getElementById('formularioLogin').style.display = 'block';
      document.querySelector('.overlay').style.display = 'block';
    }

    function cerrarLogin() {
      document.getElementById('formularioLogin').style.display = 'none';
      document.querySelector('.overlay').style.display = 'none';
    }

    // Redirección después de registrarse
    document.getElementById("formRegistro").addEventListener("submit", function (e) {
      e.preventDefault(); // Detiene el envío normal

      const form = e.target;
      const formData = new FormData(form);

      fetch(form.action, {
        method: form.method,
        body: formData,
      })
      .then(response => {
        if (response.ok) {
          // Si el registro fue exitoso, redirige al login
          window.location.href = "iniciar_sesion.html";
        } else {
          alert("Error al registrarse. Intenta de nuevo.");
        }
      })
      .catch(error => {
        console.error("Error:", error);
        alert("Hubo un problema al conectar con el servidor.");
      });
    });
  </script>

</body>
</html>
