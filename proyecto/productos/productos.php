<?php
require_once 'controlador_productos.php';
require_once 'procesar_login.php';

$controlador = new ProductoController();
$categorias = $controlador->obtenerCategorias();
$productos = $controlador->obtenerTodosLosProductos();

// Verificar si hay filtro por categoría
$categoria_filtro = $_GET['categoria'] ?? null;
if ($categoria_filtro) {
    $productos = $controlador->obtenerProductosPorCategoria($categoria_filtro);
}

// Verificar si hay búsqueda
$busqueda = $_GET['buscar'] ?? '';
if (!empty($busqueda)) {
    $productos = $controlador->buscarProductos($busqueda);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Productos - La Repostería De Lo Inimaginable</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    body {
      background: linear-gradient(to right, #e0f7fa, #fdfcfb);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .header {
      background: linear-gradient(135deg, #90E0EF 0%, #fad0c4 100%);
      padding: 20px 0;
      margin-bottom: 30px;
      border-radius: 0 0 20px 20px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .title {
      font-family: 'Comic Sans MS', cursive;
      color: white;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
      font-size: 2.5rem;
      text-align: center;
    }

    h1 {
      font-weight: 600;
      color: #333;
    }

    .card {
      margin-bottom: 25px;
      border: none;
      border-radius: 15px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      height: 100%;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
    }

    .card-img-top {
      height: 200px;
      object-fit: cover;
      border-radius: 15px 15px 0 0;
    }

    .card-body {
      padding: 25px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .card-title {
      font-size: 20px;
      font-weight: bold;
      color: #0d47a1;
      margin-bottom: 10px;
    }

    .card-text {
      font-size: 15px;
      color: #555;
      flex-grow: 1;
    }

    .price {
      font-size: 18px;
      color: #2e7d32;
      font-weight: bold;
      margin-top: 10px;
    }

    .stock-info {
      font-size: 14px;
      color: #666;
      margin-top: 5px;
    }

    .stock-low {
      color: #ff5722;
    }

    .stock-out {
      color: #f44336;
      font-weight: bold;
    }

    .no-products {
      font-size: 18px;
      color: #999;
      text-align: center;
      margin-top: 40px;
    }

    .section-divider {
      height: 3px;
      width: 60px;
      background-color: #2196f3;
      margin: 0 auto 30px auto;
      border-radius: 50px;
    }

    .filter-section {
      background: white;
      padding: 20px;
      border-radius: 15px;
      margin-bottom: 30px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .btn-category {
      margin: 5px;
      border-radius: 20px;
    }

    .btn-category.active {
      background-color: #90E0EF;
      border-color: #90E0EF;
    }

    .user-info {
      color: white;
      text-align: right;
      margin-top: 10px;
    }

    .btn-cart {
      background-color: #ff69b4;
      border: none;
      color: white;
      padding: 8px 15px;
      border-radius: 20px;
      transition: all 0.3s;
    }

    .btn-cart:hover {
      background-color: #e91e63;
      transform: scale(1.05);
    }

    .btn-cart:disabled {
      background-color: #ccc;
      cursor: not-allowed;
    }
  </style>
</head>
<body>

<div class="header">
  <div class="container">
    <h1 class="title">La Repostería De Lo Inimaginable</h1>
    <?php if (usuarioLogueado()): ?>
      <?php $usuario = obtenerUsuarioActual(); ?>
      <div class="user-info">
        <i class="fas fa-user"></i> Bienvenido, <?php echo htmlspecialchars($usuario['nombre_completo']); ?>
        <a href="procesar_login.php?logout=1" class="btn btn-sm btn-outline-light ms-2">
          <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </a>
      </div>
    <?php else: ?>
      <div class="user-info">
        <a href="iniciar_sesion.php" class="btn btn-sm btn-outline-light">
          <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="container py-3">
  <h1 class="text-center mb-2">Nuestros Productos</h1>
  <div class="section-divider"></div>
  
  <!-- Sección de Filtros y Búsqueda -->
  <div class="filter-section">
    <div class="row">
      <div class="col-md-6">
        <h5><i class="fas fa-filter"></i> Filtrar por Categoría:</h5>
        <a href="productos.php" class="btn btn-outline-secondary btn-category <?php echo empty($categoria_filtro) ? 'active' : ''; ?>">
          Todas
        </a>
        <?php foreach ($categorias as $categoria): ?>
          <a href="productos.php?categoria=<?php echo $categoria['idCategoria']; ?>" 
             class="btn btn-outline-secondary btn-category <?php echo $categoria_filtro == $categoria['idCategoria'] ? 'active' : ''; ?>">
            <?php echo htmlspecialchars($categoria['Nombre']); ?>
          </a>
        <?php endforeach; ?>
      </div>
      <div class="col-md-6">
        <h5><i class="fas fa-search"></i> Buscar Productos:</h5>
        <form method="GET" action="productos.php" class="d-flex">
          <input type="text" name="buscar" class="form-control me-2" 
                 placeholder="Buscar productos..." value="<?php echo htmlspecialchars($busqueda); ?>">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i>
          </button>
        </form>
      </div>
    </div>
  </div>
  
  <!-- Resultados -->
  <?php if (!empty($busqueda)): ?>
    <div class="alert alert-info">
      <i class="fas fa-info-circle"></i> 
      Resultados de búsqueda para: "<strong><?php echo htmlspecialchars($busqueda); ?></strong>"
      (<?php echo count($productos); ?> productos encontrados)
    </div>
  <?php endif; ?>
  
  <div class="row mt-4">
    <?php if (!empty($productos)): ?>
      <?php foreach ($productos as $producto): ?>
        <div class="col-md-4 col-lg-3 d-flex align-items-stretch">
          <div class="card w-100">
            <?php 
            $imagen = $controlador->obtenerImagenProducto($producto['Producto']);
            $precio_formateado = $controlador->formatearPrecio($producto['Precio']);
            ?>
            <img src="<?php echo $imagen; ?>" 
                 class="card-img-top" 
                 alt="<?php echo htmlspecialchars($producto['Producto']); ?>"
                 onerror="this.src='img/logo.crdownload'">
            <div class="card-body">
              <h5 class="card-title"><?php echo htmlspecialchars($producto['Producto']); ?></h5>
              <p class="card-text"><?php echo htmlspecialchars($producto['Descripcion']); ?></p>
              <p class="text-muted"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($producto['Categoria']); ?></p>
              <div class="mt-auto">
                <p class="price"><?php echo $precio_formateado; ?></p>
                
                <?php if ($producto['Stock'] > 0): ?>
                  <p class="stock-info <?php echo $producto['Stock'] <= 5 ? 'stock-low' : ''; ?>">
                    <i class="fas fa-box"></i> 
                    <?php if ($producto['Stock'] <= 5): ?>
                      ¡Solo quedan <?php echo $producto['Stock']; ?>!
                    <?php else: ?>
                      En stock (<?php echo $producto['Stock']; ?>)
                    <?php endif; ?>
                  </p>
                  
                  <?php if (usuarioLogueado()): ?>
                    <button class="btn btn-cart w-100" onclick="agregarAlCarrito(<?php echo $producto['idProducto']; ?>)">
                      <i class="fas fa-shopping-cart"></i> Agregar al Carrito
                    </button>
                  <?php else: ?>
                    <a href="iniciar_sesion.php" class="btn btn-cart w-100">
                      <i class="fas fa-sign-in-alt"></i> Inicia sesión para comprar
                    </a>
                  <?php endif; ?>
                <?php else: ?>
                  <p class="stock-info stock-out">
                    <i class="fas fa-times-circle"></i> Agotado
                  </p>
                  <button class="btn btn-cart w-100" disabled>
                    <i class="fas fa-ban"></i> No disponible
                  </button>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="no-products">
          <i class="fas fa-search" style="font-size: 3rem; color: #ccc; margin-bottom: 20px;"></i>
          <p>No se encontraron productos<?php echo !empty($busqueda) ? ' para tu búsqueda' : ''; ?>.</p>
          <?php if (!empty($busqueda) || $categoria_filtro): ?>
            <a href="productos.php" class="btn btn-primary">Ver todos los productos</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
  
  <!-- Botón para volver al inicio -->
  <div class="text-center mt-4">
    <a href="index.html" class="btn btn-secondary btn-lg">
      <i class="fas fa-home"></i> Volver al Inicio
    </a>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Función para agregar al carrito (implementar según necesidades)
function agregarAlCarrito(productoId) {
    // Por ahora, solo mostrar un mensaje
    alert('Producto agregado al carrito (ID: ' + productoId + ')');
    
    // Aquí puedes implementar la lógica real del carrito:
    // - Hacer una petición AJAX para agregar al carrito
    // - Actualizar contador del carrito
    // - Mostrar notificación
    
    /*
    fetch('carrito.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'agregar',
            producto_id: productoId,
            cantidad: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar UI
            actualizarContadorCarrito();
            mostrarNotificacion('Producto agregado al carrito');
        } else {
            alert('Error al agregar producto: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al agregar producto');
    });
    */
}

// Función para mostrar/ocultar categorías en móvil
function toggleCategorias() {
    const categorias = document.querySelector('.btn-category').parentElement;
    categorias.style.display = categorias.style.display === 'none' ? 'block' : 'none';
}
</script>

</body>
</html>
