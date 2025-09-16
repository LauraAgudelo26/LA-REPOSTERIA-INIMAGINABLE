# Placeres Ocultos - Sistema Completo

## 📋 Descripción del Proyecto

**Placeres Ocultos** es un sistema web modular desarrollado en PHP que maneja un catálogo de productos con funcionalidades de autenticación de usuarios y secciones especializadas. El proyecto utiliza una arquitectura monolítica modular con separación clara de responsabilidades.

## 🏗️ Arquitectura del Sistema

### Estructura de Directorios

```
proyecto/
├── categorias/
│   └── api/
│       └── api_categorias.php        # API para gestión de categorías
├── config/
│   ├── config_db.php                 # Configuración de base de datos
│   ├── config_db_new.php            # Configuración alternativa
│   ├── functions.php                 # Funciones auxiliares
│   └── logs/                         # Logs del sistema
├── database/
│   ├── BASE_DE_DATOS_FINAL.sql       # Script principal de BD
│   └── [otros scripts SQL]
├── productos/
│   ├── api/
│   │   ├── api_productos.php         # API principal de productos
│   │   └── api_productos_especiales.php # API para productos especiales
│   └── controllers/
│       └── controlador_productos.php # Controlador de productos
├── public/
│   ├── css/
│   │   └── style.css                 # Estilos principales
│   ├── img/                          # Imágenes de productos
│   ├── js/                           # Scripts JavaScript
│   └── views/
│       ├── index.html               # Página principal
│       └── placeres_ocultos.html    # Sección especial
├── usuarios/
│   ├── api/
│   │   └── api_usuario_info.php      # API de información de usuario
│   ├── controllers/                  # Controladores de usuario
│   └── views/                        # Vistas de usuario
├── utils/                            # Utilidades y scripts auxiliares
└── index.php                        # Punto de entrada principal
```

### Características Técnicas

- **Lenguaje Backend**: PHP
- **Base de Datos**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Servidor**: Apache (Laragon)
- **Arquitectura**: Monolítica Modular
- **APIs**: RESTful JSON

## 🚀 Instalación y Configuración

### Prerrequisitos

1. **Laragon** o stack similar (Apache + PHP + MySQL)
2. **PHP 7.4+**
3. **MySQL 5.7+**
4. **Navegador web moderno**

### Pasos de Instalación

#### 1. Configuración del Servidor

```bash
# Ubicar el proyecto en el directorio web de Laragon
C:\laragon\www\proyecto\
```

#### 2. Configuración de Base de Datos

1. Abrir **HeidiSQL** o cliente MySQL preferido
2. Crear nueva base de datos:
   ```sql
   CREATE DATABASE placeres_ocultos;
   ```

3. Ejecutar el script principal:
   ```bash
   # Importar desde HeidiSQL o línea de comandos
   mysql -u root -p placeres_ocultos < database/BASE_DE_DATOS_FINAL.sql
   ```

#### 3. Configuración de Conexión

Editar `config/config_db.php`:

```php
<?php
$host = 'localhost';
$dbname = 'placeres_ocultos';
$username = 'root';
$password = '';  // Contraseña de MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    error_log("Error de conexión: " . $e->getMessage());
    die("Error de conexión a la base de datos");
}
?>
```

#### 4. Configuración de URLs

El sistema utiliza URLs base relativas. Verificar que Laragon esté ejecutándose en:
- **URL Local**: `http://localhost/proyecto/`
- **Puerto**: 80 (por defecto)

## 📊 Base de Datos

### Estructura de Tablas

#### Tabla `producto`
```sql
CREATE TABLE producto (
    id_producto INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    imagen VARCHAR(255),
    id_categoria INT,
    FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria)
);
```

#### Tabla `categoria`
```sql
CREATE TABLE categoria (
    id_categoria INT PRIMARY KEY AUTO_INCREMENT,
    nombre_categoria VARCHAR(255) NOT NULL,
    descripcion TEXT
);
```

#### Tabla `usuario`
```sql
CREATE TABLE usuario (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nombre_usuario VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    rol VARCHAR(50) DEFAULT 'usuario',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Datos de Prueba

El sistema incluye:
- **9 productos regulares** (categorías 1, 3, 4, 5)
- **7 productos especiales** (categoría 2 - Placeres Ocultos)
- **5 categorías** predefinidas
- **Usuario de prueba**: admin/admin123

## 🔌 APIs Disponibles

### API de Productos

#### Productos Regulares
```
GET /productos/api/api_productos.php
```

**Respuesta Ejemplo**:
```json
{
    "success": true,
    "productos": [
        {
            "id_producto": "1",
            "nombre": "Banano Fresco",
            "descripcion": "Banano maduro y delicioso",
            "precio": "1500.00",
            "imagen": "banano.jpg",
            "categoria": "Frutas"
        }
    ]
}
```

#### Productos Especiales
```
GET /productos/api/api_productos_especiales.php
```

### API de Categorías
```
GET /categorias/api/api_categorias.php
```

### API de Usuario
```
GET /usuarios/api/api_usuario_info.php
```

## 🎯 Funcionalidades Principales

### 1. Catálogo de Productos
- **Visualización**: Grid responsivo de productos
- **Filtrado**: Por categoría en tiempo real
- **Detalles**: Nombre, descripción, precio e imagen
- **Imágenes**: Optimizadas y alojadas localmente

### 2. Sistema de Autenticación
- **Registro**: Formulario con validación
- **Login**: Autenticación segura con sessions
- **Sesiones**: Manejo automático de estado
- **Logout**: Limpieza completa de sesión

### 3. Sección Placeres Ocultos
- **Acceso Restringido**: Verificación de edad
- **Productos Especiales**: Catálogo exclusivo
- **Temática Especializada**: Interfaz adaptada
- **Filtrado Avanzado**: Por categorías especiales

### 4. Panel de Administración
- **Gestión de Productos**: CRUD completo
- **Gestión de Usuarios**: Control de acceso
- **Estadísticas**: Visualización de datos
- **Logs**: Monitoreo del sistema

## 🛠️ Uso del Sistema

### Acceso Inicial

1. **Abrir navegador** y ir a: `http://localhost/proyecto/`
2. **Página principal** muestra catálogo de productos
3. **Botón "Placeres Ocultos"** para sección especial

