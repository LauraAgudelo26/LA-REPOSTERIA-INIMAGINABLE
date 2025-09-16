-- Base de datos para el sistema de productos y pedidos
-- Creado: 2025-09-01
-- Compatible con MySQL/MariaDB

-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS tienda_productos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tienda_productos;

-- Tabla Categoria
CREATE TABLE Categoria (
    idCategoria INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(45) NOT NULL,
    Descripcion TEXT
);

-- Tabla Proveedor
CREATE TABLE Proveedor (
    idProveedor INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(45) NOT NULL,
    Direccion VARCHAR(45),
    Telefono VARCHAR(45),
    Correo_Electronico VARCHAR(45)
);

-- Tabla Producto
CREATE TABLE Producto (
    idProducto INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(45) NOT NULL,
    Descripcion TEXT,
    Precio DECIMAL(10,2) NOT NULL,
    Stock INT NOT NULL DEFAULT 0,
    idCategoria INT NOT NULL,
    FOREIGN KEY (idCategoria) REFERENCES Categoria(idCategoria) ON DELETE CASCADE
);

-- Tabla Cliente
CREATE TABLE Cliente (
    idCliente INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(45) NOT NULL,
    Apellido VARCHAR(45) NOT NULL,
    Direccion VARCHAR(45),
    Telefono VARCHAR(45),
    Correo_Electronico VARCHAR(45) UNIQUE,
    Password_Hash VARCHAR(255),
    Reset_Token VARCHAR(255) NULL,
    Token_Expiry DATETIME NULL,
    Fecha_Registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla Pedido
CREATE TABLE Pedido (
    idPedido INT AUTO_INCREMENT PRIMARY KEY,
    Fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    Total DECIMAL(10,2) NOT NULL,
    Id_cliente INT NOT NULL,
    Estado ENUM('pendiente', 'procesando', 'enviado', 'entregado', 'cancelado') DEFAULT 'pendiente',
    FOREIGN KEY (Id_cliente) REFERENCES Cliente(idCliente) ON DELETE CASCADE
);

-- Tabla DetallePedido
CREATE TABLE DetallePedido (
    idDetallePedido INT AUTO_INCREMENT PRIMARY KEY,
    Id_pedido INT NOT NULL,
    Id_producto INT NOT NULL,
    Cantidad INT NOT NULL,
    Precio DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (Id_pedido) REFERENCES Pedido(idPedido) ON DELETE CASCADE,
    FOREIGN KEY (Id_producto) REFERENCES Producto(idProducto) ON DELETE CASCADE
);

-- Tabla Compra (para proveedores)
CREATE TABLE Compra (
    idCompra INT AUTO_INCREMENT PRIMARY KEY,
    Fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    Id_proveedor INT NOT NULL,
    Total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (Id_proveedor) REFERENCES Proveedor(idProveedor) ON DELETE CASCADE
);

-- Tabla DetalleCompra
CREATE TABLE DetalleCompra (
    idDetalleCompra INT AUTO_INCREMENT PRIMARY KEY,
    Id_detalle INT NOT NULL,
    Id_compra INT NOT NULL,
    Id_Producto INT NOT NULL,
    Cantidad INT NOT NULL,
    Precio DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (Id_compra) REFERENCES Compra(idCompra) ON DELETE CASCADE,
    FOREIGN KEY (Id_Producto) REFERENCES Producto(idProducto) ON DELETE CASCADE
);

-- Tabla para relación Compra-Proveedor (muchos a muchos)
CREATE TABLE Compra_idProveedor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idCompra INT NOT NULL,
    idProveedor INT NOT NULL,
    FOREIGN KEY (idCompra) REFERENCES Compra(idCompra) ON DELETE CASCADE,
    FOREIGN KEY (idProveedor) REFERENCES Proveedor(idProveedor) ON DELETE CASCADE
);

-- Insertar datos de ejemplo

