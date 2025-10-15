-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 15-10-2025 a las 00:10:34
-- Versión del servidor: 8.4.3
-- Versión de PHP: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `la_repoteria_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id` int NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `imagen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activa` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `orden_mostrar` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id`, `nombre`, `descripcion`, `imagen`, `activa`, `fecha_creacion`, `orden_mostrar`) VALUES
(1, 'Postres', 'Deliciosos postres caseros', NULL, 1, '2025-09-24 00:43:02', 0),
(2, 'Tortas Especiales', 'Tortas para ocasiones especiales', NULL, 1, '2025-09-24 00:43:02', 0),
(3, 'Frutas', 'Frutas frescas y preparadas', NULL, 1, '2025-09-24 00:43:02', 0),
(4, 'Galletas', 'Galletas artesanales', NULL, 1, '2025-09-24 00:43:02', 0),
(5, 'Bebidas', 'Bebidas y café especializado', NULL, 1, '2025-09-24 00:43:02', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id` int NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_usuario` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `tipo_documento` enum('cc','ti','dni','pasaporte','otro') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'cc',
  `numero_documento` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('admin','cliente') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'cliente',
  `activo` tinyint(1) DEFAULT '1',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_acceso` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id`, `nombre`, `apellido`, `nombre_usuario`, `email`, `telefono`, `direccion`, `tipo_documento`, `numero_documento`, `password`, `rol`, `activo`, `fecha_registro`, `ultimo_acceso`) VALUES
(1, 'Administrador', 'Sistema', 'admin', 'admin@placeresocultos.com', '1234567890', 'Dirección Admin', 'cc', NULL, '$2y$10$PJsNoIa2aw3VCB.q54/ykeMw/KvrYhTKD1ePJKt/zcMb/CL3jrwES', 'admin', 1, '2025-09-05 01:30:24', NULL),
(2, 'Cliente', 'Prueba', 'cliente', 'cliente@test.com', '0987654321', 'Dirección Cliente', 'cc', NULL, '$2y$10$0Bi.w9jqq0ylkmsxzMdSlO0SFi8Gpc1eXqicY2JHEQUCSLYrL022S', 'cliente', 1, '2025-09-05 01:30:24', NULL),
(3, 'A', 'A', 'a2', 'A@gmail.com', '231', 'Calle A', 'cc', '2123123213', 'aaaaaaa', 'cliente', 1, '2025-09-05 01:54:47', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `id` int NOT NULL,
  `cliente_id` int NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','confirmado','en_preparacion','listo','entregado','cancelado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `fecha_pedido` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_entrega` timestamp NULL DEFAULT NULL,
  `direccion_entrega` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `telefono_entrega` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_detalle`
--

CREATE TABLE `pedido_detalle` (
  `id` int NOT NULL,
  `pedido_id` int NOT NULL,
  `producto_id` int NOT NULL,
  `cantidad` int NOT NULL DEFAULT '1',
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id` int NOT NULL,
  `nombre` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `precio` decimal(10,2) NOT NULL,
  `categoria_id` int DEFAULT NULL,
  `imagen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock` int DEFAULT '0',
  `activo` tinyint(1) DEFAULT '1',
  `destacado` tinyint(1) DEFAULT '0',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id`, `nombre`, `descripcion`, `precio`, `categoria_id`, `imagen`, `stock`, `activo`, `destacado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Banano', 'Delicioso Banano hecho artesanalmente', 20000.00, 3, 'banano.jpg', 16, 1, 0, '2025-09-24 00:43:02', '2025-09-24 00:43:02'),
