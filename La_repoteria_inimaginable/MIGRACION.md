# 📦 Migración del Frontend PHP a Node.js

## ✅ Completado

### Estructura del Proyecto
```
La_repoteria_inimaginable/
├── public/                  # Archivos estáticos (frontend)
│   ├── css/
│   │   └── style.css       # Estilos del frontend
│   ├── js/
│   │   ├── api.js          # Cliente API REST
│   │   └── app.js          # Lógica principal del frontend
│   ├── img/                # Imágenes de productos
│   └── index.html          # Página principal
├── src/                    # Backend (Node.js + Express)
│   ├── config/
│   │   └── db.js
│   ├── controllers/
│   │   ├── productos_controller.js
│   │   ├── categorias_controller.js
│   │   ├── pedidos_controller.js
│   │   ├── clientes_controller.js
│   │   └── auth_controller.js
│   ├── models/
│   │   ├── producto_model.js
│   │   ├── categoria_model.js
│   │   ├── pedido_model.js
│   │   └── cliente_model.js
│   ├── routes/
│   │   ├── producto_routes.js
│   │   ├── categoria_routes.js
│   │   ├── pedido_routes.js
│   │   └── auth_routes.js
│   └── app.js
├── server.js
├── package.json
└── .env
```

## 🔧 Configuración

### 1. Archivo `.env`
Crea un archivo `.env` en la raíz del proyecto con:

```env
# Base de datos
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=tu_password
DB_NAME=la_repoteria_db
DB_PORT=3306

# Servidor
PORT=3000

# JWT
JWT_SECRET=tu_clave_secreta_super_segura_cambiar_en_produccion
JWT_EXPIRES_IN=24h
```

### 2. Copiar Imágenes
Copia todas las imágenes del proyecto PHP al nuevo proyecto Node.js:

**PowerShell:**
```powershell
Copy-Item "c:\Users\SENA\Documents\GitHub\LA-REPOSTERIA-INIMAGINABLE\proyecto\public\img\*" -Destination "c:\Users\SENA\Documents\GitHub\LA-REPOSTERIA-INIMAGINABLE\La_repoteria_inimaginable\public\img\" -Recurse
```

**CMD:**
```cmd
xcopy "c:\Users\SENA\Documents\GitHub\LA-REPOSTERIA-INIMAGINABLE\proyecto\public\img\*" "c:\Users\SENA\Documents\GitHub\LA-REPOSTERIA-INIMAGINABLE\La_repoteria_inimaginable\public\img\" /E /I /Y
```

### 3. Instalar Dependencias
Si aún no las has instalado:

```bash
npm install
```

### 4. Iniciar el Servidor
```bash
npm run dev
```

## 🌐 Endpoints de la API

### Productos
- `GET /api/productos` - Todos los productos
- `GET /api/productos/destacados` - Productos destacados
- `GET /api/productos/search?q=termo` - Buscar productos
- `GET /api/productos/categoria/:id` - Productos por categoría
- `GET /api/productos/:id` - Un producto
- `POST /api/productos` - Crear producto (admin)
- `PUT /api/productos/:id` - Actualizar producto (admin)
- `DELETE /api/productos/:id` - Eliminar producto (admin)

### Categorías
- `GET /api/categorias` - Todas las categorías
- `GET /api/categorias/:id` - Una categoría
- `GET /api/categorias/:id/productos` - Categoría con productos
- `POST /api/categorias` - Crear categoría (admin)
- `PUT /api/categorias/:id` - Actualizar categoría (admin)
- `DELETE /api/categorias/:id` - Eliminar categoría (admin)

### Autenticación
- `POST /api/auth/register` - Registrar usuario
- `POST /api/auth/login` - Iniciar sesión
- `POST /api/auth/logout` - Cerrar sesión
- `GET /api/auth/profile` - Obtener perfil (requiere token)

### Pedidos
- `GET /api/pedidos` - Todos los pedidos (admin)
- `GET /api/pedidos/:id` - Un pedido
- `GET /api/pedidos/cliente/:cliente_id` - Pedidos de un cliente
- `GET /api/pedidos/estadisticas` - Estadísticas (admin)
- `POST /api/pedidos` - Crear pedido
- `PUT /api/pedidos/:id/estado` - Actualizar estado (admin)
- `DELETE /api/pedidos/:id` - Cancelar pedido

## 🔐 Autenticación

El sistema usa **JWT (JSON Web Tokens)**:

### Registro
```javascript
POST /api/auth/register
Content-Type: application/json

{
  "nombre": "Laura Agudelo",
  "email": "laura@example.com",
  "password": "password123",
  "telefono": "300 123 4567",
  "direccion": "Calle 123 #45-67"
}
```

### Login
```javascript
POST /api/auth/login
Content-Type: application/json

{
  "email": "laura@example.com",
  "password": "password123"
}
```

### Usar el Token
```javascript
GET /api/auth/profile
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

## 📊 Frontend

### Características Implementadas
✅ Pantalla de carga animada
✅ Sistema de autenticación (login/registro)
✅ Filtrado por categorías
✅ Productos destacados
✅ Búsqueda de productos
✅ Carrito de compras
✅ Gestión de pedidos
✅ Perfil de usuario
✅ Diseño responsive

### Archivos del Frontend
- **`public/index.html`** - Página principal
- **`public/css/style.css`** - Estilos personalizados
- **`public/js/api.js`** - Cliente API REST (maneja todas las peticiones)
- **`public/js/app.js`** - Lógica de la aplicación (renderizado, eventos, etc.)

## 🚀 Acceso

Una vez iniciado el servidor:

- **Frontend:** http://localhost:3000
- **API:** http://localhost:3000/api
- **API Info:** http://localhost:3000/api (GET)

## 🔄 Diferencias con PHP

| Aspecto | PHP (Anterior) | Node.js (Nuevo) |
|---------|---------------|-----------------|
| **API** | Múltiples archivos PHP | API REST unificada |
| **Sesiones** | PHP Sessions | JWT Tokens |
| **Rutas** | Archivos PHP directos | Express Router |
| **Base de datos** | mysqli | mysql2 con Promises |
| **Frontend** | HTML embebido en PHP | HTML estático + JS |
| **Autenticación** | Session cookies | Bearer tokens |

## 📝 Próximos Pasos

1. ✅ Copiar imágenes del proyecto PHP
2. ✅ Probar todos los endpoints de la API
3. ⏳ Crear página de login (`login.html`)
4. ⏳ Crear página de registro (`register.html`)
5. ⏳ Crear página de productos especiales (`placeres_ocultos.html`)
6. ⏳ Implementar carrito de compras
7. ⏳ Crear dashboard de administración

## 🛠️ Comandos Útiles

```bash
# Desarrollo
npm run dev

# Producción
npm start

# Ver logs
npm run dev -- --verbose

# Instalar nueva dependencia
npm install nombre-paquete
```

## 📦 Dependencias

```json
{
  "dependencies": {
    "express": "^5.1.0",
    "mysql2": "^3.15.2",
    "dotenv": "^16.4.7",
    "bcrypt": "^5.1.1",
    "jsonwebtoken": "^9.0.2",
    "cors": "^2.8.5"
  },
  "devDependencies": {
    "nodemon": "^3.1.9",
    "morgan": "^1.10.0"
  }
}
```

## 🎉 ¡Listo para usar!

El frontend está completamente migrado y listo para consumir la API de Node.js. Solo necesitas:

1. Copiar las imágenes
2. Configurar el `.env`
3. Iniciar el servidor con `npm run dev`
4. Abrir http://localhost:3000

¡Disfruta tu nueva aplicación! 🎂
