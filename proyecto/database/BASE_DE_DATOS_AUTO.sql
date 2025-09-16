-- Base de datos FINAL con selección automática de BD
-- EJECUTAR EN PHPMYADMIN SIN SELECCIONAR BASE DE DATOS

-- PASO 1: Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS placeres_ocultos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- PASO 2: Seleccionar la base de datos
USE placeres_ocultos;

-- PASO 3: Eliminar tablas si existen (para empezar limpio)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS pedido_detalle;
DROP TABLE IF EXISTS pedido;
DROP TABLE IF EXISTS producto;
DROP TABLE IF EXISTS categoria;
DROP TABLE IF EXISTS cliente;
SET FOREIGN_KEY_CHECKS = 1;

-- Crear tabla de clientes con roles y campos corregidos
CREATE TABLE cliente (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    nombre_usuario VARCHAR(50) UNIQUE,
    email VARCHAR(150) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    direccion TEXT,
    tipo_documento ENUM('cc', 'ti', 'dni', 'pasaporte', 'otro') DEFAULT 'cc',
    numero_documento VARCHAR(50),
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'cliente') DEFAULT 'cliente',
    activo TINYINT(1) DEFAULT 1,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_usuario (nombre_usuario),
    INDEX idx_rol (rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla de categorías
CREATE TABLE categoria (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    imagen VARCHAR(255),
    activa TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    orden_mostrar INT(11) DEFAULT 0,
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla de productos mejorada
CREATE TABLE producto (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    categoria_id INT(11),
    imagen VARCHAR(255),
    stock INT(11) DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    destacado TINYINT(1) DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre),
    INDEX idx_precio (precio),
    INDEX idx_categoria (categoria_id),
    INDEX idx_activo (activo),
    INDEX idx_destacado (destacado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla de pedidos
CREATE TABLE pedido (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT(11) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    estado ENUM('pendiente', 'confirmado', 'en_preparacion', 'listo', 'entregado', 'cancelado') DEFAULT 'pendiente',
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_entrega TIMESTAMP NULL,
    direccion_entrega TEXT,
    telefono_entrega VARCHAR(20),
    notas TEXT,
    INDEX idx_cliente (cliente_id),
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha_pedido)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla de detalles de pedido
CREATE TABLE pedido_detalle (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT(11) NOT NULL,
    producto_id INT(11) NOT NULL,
    cantidad INT(11) NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    INDEX idx_pedido (pedido_id),
    INDEX idx_producto (producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar las relaciones de clave foránea
ALTER TABLE producto ADD CONSTRAINT fk_producto_categoria FOREIGN KEY (categoria_id) REFERENCES categoria(id) ON DELETE SET NULL;
ALTER TABLE pedido ADD CONSTRAINT fk_pedido_cliente FOREIGN KEY (cliente_id) REFERENCES cliente(id) ON DELETE CASCADE;
ALTER TABLE pedido_detalle ADD CONSTRAINT fk_detalle_pedido FOREIGN KEY (pedido_id) REFERENCES pedido(id) ON DELETE CASCADE;
ALTER TABLE pedido_detalle ADD CONSTRAINT fk_detalle_producto FOREIGN KEY (producto_id) REFERENCES producto(id) ON DELETE CASCADE;

-- Insertar usuarios por defecto
-- Usuario Admin - Email: admin@placeresocultos.com - Contraseña: admin123
INSERT INTO cliente (nombre, apellido, nombre_usuario, email, telefono, direccion, password, rol) VALUES
('Administrador', 'Sistema', 'admin', 'admin@placeresocultos.com', '1234567890', 'Dirección Admin', '$2y$10$PJsNoIa2aw3VCB.q54/ykeMw/KvrYhTKD1ePJKt/zcMb/CL3jrwES', 'admin');

-- Usuario Cliente - Email: cliente@test.com - Contraseña: cliente123  
INSERT INTO cliente (nombre, apellido, nombre_usuario, email, telefono, direccion, password, rol) VALUES
('Cliente', 'Prueba', 'cliente', 'cliente@test.com', '0987654321', 'Dirección Cliente', '$2y$10$0Bi.w9jqq0ylkmsxzMdSlO0SFi8Gpc1eXqicY2JHEQUCSLYrL022S', 'cliente');

-- Insertar categorías de ejemplo
INSERT INTO categoria (nombre, descripcion, orden_mostrar) VALUES
('Postres Clásicos', 'Deliciosos postres tradicionales', 1),
('Tortas Especiales', 'Tortas únicas y creativas', 2),
('Bebidas', 'Bebidas refrescantes y calientes', 3),
('Frutas Frescas', 'Frutas de temporada y preparaciones', 4),
('Dulces Artesanales', 'Dulces hechos en casa', 5);

-- Insertar productos de ejemplo
INSERT INTO producto (nombre, descripcion, precio, categoria_id, imagen, stock, destacado) VALUES
('Cheesecake de Fresa', 'Delicioso cheesecake con fresas frescas', 25000.00, 1, 'Cheesecake de Fresa Delicioso.jpg', 10, 1),
('Torta de Chocolate', 'Rica torta de chocolate con cobertura', 30000.00, 2, 'funny birthday cake.jpg', 8, 1),
('Cappuccino Especial', 'Cappuccino cremoso con arte latte', 8000.00, 3, 'Cappuccino .jpg', 50, 0),
('Fresas con Crema', 'Fresas frescas con crema batida', 12000.00, 4, 'fresas con crema.jpg', 20, 1),
('Churros Caseros', 'Churros recién hechos con azúcar', 6000.00, 5, 'churros.jpg', 30, 0),
('Mini Tarta de Frutas', 'Pequeña tarta con frutas mixtas', 15000.00, 1, 'mini tarta de frutas.jpg', 15, 0),
('Postre de Oreo', 'Cremoso postre con galletas Oreo', 18000.00, 1, 'postre de oreo.jfif', 12, 1),
('Galletas con Chips', 'Galletas caseras con chips de chocolate', 5000.00, 5, 'galletas con chips de chocolate.jpg', 40, 0),
('Rollos de Canela', 'Suaves rollos de canela recién horneados', 8000.00, 5, 'rollos de canela.jpg', 25, 0),
('Postre de Limón', 'Refrescante postre de limón', 14000.00, 1, 'postre de limon.jpg', 18, 0);

-- Verificar que todo se creó correctamente
SELECT 'USUARIOS CREADOS:' as mensaje;
SELECT id, nombre, apellido, email, rol FROM cliente;

SELECT 'CATEGORÍAS CREADAS:' as mensaje;
SELECT id, nombre, descripcion FROM categoria;

SELECT 'PRODUCTOS CREADOS:' as mensaje;
SELECT id, nombre, precio, categoria_id FROM producto LIMIT 5;

SELECT 'BASE DE DATOS CREADA EXITOSAMENTE' as resultado;
