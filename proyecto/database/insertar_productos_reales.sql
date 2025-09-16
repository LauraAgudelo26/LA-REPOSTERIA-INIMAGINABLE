-- Insertar productos reales basados en las imágenes disponibles
-- Ejecutar después de crear la base de datos con database_schema.sql

USE tienda_productos;

-- Limpiar productos existentes si es necesario
-- DELETE FROM DetallePedido;
-- DELETE FROM Pedido;
-- DELETE FROM Producto;

-- Insertar productos reales
INSERT INTO Producto (Nombre, Descripcion, Precio, Stock, idCategoria) VALUES

-- POSTRES (idCategoria = 1)
('Cheesecake de Fresa Delicioso', 'Exquisito cheesecake cremoso con base de galletas y cobertura de fresas frescas', 28000.00, 12, 1),
('Churros Tradicionales', 'Churros dorados y crujientes espolvoreados con azúcar y canela, perfectos para compartir', 8500.00, 35, 1),
('Donas Glaseadas', 'Suaves donas esponjosas con glaseado colorido y decoraciones especiales', 5500.00, 40, 1),
('Postre de Gelatina', 'Refrescante gelatina de frutas en capas con texturas variadas', 6500.00, 25, 1),
('Postre de Limón', 'Cremoso postre cítrico con base de galletas y cobertura de merengue', 9500.00, 18, 1),
('Postre de Oreo', 'Delicioso postre cremoso con galletas Oreo trituradas y crema batida', 11000.00, 20, 1),
('Rollos de Canela', 'Tiernos rollos dulces con canela y glaseado de vainilla', 9000.00, 22, 1),
('Cuca Tradicional', 'Torta húmeda con cobertura crujiente de azúcar y mantequilla', 15000.00, 10, 1),

-- FRUTAS (idCategoria = 3)
('Fresas con Crema', 'Fresas frescas seleccionadas servidas con crema batida natural', 12500.00, 30, 3),
('Fresas con Marshmallow', 'Fresas jugosas acompañadas de marshmallows suaves y cremosos', 14000.00, 25, 3),
('Banano Fresco', 'Bananos maduros y dulces, perfectos para postres o consumo directo', 3500.00, 50, 3),
('Cerezas Selectas', 'Cerezas rojas dulces y jugosas, ideales para decorar postres', 18000.00, 15, 3),
('Manzanas Rojas', 'Manzanas crujientes y dulces, perfectas para tartas y postres', 4500.00, 45, 3),
('Uvas Frescas', 'Racimos de uvas dulces y jugosas de primera calidad', 8000.00, 30, 3),

-- PASTELES ESPECIALES (idCategoria = 4)
('Torta para Despedida de Soltera', 'Torta temática especial para celebraciones de despedida de soltera', 95000.00, 3, 4),
('Mini Tarta de Frutas', 'Pequeña tarta decorada con frutas frescas de temporada', 16500.00, 15, 4),
('Funny Birthday Cake', 'Torta divertida de cumpleaños con decoración especial y personalizable', 75000.00, 5, 4),
('Galletas de Sujetadores y Tangas', 'Galletas temáticas con formas divertidas para fiestas especiales', 25000.00, 8, 4),
('Lingerie Cake', 'Torta temática elegante para ocasiones especiales de adultos', 120000.00, 2, 4),
('Torta Fálica', 'Torta temática para despedidas de soltero y fiestas especiales', 85000.00, 3, 4),

-- GALLETAS (idCategoria = 5)
('Galletas con Chips de Chocolate', 'Galletas caseras horneadas con chips de chocolate belga', 4500.00, 50, 5),

-- BEBIDAS (idCategoria = 2)
('Cappuccino Artesanal', 'Café cappuccino preparado con granos selectos y espuma de leche cremosa', 7500.00, 60, 2),
('Bebidas Refrescantes', 'Variedad de bebidas naturales refrescantes con frutas', 6000.00, 40, 2);

-- Verificar la inserción
SELECT 'Productos insertados correctamente' as Status;
SELECT COUNT(*) as Total_Productos_Insertados FROM Producto;

-- Mostrar productos por categoría
SELECT 
    c.Nombre as Categoria,
    COUNT(p.idProducto) as Cantidad_Productos
FROM Categoria c
LEFT JOIN Producto p ON c.idCategoria = p.idCategoria
GROUP BY c.idCategoria, c.Nombre
ORDER BY c.Nombre;

-- Mostrar todos los productos con sus categorías
SELECT 
    p.idProducto,
    p.Nombre as Producto,
    c.Nombre as Categoria,
    p.Precio,
    p.Stock
FROM Producto p
JOIN Categoria c ON p.idCategoria = c.idCategoria
ORDER BY c.Nombre, p.Nombre;
