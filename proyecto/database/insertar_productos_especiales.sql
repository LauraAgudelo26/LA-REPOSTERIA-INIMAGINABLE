-- Insertar productos especiales para "Placeres Ocultos"
-- Estos productos van en la categoría ID = 2 (Tortas Especiales)

INSERT INTO producto (nombre, descripcion, precio, stock, imagen, categoria_id, destacado, activo) VALUES 
('Torta Lingerie Sensual', 'Deliciosa torta temática para momentos íntimos especiales', 85000, 5, 'Lingerie Cake.jpg', 2, 1, 1),

('Galletas Sugestivas', 'Galletas artesanales con diseños atrevidos y divertidos', 35000, 10, 'Galletas de Sujetadores y Tangas.jpg', 2, 1, 1),

('Torta Despedida de Soltera', 'Espectacular torta temática para despedidas inolvidables', 120000, 3, 'torta para despedida de soltera___.jpg', 2, 1, 1),

('Postre Afrodisíaco', 'Exótico postre con ingredientes especiales y formas sugestivas', 45000, 8, 'pene.jpg', 2, 0, 1),

('Delicia Tropical Sensual', 'Combinación única de frutas exóticas con presentación atrevida', 55000, 6, 'pichos.jpeg', 2, 0, 1),

('Pastel Funny Birthday', 'Divertido pastel para celebraciones especiales entre adultos', 75000, 4, 'funny birthday cake.jpg', 2, 0, 1),

('Fresas con Toque Especial', 'Fresas cubiertas con malvaviscos en presentación romántica', 25000, 12, 'fresas con masmelo.jpeg', 2, 0, 1);

-- Verificar que la categoría existe
SELECT * FROM categoria WHERE id = 2;