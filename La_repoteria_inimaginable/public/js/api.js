// Configuración de la API
const API_URL = 'http://localhost:3000/api';

// Clase para manejar las llamadas a la API
class API {
    constructor(baseURL) {
        this.baseURL = baseURL;
        this.token = localStorage.getItem('token');
    }

    // Método genérico para hacer peticiones
    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const headers = {
            'Content-Type': 'application/json',
            ...options.headers
        };

        // Agregar token si existe
        if (this.token) {
            headers['Authorization'] = `Bearer ${this.token}`;
        }

        try {
            const response = await fetch(url, {
                ...options,
                headers
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Error en la petición');
            }

            return data;
        } catch (error) {
            console.error(`Error en petición a ${endpoint}:`, error);
            throw error;
        }
    }

    // ===== PRODUCTOS =====
    async getProductos() {
        return this.request('/productos');
    }

    async getProductoById(id) {
        return this.request(`/productos/${id}`);
    }

    async getProductosDestacados() {
        return this.request('/productos/destacados');
    }

    async getProductosByCategoria(categoriaId) {
        return this.request(`/productos/categoria/${categoriaId}`);
    }

    async searchProductos(query) {
        return this.request(`/productos/search?q=${encodeURIComponent(query)}`);
    }

    async createProducto(productoData) {
        return this.request('/productos', {
            method: 'POST',
            body: JSON.stringify(productoData)
        });
    }

    async updateProducto(id, productoData) {
        return this.request(`/productos/${id}`, {
            method: 'PUT',
            body: JSON.stringify(productoData)
        });
    }

    async deleteProducto(id) {
        return this.request(`/productos/${id}`, {
            method: 'DELETE'
        });
    }

    // ===== CATEGORÍAS =====
    async getCategorias() {
        return this.request('/categorias');
    }

    async getCategoriaById(id) {
        return this.request(`/categorias/${id}`);
    }

    async getCategoriaWithProducts(id) {
        return this.request(`/categorias/${id}/productos`);
    }

    async createCategoria(categoriaData) {
        return this.request('/categorias', {
            method: 'POST',
            body: JSON.stringify(categoriaData)
        });
    }

    async updateCategoria(id, categoriaData) {
        return this.request(`/categorias/${id}`, {
            method: 'PUT',
            body: JSON.stringify(categoriaData)
        });
    }

    async deleteCategoria(id) {
        return this.request(`/categorias/${id}`, {
            method: 'DELETE'
        });
    }

    // ===== AUTENTICACIÓN =====
    async register(userData) {
        const response = await this.request('/auth/register', {
            method: 'POST',
            body: JSON.stringify(userData)
        });
        
        if (response.success && response.data.token) {
            this.token = response.data.token;
            localStorage.setItem('token', this.token);
            localStorage.setItem('user', JSON.stringify(response.data.user));
        }
        
        return response;
    }

    async login(credentials) {
        const response = await this.request('/auth/login', {
            method: 'POST',
            body: JSON.stringify(credentials)
        });
        
        if (response.success && response.data.token) {
            this.token = response.data.token;
            localStorage.setItem('token', this.token);
            localStorage.setItem('user', JSON.stringify(response.data.user));
        }
        
        return response;
    }

    async logout() {
        const response = await this.request('/auth/logout', {
            method: 'POST'
        });
        
        this.token = null;
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        
        return response;
    }

    async getProfile() {
        return this.request('/auth/profile');
    }

    // ===== PEDIDOS =====
    async getPedidos() {
        return this.request('/pedidos');
    }

    async getPedidoById(id) {
        return this.request(`/pedidos/${id}`);
    }

    async getPedidosByCliente(clienteId) {
        return this.request(`/pedidos/cliente/${clienteId}`);
    }

    async createPedido(pedidoData) {
        return this.request('/pedidos', {
            method: 'POST',
            body: JSON.stringify(pedidoData)
        });
    }

    async updatePedidoEstado(id, estado) {
        return this.request(`/pedidos/${id}/estado`, {
            method: 'PUT',
            body: JSON.stringify({ estado })
        });
    }

    async cancelPedido(id) {
        return this.request(`/pedidos/${id}`, {
            method: 'DELETE'
        });
    }

    async getEstadisticas() {
        return this.request('/pedidos/estadisticas');
    }

    // ===== ADMIN =====
    async getDashboardStats() {
        return this.request('/admin/dashboard/stats');
    }

    async getUsuarios() {
        return this.request('/admin/usuarios');
    }

    async updateUsuarioRol(id, rol) {
        return this.request(`/admin/usuarios/${id}/rol`, {
            method: 'PUT',
            body: JSON.stringify({ rol })
        });
    }

    async deleteUsuario(id) {
        return this.request(`/admin/usuarios/${id}`, {
            method: 'DELETE'
        });
    }

    async getTodosPedidos() {
        return this.request('/admin/pedidos');
    }

    async updateEstadoPedido(id, estado) {
        return this.request(`/admin/pedidos/${id}/estado`, {
            method: 'PUT',
            body: JSON.stringify({ estado })
        });
    }

    async getReporteVentas(fechaInicio, fechaFin) {
        let url = '/admin/reportes/ventas';
        if (fechaInicio && fechaFin) {
            url += `?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
        }
        return this.request(url);
    }

    // ===== UTILIDADES =====
    isAuthenticated() {
        return !!this.token;
    }

    isAdmin() {
        const user = this.getUser();
        return user && user.rol === 'admin';
    }

    getUser() {
        const user = localStorage.getItem('user');
        return user ? JSON.parse(user) : null;
    }

    setToken(token) {
        this.token = token;
        localStorage.setItem('token', token);
    }

    clearAuth() {
        this.token = null;
        localStorage.removeItem('token');
        localStorage.removeItem('user');
    }
}

// Exportar instancia de la API
const api = new API(API_URL);
