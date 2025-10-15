// Variables globales
let todosLosProductos = [];
let categorias = [];
let categoriaActual = 'all';
let usuarioLogueado = null;

// Elementos del DOM
const loadingScreen = document.getElementById('loadingScreen');
const productosContainer = document.getElementById('productosContainer');
const destacadosContainer = document.getElementById('destacadosContainer');
const categoryFilter = document.getElementById('categoryFilter');
const userInfo = document.getElementById('userInfo');
const userName = document.getElementById('userName');
const noProducts = document.getElementById('noProducts');
const productosDestacados = document.getElementById('productosDestacados');

// ===== FUNCIONES DE SESIÓN =====

// Función para verificar si el usuario está logueado
async function verificarSesion() {
    try {
        console.log('🔍 Verificando sesión...');
        
        if (!api.isAuthenticated()) {
            console.log('ℹ️ Usuario no autenticado');
            mostrarBotonesAuth();
            return;
        }

        // Obtener perfil del usuario
        const response = await api.getProfile();
        console.log('📊 Datos de perfil:', response);
        
        if (response.success) {
            usuarioLogueado = response.data;
            mostrarInfoUsuario();
            console.log('✅ Información de usuario mostrada');
        } else {
            console.log('❌ Error en datos de usuario:', response.error);
            mostrarBotonesAuth();
        }
    } catch (error) {
        console.error('❌ Error verificando sesión:', error);
        api.clearAuth();
        mostrarBotonesAuth();
    }
}

// Función para mostrar botones de autenticación
function mostrarBotonesAuth() {
    console.log('🔄 Mostrando botones de autenticación');
    if (userInfo) userInfo.style.display = 'none';
    const authButtons = document.getElementById('authButtons');
    if (authButtons) authButtons.style.display = 'block';
}

// Mostrar información del usuario logueado
function mostrarInfoUsuario() {
    if (usuarioLogueado) {
        console.log('👤 Mostrando información del usuario:', usuarioLogueado);
        
        // Actualizar nombre del usuario
        if (userName) {
            // Usar nombre_completo si existe, sino concatenar nombre y apellido
            const nombreMostrar = usuarioLogueado.nombre_completo || 
                                  usuarioLogueado.nombre || 
                                  'Usuario';
            userName.textContent = nombreMostrar;
        }
        
        // Mostrar info del usuario y ocultar botones de auth
        if (userInfo) userInfo.style.display = 'block';
        const authButtons = document.getElementById('authButtons');
        if (authButtons) authButtons.style.display = 'none';
        
        // Configurar evento de logout (solo una vez)
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn && !logoutBtn.hasAttribute('data-configured')) {
            logoutBtn.setAttribute('data-configured', 'true');
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                logout();
            });
        }
        
        console.log('✅ Info de usuario configurada correctamente');
    }
}

