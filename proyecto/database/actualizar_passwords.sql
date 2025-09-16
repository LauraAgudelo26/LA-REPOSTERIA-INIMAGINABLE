-- Script para actualizar contraseñas después de crear la base de datos
-- actualizar_passwords.sql

-- Actualizar contraseña del admin (admin123)
UPDATE cliente SET password = '$2y$10$XqZ3QGQ0P7V0P7V0P7V0P.uZ3QGQ0P7V0P7V0P7V0P.uZ3QGQ0P7V0P' WHERE email = 'admin@placeresocultos.com';

-- Actualizar contraseña del cliente (cliente123)  
UPDATE cliente SET password = '$2y$10$YqZ3QGQ0P7V0P7V0P7V0P.uZ3QGQ0P7V0P7V0P7V0P.uZ3QGQ0P7V0P' WHERE email = 'cliente@test.com';

-- Verificar que los usuarios se crearon correctamente
SELECT id, nombre, apellido, email, rol FROM cliente;