-- Categorías
INSERT INTO Categoria (Nombre, Descripcion) VALUES
('Postres', 'Deliciosos postres y dulces'),
('Bebidas', 'Bebidas refrescantes y calientes'),
('Frutas', 'Frutas frescas y naturales'),
('Pasteles', 'Pasteles especiales y temáticos'),
('Galletas', 'Galletas artesanales');

-- Proveedores
INSERT INTO Proveedor (Nombre, Direccion, Telefono, Correo_Electronico) VALUES
('Dulces El Paraíso', 'Calle 123 #45-67', '555-0001', 'ventas@dulceselparaiso.com'),
('Frutas Frescas SA', 'Avenida 80 #12-34', '555-0002', 'pedidos@frutasfrescas.com'),
('Bebidas Premium', 'Carrera 15 #67-89', '555-0003', 'info@bebidaspremium.com');

-- Productos (basados en las imágenes de tu carpeta)
INSERT INTO Producto (Nombre, Descripcion, Precio, Stock, idCategoria) VALUES
('Cheesecake de Fresa', 'Delicioso cheesecake con fresas frescas', 25000.00, 10, 1),
('Churros', 'Churros tradicionales con azúcar y canela', 8000.00, 25, 1),
('Donas Glaseadas', 'Donas suaves con glaseado de colores', 5000.00, 30, 1),
('Fresas con Crema', 'Fresas frescas con crema batida', 12000.00, 15, 3),
('Cappuccino', 'Café cappuccino aromático', 7000.00, 50, 2),
('Postre de Gelatina', 'Gelatina de frutas en capas', 6000.00, 20, 1),
('Galletas con Chips', 'Galletas caseras con chips de chocolate', 4000.00, 40, 5),
('Torta Despedida Soltera', 'Torta temática para despedida de soltera', 80000.00, 5, 4),
('Mini Tarta de Frutas', 'Pequeña tarta decorada con frutas', 15000.00, 12, 4),
('Rollos de Canela', 'Rollos dulces con canela y glaseado', 9000.00, 18, 1);

-- Clientes de ejemplo
INSERT INTO Cliente (Nombre, Apellido, Direccion, Telefono, Correo_Electronico, Password_Hash) VALUES
('María', 'González', 'Calle 45 #12-34', '300-123-4567', 'maria@email.com', '$2y$10$example_hash_1'),
('Carlos', 'Rodríguez', 'Avenida 68 #23-45', '301-234-5678', 'carlos@email.com', '$2y$10$example_hash_2'),
('Ana', 'López', 'Carrera 25 #34-56', '302-345-6789', 'ana@email.com', '$2y$10$example_hash_3');

-- Pedidos de ejemplo
INSERT INTO Pedido (Total, Id_cliente) VALUES
(45000.00, 1),
(32000.00, 2),
(28000.00, 3);

-- Detalles de pedidos
INSERT INTO DetallePedido (Id_pedido, Id_producto, Cantidad, Precio) VALUES
(1, 1, 1, 25000.00),  -- Cheesecake de Fresa
(1, 3, 4, 5000.00),   -- Donas Glaseadas
(2, 4, 2, 12000.00),  -- Fresas con Crema
(2, 5, 1, 7000.00),   -- Cappuccino
(3, 7, 5, 4000.00),   -- Galletas con Chips
(3, 6, 1, 6000.00);   -- Postre de Gelatina

-- Compras de ejemplo
INSERT INTO Compra (Id_proveedor, Total) VALUES
(1, 150000.00),
(2, 80000.00),
(3, 45000.00);

-- Detalles de compras
INSERT INTO DetalleCompra (Id_detalle, Id_compra, Id_Producto, Cantidad, Precio) VALUES
(1, 1, 1, 10, 15000.00),  -- Compra Cheesecake
(2, 1, 3, 20, 3000.00),   -- Compra Donas
(3, 2, 4, 15, 8000.00),   -- Compra Fresas
(4, 3, 5, 30, 4000.00);   -- Compra Cappuccino