// Función de logout
async function logout() {
    const result = await Swal.fire({
        title: '¿Cerrar sesión?',
        text: '¿Estás seguro de que quieres cerrar tu sesión?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, cerrar sesión',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        try {
            await api.logout();
            window.location.reload();
        } catch (error) {
            console.error('Error en logout:', error);
            api.clearAuth();
            window.location.reload();
        }
    }
}

// ===== FUNCIONES DE CATEGORÍAS =====

// Cargar categorías
async function cargarCategorias() {
    try {
        const response = await api.getCategorias();
        
        if (response.success) {
            // Filtrar categorías excluyendo "Tortas Especiales" (ID 2)
            categorias = response.data.filter(categoria => categoria.id !== 2);
            renderizarFiltrosCategorias();
        }
    } catch (error) {
        console.error('Error cargando categorías:', error);
    }
}

// Renderizar filtros de categorías
function renderizarFiltrosCategorias() {
    // Limpiar filtros existentes (excepto "Todos")
    const botones = categoryFilter.querySelectorAll('.category-btn:not([data-category="all"])');
    botones.forEach(btn => btn.remove());

    // Agregar categorías
    categorias.forEach(categoria => {
        const button = document.createElement('button');
        button.className = 'category-btn';
        button.setAttribute('data-category', categoria.id);
        button.innerHTML = `<i class="fas fa-tag"></i> ${categoria.nombre}`;
        
        button.addEventListener('click', () => filtrarPorCategoria(categoria.id));
        categoryFilter.appendChild(button);
    });
}

// Filtrar por categoría
function filtrarPorCategoria(categoriaId) {
    categoriaActual = categoriaId;
    
    // Actualizar botones activos
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    document.querySelector(`[data-category="${categoriaId}"]`).classList.add('active');
    
    // Renderizar productos filtrados
    renderizarProductos();
}

// ===== FUNCIONES DE PRODUCTOS =====

// Cargar productos
async function cargarProductos() {
    try {
        const response = await api.getProductos();
        
        if (response.success) {
            // Filtrar productos excluyendo categoría "Tortas Especiales" (ID 2)
            todosLosProductos = response.data.filter(producto => producto.categoria_id !== 2);
            renderizarProductos();
            renderizarProductosDestacados();
        } else {
            throw new Error(response.error || 'Error desconocido');
        }
    } catch (error) {
        console.error('Error cargando productos:', error);
        mostrarError('Error al cargar productos', error.message);
    }
}

// Renderizar productos destacados
async function renderizarProductosDestacados() {
    try {
        const response = await api.getProductosDestacados();
        
        if (response.success && response.data.length > 0) {
            // Filtrar productos destacados excluyendo categoría 2
            const destacadosFiltrados = response.data.filter(producto => producto.categoria_id !== 2);
            
            if (destacadosFiltrados.length > 0) {
                destacadosContainer.innerHTML = '';
                destacadosFiltrados.forEach(producto => {
                    destacadosContainer.appendChild(crearCardProducto(producto));
                });
                productosDestacados.style.display = 'block';
            }
        }
    } catch (error) {
        console.error('Error cargando productos destacados:', error);
    }
}

// Renderizar productos
function renderizarProductos() {
    let productosFiltrados = todosLosProductos;
    
    // Filtrar por categoría si no es "all"
    if (categoriaActual !== 'all') {
        productosFiltrados = todosLosProductos.filter(producto => 
            producto.categoria_id == categoriaActual
        );
    }

    // Limpiar contenedor
    productosContainer.innerHTML = '';

    if (productosFiltrados.length === 0) {
        noProducts.style.display = 'block';
        return;
    }

    noProducts.style.display = 'none';

    // Renderizar productos
    productosFiltrados.forEach(producto => {
        productosContainer.appendChild(crearCardProducto(producto));
    });
}

// Crear card de producto
function crearCardProducto(producto) {
    const col = document.createElement('div');
    col.className = 'col-lg-3 col-md-4 col-sm-6 mb-4';

    // Formatear precio
    const precioFormateado = parseFloat(producto.precio).toLocaleString('es-CO');
    
    // URL de imagen (asumiendo que las imágenes están en /img/)
    const imagenUrl = producto.imagen_url || `/img/${producto.imagen}` || '/img/logo.jpg';

    col.innerHTML = `
        <div class="card h-100" style="position: relative;">
            ${producto.destacado ? '<div class="destacado-badge">Destacado</div>' : ''}
            <img src="${imagenUrl}" class="card-img-top" alt="${producto.nombre}" 
                 onerror="this.src='/img/logo.jpg'">
            <div class="card-body">
                <div class="categoria-badge">${producto.categoria_nombre || 'Sin categoría'}</div>
                <h5 class="card-title">${producto.nombre}</h5>
                <p class="card-text">${producto.descripcion || 'Delicioso producto artesanal'}</p>
                <div class="stock-info">
                    <i class="fas fa-box"></i> Stock: ${producto.stock || 0} unidades
                </div>
                <div class="price">$${precioFormateado}</div>
                <button class="btn btn-fruty btn-ordenar" 
                        data-producto='${JSON.stringify(producto)}'>
                    <i class="fas fa-shopping-cart"></i> Ordenar
                </button>
            </div>
        </div>
    `;

    // Agregar event listener al botón
    const btnOrdenar = col.querySelector('.btn-ordenar');
    btnOrdenar.addEventListener('click', function() {
        const productoData = JSON.parse(this.getAttribute('data-producto'));
        manejarPedido(productoData);
    });

    return col;
}

// ===== FUNCIONES DE PEDIDOS =====

// Manejar pedido de producto
async function manejarPedido(producto) {
    // Verificar si el usuario está logueado
    if (!usuarioLogueado) {
        const result = await Swal.fire({
            title: 'Inicia Sesión',
            text: 'Debes iniciar sesión para realizar pedidos',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Iniciar Sesión',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#e91e63'
        });

        if (result.isConfirmed) {
            window.location.href = 'login.html';
        }
        return;
    }

    // Mostrar formulario de pedido
    const result = await Swal.fire({
        title: '🛒 Realizar Pedido',
        html: `
            <div class="text-start">
                <p><strong>Producto:</strong> ${producto.nombre}</p>
                <p><strong>Precio:</strong> $${parseFloat(producto.precio).toLocaleString('es-CO')}</p>
                <hr>
                <div class="mb-3">
                    <label for="cantidad" class="form-label">Cantidad:</label>
                    <input type="number" id="cantidad" class="form-control" value="1" min="1" max="${producto.stock || 10}">
                </div>
                <div class="mb-3">
                    <label for="notas" class="form-label">Notas especiales (opcional):</label>
                    <textarea id="notas" class="form-control" rows="3" placeholder="Instrucciones especiales para tu pedido..."></textarea>
                </div>
                <div class="mb-3">
                    <label for="direccion" class="form-label">Dirección de entrega:</label>
                    <input type="text" id="direccion" class="form-control" placeholder="Calle 123 #45-67">
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Confirmar Pedido',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#e91e63',
        preConfirm: () => {
            const cantidad = document.getElementById('cantidad').value;
            const notas = document.getElementById('notas').value;
            const direccion = document.getElementById('direccion').value;
            
            if (!cantidad || cantidad < 1) {
                Swal.showValidationMessage('La cantidad debe ser mayor a 0');
                return false;
            }
            
            if (!direccion || direccion.trim() === '') {
                Swal.showValidationMessage('La dirección de entrega es obligatoria');
                return false;
            }
            
            return {
                cantidad: parseInt(cantidad),
                notas: notas.trim(),
                direccion: direccion.trim()
            };
        }
    });

    if (result.isConfirmed) {
        await procesarPedido(producto, result.value);
    }
}

// Procesar pedido
async function procesarPedido(producto, datosPedido) {
    try {
        const total = parseFloat(producto.precio) * datosPedido.cantidad;
        
        // Crear pedido en la API
        const pedidoData = {
            cliente_id: usuarioLogueado.id,
            productos: [
                {
                    producto_id: producto.id,
                    cantidad: datosPedido.cantidad,
                    precio_unitario: producto.precio
                }
            ],
            direccion_entrega: datosPedido.direccion,
            telefono: datosPedido.telefono || null,
            notas: datosPedido.notas,
            total: total
        };

        const response = await api.createPedido(pedidoData);

        if (response.success) {
            await Swal.fire({
                title: '¡Pedido Enviado!',
                html: `
                    <div class="text-start">
                        <h5>Resumen del Pedido:</h5>
                        <p><strong>Producto:</strong> ${producto.nombre}</p>
                        <p><strong>Cantidad:</strong> ${datosPedido.cantidad}</p>
                        <p><strong>Precio unitario:</strong> $${parseFloat(producto.precio).toLocaleString('es-CO')}</p>
                        <p><strong>Total:</strong> $${total.toLocaleString('es-CO')}</p>
                        ${datosPedido.notas ? `<p><strong>Notas:</strong> ${datosPedido.notas}</p>` : ''}
                        <p><strong>Dirección:</strong> ${datosPedido.direccion}</p>
                        <hr>
                        <p class="text-muted">Tu pedido ha sido registrado exitosamente. Nos pondremos en contacto contigo pronto.</p>
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#27ae60'
            });
        } else {
            throw new Error(response.error);
        }
    } catch (error) {
        console.error('Error procesando pedido:', error);
        Swal.fire({
            title: 'Error',
            text: error.message || 'No se pudo procesar el pedido',
            icon: 'error',
            confirmButtonText: 'Entendido'
        });
    }
}

// ===== UTILIDADES =====

// Mostrar error
function mostrarError(titulo, mensaje) {
    Swal.fire({
        title: titulo,
        text: mensaje,
        icon: 'error',
        confirmButtonText: 'Entendido'
    });
}

// ===== INICIALIZACIÓN =====

// Inicializar la aplicación
async function inicializar() {
    try {
        // Mostrar pantalla de carga por al menos 2 segundos
        const tiempoMinimo = new Promise(resolve => setTimeout(resolve, 2000));
        
        // Cargar datos en paralelo
        const promesas = [
            verificarSesion(),
            cargarCategorias(),
            cargarProductos(),
            tiempoMinimo
        ];
        
        await Promise.all(promesas);
        
        // Ocultar pantalla de carga
        loadingScreen.classList.add('fade-out');
        setTimeout(() => {
            loadingScreen.style.display = 'none';
        }, 500);
        
    } catch (error) {
        console.error('Error inicializando aplicación:', error);
        mostrarError('Error de carga', 'Hubo un problema cargando la página. Por favor, recarga.');
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', inicializar);

// Verificar sesión cuando la ventana regain focus
window.addEventListener('focus', function() {
    console.log('🔄 Ventana recuperó el foco, verificando sesión...');
    verificarSesion();
});