(2, 'Bebidas Refrescantes', 'Delicioso Bebidas Refrescantes hecho artesanalmente', 25000.00, 5, 'bebidas_refrescantes.jpg', 14, 1, 0, '2025-09-24 00:43:02', '2025-09-24 00:48:39'),
(3, 'Cappuccino', 'Delicioso Cappuccino hecho artesanalmente', 40000.00, 5, 'cappuccino.jpg', 18, 1, 1, '2025-09-24 00:43:02', '2025-09-24 00:43:02'),
(4, 'Cereza', 'Delicioso Cereza hecho artesanalmente', 30000.00, 3, 'cereza.jpg', 18, 1, 1, '2025-09-24 00:43:02', '2025-09-24 00:43:02'),
(5, 'Cheesecake Fresa', 'Delicioso Cheesecake Fresa hecho artesanalmente', 18000.00, 3, 'cheesecake_fresa.jpg', 9, 1, 0, '2025-09-24 00:43:02', '2025-09-24 00:43:02'),
(6, 'Churros', 'Delicioso Churros hecho artesanalmente', 15000.00, 1, 'churros.jpg', 7, 1, 0, '2025-09-24 00:43:02', '2025-09-24 00:43:02'),
(7, 'Cuca', 'Delicioso Cuca hecho artesanalmente', 15000.00, 2, 'cuca.jpg', 8, 1, 0, '2025-09-24 00:43:02', '2025-09-24 01:24:48'),
(8, 'Donas', 'Delicioso Donas hecho artesanalmente', 40000.00, 1, 'donas.jpg', 11, 1, 1, '2025-09-24 00:43:02', '2025-09-24 00:43:02'),
(9, 'Fresas Crema', 'Delicioso Fresas Crema hecho artesanalmente', 18000.00, 3, 'fresas_crema.jpg', 6, 1, 0, '2025-09-24 00:43:02', '2025-09-24 00:43:02'),
(10, 'Fresas Marshmallow', 'Delicioso Fresas Marshmallow hecho artesanalmente', 15000.00, 3, 'fresas_marshmallow.jpg', 16, 1, 1, '2025-09-24 00:43:02', '2025-09-24 00:48:39'),
(11, 'Galletas Chocolate', 'Delicioso Galletas Chocolate hecho artesanalmente', 40000.00, 4, 'galletas_chocolate.jpg', 8, 1, 0, '2025-09-24 00:43:02', '2025-09-24 00:43:02'),
(12, 'Galletas Especiales', 'Delicioso Galletas Especiales hecho artesanalmente', 35000.00, 2, 'galletas_especiales.jpg', 15, 1, 0, '2025-09-24 00:43:02', '2025-09-24 01:25:18'),
(14, 'Manzana', 'Delicioso Manzana hecho artesanalmente', 20000.00, 3, 'manzana.jpg', 13, 1, 0, '2025-09-24 00:43:02', '2025-09-24 00:43:02'),
(15, 'Mini Tarta Frutas', 'Delicioso Mini Tarta Frutas hecho artesanalmente', 30000.00, 1, 'mini_tarta_frutas.jpg', 17, 1, 0, '2025-09-24 00:43:02', '2025-09-24 00:43:02'),
(16, 'Pichos', 'Delicioso Pichos hecho artesanalmente', 50000.00, 1, 'pichos.jpg', 15, 1, 1, '2025-09-24 00:43:02', '2025-09-24 00:43:02'),
(17, 'Postre Gelatina', 'Delicioso Postre Gelatina hecho artesanalmente', 40000.00, 1, 'postre_gelatina.jpg', 14, 1, 0, '2025-09-24 00:43:02', '2025-09-24 00:43:02'),
(18, 'Postre Limon', 'Delicioso Postre Limon hecho artesanalmente', 35000.00, 1, 'postre_limon.jpg', 19, 1, 0, '2025-09-24 00:43:02', '2025-09-24 00:43:02'),
(19, 'Postre Oreo', 'Delicioso Postre Oreo hecho artesanalmente', 35000.00, 1, 'postre_oreo.jpg', 10, 1, 0, '2025-09-24 00:43:02', '2025-09-24 00:43:02'),
(20, 'Rollos Canela', 'Delicioso Rollos Canela hecho artesanalmente', 18000.00, 1, 'rollos_canela.jpg', 9, 1, 0, '2025-09-24 00:43:02', '2025-09-24 00:43:02'),
(21, 'Torta Cumpleanos', 'Delicioso Torta Cumpleanos hecho artesanalmente', 15000.00, 2, 'torta_cumpleanos.jpg', 10, 1, 0, '2025-09-24 00:43:02', '2025-09-24 00:43:02'),
(22, 'Torta Falica', 'Delicioso Torta Falica hecho artesanalmente', 15000.00, 2, 'torta_falica.jpg', 19, 1, 0, '2025-09-24 00:43:02', '2025-09-24 00:43:02'),
(23, 'Torta Lingerie', 'Delicioso Torta Lingerie hecho artesanalmente', 30000.00, 2, 'torta_lingerie.jpg', 5, 1, 1, '2025-09-24 00:43:02', '2025-09-24 00:43:02'),
(24, 'Torta Soltera', 'Delicioso Torta Soltera hecho artesanalmente', 35000.00, 2, 'torta_soltera.jpg', 7, 1, 0, '2025-09-24 00:43:02', '2025-09-24 00:43:02'),
(25, 'Uvas', 'Delicioso Uvas hecho artesanalmente', 35000.00, 3, 'uvas.jpg', 10, 1, 0, '2025-09-24 00:43:02', '2025-09-24 00:43:02');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `idx_nombre` (`nombre`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_usuario` (`nombre_usuario`),
  ADD KEY `idx_rol` (`rol`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cliente` (`cliente_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha` (`fecha_pedido`);

--
-- Indices de la tabla `pedido_detalle`
--
ALTER TABLE `pedido_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pedido` (`pedido_id`),
  ADD KEY `idx_producto` (`producto_id`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nombre` (`nombre`),
  ADD KEY `idx_precio` (`precio`),
  ADD KEY `idx_categoria` (`categoria_id`),
  ADD KEY `idx_activo` (`activo`),
  ADD KEY `idx_destacado` (`destacado`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedido_detalle`
--
ALTER TABLE `pedido_detalle`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `fk_pedido_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pedido_detalle`
--
ALTER TABLE `pedido_detalle`
  ADD CONSTRAINT `fk_detalle_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `fk_producto_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