-- Crear índices para mejorar rendimiento
CREATE INDEX idx_producto_categoria ON Producto(idCategoria);
CREATE INDEX idx_pedido_cliente ON Pedido(Id_cliente);
CREATE INDEX idx_detalle_pedido ON DetallePedido(Id_pedido);
CREATE INDEX idx_detalle_producto ON DetallePedido(Id_producto);
CREATE INDEX idx_cliente_email ON Cliente(Correo_Electronico);
CREATE INDEX idx_cliente_token ON Cliente(Reset_Token);

-- Crear vistas útiles
CREATE VIEW vista_productos_categoria AS
SELECT 
    p.idProducto,
    p.Nombre as Producto,
    p.Descripcion,
    p.Precio,
    p.Stock,
    c.Nombre as Categoria
FROM Producto p
JOIN Categoria c ON p.idCategoria = c.idCategoria;

CREATE VIEW vista_pedidos_completos AS
SELECT 
    p.idPedido,
    p.Fecha,
    p.Total,
    p.Estado,
    CONCAT(cl.Nombre, ' ', cl.Apellido) as Cliente,
    cl.Correo_Electronico
FROM Pedido p
JOIN Cliente cl ON p.Id_cliente = cl.idCliente;

-- Procedimientos almacenados útiles

DELIMITER //

-- Procedimiento para registrar nuevo cliente
CREATE PROCEDURE RegistrarCliente(
    IN p_nombre VARCHAR(45),
    IN p_apellido VARCHAR(45),
    IN p_direccion VARCHAR(45),
    IN p_telefono VARCHAR(45),
    IN p_email VARCHAR(45),
    IN p_password VARCHAR(255)
)
BEGIN
    INSERT INTO Cliente (Nombre, Apellido, Direccion, Telefono, Correo_Electronico, Password_Hash)
    VALUES (p_nombre, p_apellido, p_direccion, p_telefono, p_email, p_password);
END //

-- Procedimiento para generar token de recuperación
CREATE PROCEDURE GenerarTokenRecuperacion(
    IN p_email VARCHAR(45),
    IN p_token VARCHAR(255)
)
BEGIN
    UPDATE Cliente 
    SET Reset_Token = p_token, 
        Token_Expiry = DATE_ADD(NOW(), INTERVAL 24 HOUR)
    WHERE Correo_Electronico = p_email;
END //

-- Procedimiento para verificar token
CREATE PROCEDURE VerificarToken(
    IN p_token VARCHAR(255),
    OUT p_cliente_id INT,
    OUT p_valido BOOLEAN
)
BEGIN
    DECLARE cliente_count INT DEFAULT 0;
    
    SELECT COUNT(*), idCliente INTO cliente_count, p_cliente_id
    FROM Cliente 
    WHERE Reset_Token = p_token 
    AND Token_Expiry > NOW()
    AND Reset_Token IS NOT NULL;
    
    IF cliente_count > 0 THEN
        SET p_valido = TRUE;
    ELSE
        SET p_valido = FALSE;
        SET p_cliente_id = NULL;
    END IF;
END //

-- Procedimiento para actualizar contraseña
CREATE PROCEDURE ActualizarPassword(
    IN p_token VARCHAR(255),
    IN p_nueva_password VARCHAR(255)
)
BEGIN
    UPDATE Cliente 
    SET Password_Hash = p_nueva_password,
        Reset_Token = NULL,
        Token_Expiry = NULL
    WHERE Reset_Token = p_token 
    AND Token_Expiry > NOW();
END //

DELIMITER ;

-- Mostrar resumen de la base de datos
SELECT 'Base de datos creada exitosamente' as Status;
SELECT COUNT(*) as Total_Categorias FROM Categoria;
SELECT COUNT(*) as Total_Productos FROM Producto;
SELECT COUNT(*) as Total_Clientes FROM Cliente;
SELECT COUNT(*) as Total_Proveedores FROM Proveedor;