### Registro de Usuario

1. Hacer clic en **"Registrarse"**
2. Completar formulario:
   - Nombre de usuario (único)
   - Email (válido)
   - Contraseña (mínimo 6 caracteres)
3. Confirmar registro

### Navegación

- **Inicio**: Catálogo principal con filtros
- **Categorías**: Filtrado dinámico
- **Placeres Ocultos**: Sección especial (18+)
- **Perfil**: Información de usuario

## 🔧 Desarrollo y Mantenimiento

### Estructura de Controladores

Los controladores siguen el patrón:
```php
<?php
session_start();
require_once '../config/config_db.php';

// Lógica del controlador
// Procesamiento de datos
// Redirección o respuesta
?>
```

### Logs del Sistema

Los logs se almacenan en:
- `config/logs/` - Logs de configuración
- `logs/` - Logs principales del sistema

### Debugging

Para activar modo debug, editar `config/functions.php`:
```php
define('DEBUG_MODE', true);
```

### Backup de Base de Datos

Comando para backup:
```bash
mysqldump -u root -p placeres_ocultos > backup_$(date +%Y%m%d).sql
```

## 🔒 Seguridad

### Medidas Implementadas

- **Sanitización**: Todas las entradas de usuario
- **Prepared Statements**: Prevención de SQL injection
- **Hash de Contraseñas**: bcrypt para almacenamiento
- **Validación**: Frontend y backend
- **Sessions**: Manejo seguro de sesiones

### Configuración de Seguridad

En `config/functions.php`:
```php
// Configuración de sesiones seguras
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
```

## 🚨 Solución de Problemas

### Problemas Comunes

#### Error de Conexión a BD
```
Error: Could not connect to database
```
**Solución**: Verificar credenciales en `config/config_db.php`

#### Imágenes no cargan
```
Error 404 en imágenes
```
**Solución**: Verificar rutas en `public/img/` y permisos

#### API devuelve HTML en lugar de JSON
```
SyntaxError: Unexpected token '<'
```
**Solución**: Verificar errores PHP en logs y configuración

#### Sesiones no funcionan
```
Usuario no mantiene sesión
```
**Solución**: Verificar configuración de PHP sessions

### Comandos de Verificación

```bash
# Verificar estado del servidor
curl http://localhost/proyecto/productos/api/api_productos.php

# Probar conexión a BD
php utils/test_db_connection.php

# Verificar permisos
ls -la public/img/

# Ver logs de errores
tail -f config/logs/sistema_*.log
```

## 📈 Próximas Mejoras

### Funcionalidades Planificadas

1. **Carrito de Compras**
   - Agregar productos al carrito
   - Gestión de cantidades
   - Proceso de checkout

2. **Panel de Administración Avanzado**
   - Dashboard con métricas
   - Gestión de inventario
   - Reportes de ventas

3. **Optimizaciones**
   - Cache de consultas
   - Compresión de imágenes
   - CDN para assets

4. **Seguridad Avanzada**
   - 2FA para administradores
   - Rate limiting en APIs
   - Auditoría de acciones

## 👥 Contribución

### Estructura para Nuevas Funcionalidades

1. **APIs**: Crear en `[modulo]/api/`
2. **Controladores**: Ubicar en `[modulo]/controllers/`
3. **Vistas**: Colocar en `public/views/`
4. **Estilos**: Añadir a `public/css/`

### Estándares de Código

- **PSR-4**: Para autoloading de clases
- **Camel Case**: Para funciones y variables
- **Snake Case**: Para nombres de BD
- **Comentarios**: Documentación clara

## 🎉 Estado Actual

### ✅ Funcionalidades Completadas

- [x] Arquitectura modular implementada
- [x] Base de datos estructurada y poblada
- [x] APIs REST funcionales
- [x] Sistema de autenticación completo
- [x] Interfaz de usuario responsive
- [x] Sección Placeres Ocultos implementada
- [x] Sistema de filtrado por categorías
- [x] Gestión de imágenes optimizada
- [x] Logs y debugging implementados
- [x] Documentación unificada

### 🔄 En Desarrollo

- [ ] Carrito de compras
- [ ] Panel de administración avanzado
- [ ] Sistema de pedidos
- [ ] Notificaciones push

## 📞 Soporte

Para problemas técnicos o consultas:

1. **Revisar logs** en `config/logs/` y `logs/`
2. **Verificar configuración** en `config/`
3. **Probar APIs** directamente con curl
4. **Consultar esta documentación**

---

**Proyecto Placeres Ocultos** - Sistema Web Modular PHP
*Versión: 1.0 - Documentación actualizada: 2024*