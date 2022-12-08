-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-12-2022 a las 20:47:00
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `la-comanda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comanda`
--

CREATE TABLE `comanda` (
  `id_comanda` int(11) NOT NULL,
  `codigo_pedido` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_sector` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `estado` int(11) NOT NULL,
  `precio` int(11) NOT NULL,
  `activo` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `comanda`
--

INSERT INTO `comanda` (`id_comanda`, `codigo_pedido`, `id_producto`, `id_sector`, `cantidad`, `estado`, `precio`, `activo`, `id_empleado`, `fecha`) VALUES
(14, 'hjmbh', 14, 1, 1, 3, 450, 1, 3, '2022-12-08 00:56:38'),
(15, 'k971g', 1, 3, 1, 3, 800, 1, 5, '2022-12-08 00:56:38'),
(16, '63wsv', 1, 3, 1, 3, 800, 1, 7, '2022-12-08 00:56:38'),
(17, 'j8vmm', 1, 3, 1, 3, 800, 1, 5, '2022-12-08 00:56:38'),
(18, 'j8vmm', 3, 3, 2, 3, 1000, 1, 7, '2022-12-08 00:56:38'),
(19, 'j8vmm', 11, 2, 1, 3, 100, 1, 4, '2022-12-08 00:56:38'),
(20, 'j8vmm', 14, 1, 1, 3, 450, 1, 3, '2022-12-08 00:56:38'),
(21, '8rkqm', 1, 3, 5, 3, 4000, 1, 7, '2022-12-08 01:02:07'),
(22, '8rkqm', 11, 2, 5, 3, 500, 1, 4, '2022-12-08 01:02:18'),
(23, 'l672s', 6, 3, 2, 3, 1800, 1, 7, '2022-12-08 15:41:02'),
(24, 'l672s', 10, 1, 2, 3, 200, 1, 3, '2022-12-08 15:41:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado`
--

CREATE TABLE `empleado` (
  `id_empleado` int(11) NOT NULL,
  `usuario` varchar(250) NOT NULL,
  `clave` varchar(1000) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `nombre_empleado` varchar(50) NOT NULL,
  `estado` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `fecha_ultimo_login` datetime NOT NULL,
  `id_sector` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `empleado`
--

INSERT INTO `empleado` (`id_empleado`, `usuario`, `clave`, `id_tipo`, `nombre_empleado`, `estado`, `fecha_registro`, `fecha_ultimo_login`, `id_sector`) VALUES
(1, 'admin', '$2y$10$yiebVkIeNFUTqhk/wPsC..H.WAekqT3vBX0xpSagTQ/K9U95NYtk.', 5, 'Administrador', 1, '2022-06-24 00:00:00', '2022-12-08 00:34:15', NULL),
(2, 'unia', '$2y$10$brwNjigl6H0PGz9rG8Of1ep1RsdeGvkaM46gF7.ad4icDTiDSoFci', 5, 'Ezequiel Unía', 1, '2022-06-24 00:00:00', '2022-06-24 00:00:00', NULL),
(3, 'bartender', '$2y$10$/dmlREp5NWsZXCS61JoFKeMbKKxEOdzqFsNkIPc7cG/GFEhiEWLUC', 1, 'Empleado Bar', 1, '2022-06-24 00:00:00', '2022-12-08 15:39:21', 1),
(4, 'cervecero', '$2y$10$fZsqU.sb.O2niDZ8ezfx/em50i/loQT1/OYLa06k101e7uAD3lHV.', 2, 'Empleado Choperia', 1, '2022-06-24 00:00:00', '2022-12-07 20:43:40', 2),
(5, 'cocinero', '$2y$10$nVIh4S8Bndx/TztP57F4..iOPvDTvlNK4w4UlLuMKvZ8Ti0/kWcMe', 3, 'Empleado Cocina', 1, '2022-06-24 00:00:00', '2022-12-07 20:43:38', 3),
(6, 'mozo', '$2y$10$CIktwUFWAcvBZCkqHK8eYuwkpII4br/iy197gsuCuedAgDYS9dMQu', 4, 'Empleado Mozo', 1, '2022-06-24 00:00:00', '2022-12-07 20:43:36', NULL),
(7, 'cocinero2', '$2y$10$g8c/DqAR3SaqYLIBYfxELOSO7iJtLrVw4NzY/9fzsWl8P4iAO2SVW', 2, 'Empleado Cheff', 1, '2022-12-07 18:51:21', '2022-12-07 18:51:21', 3),
(8, 'barman2', '$2y$10$m8jJVX6VpPFznZr5KR24EeYlR9lFsCukUu6o6pG1Gx0S3PZ5ESaZG', 1, 'Empleado Batman', 1, '2022-12-08 15:38:17', '2022-12-08 15:38:17', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuesta`
--

CREATE TABLE `encuesta` (
  `id_encuesta` int(11) NOT NULL,
  `codigo_pedido` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `codigo_mesa` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `cliente` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `rating_mesa` int(11) NOT NULL,
  `rating_restaurante` int(11) NOT NULL,
  `rating_mozo` int(11) NOT NULL,
  `rating_cocinero` int(11) NOT NULL,
  `opinion` varchar(66) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `encuesta`
--

INSERT INTO `encuesta` (`id_encuesta`, `codigo_pedido`, `codigo_mesa`, `cliente`, `rating_mesa`, `rating_restaurante`, `rating_mozo`, `rating_cocinero`, `opinion`, `fecha`) VALUES
(2, '63wsv', 'me003', 'Roberto3', 8, 7, 9, 8, 'Buena atencion', '2022-12-08 15:25:23'),
(3, 'j8vmm', 'me003', 'Eze', 5, 5, 5, 5, 'Buena', '2022-12-08 15:25:23'),
(4, '8rkqm', 'me001', 'Luciano', 6, 4, 6, 4, 'Regular', '2022-12-08 15:25:23'),
(5, 'l672s', 'me001', 'Alfredo', 3, 2, 1, 2, 'muy mala', '2022-12-08 15:47:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_comanda`
--

CREATE TABLE `estados_comanda` (
  `id_estado_comanda` int(11) NOT NULL,
  `estado_comanda` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `estados_comanda`
--

INSERT INTO `estados_comanda` (`id_estado_comanda`, `estado_comanda`) VALUES
(1, 'Pendiente'),
(2, 'En Preparacion'),
(3, 'Listo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_mesa`
--

CREATE TABLE `estados_mesa` (
  `id_estado_mesa` int(11) NOT NULL,
  `estado_mesa` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `estados_mesa`
--

INSERT INTO `estados_mesa` (`id_estado_mesa`, `estado_mesa`) VALUES
(1, 'Cliente Esperando Pedido'),
(2, 'Cliente Comiendo'),
(3, 'Cliente Pagando'),
(4, 'Cerrada'),
(5, 'Libre');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_pedido`
--

CREATE TABLE `estados_pedido` (
  `id_estado_pedido` int(11) NOT NULL,
  `estado_pedido` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `estados_pedido`
--

INSERT INTO `estados_pedido` (`id_estado_pedido`, `estado_pedido`) VALUES
(1, 'Pendiente'),
(2, 'En Preparacin'),
(3, 'Listo'),
(4, 'Cancelado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesa`
--

CREATE TABLE `mesa` (
  `id_mesa` int(11) NOT NULL,
  `estado_mesa` int(11) NOT NULL,
  `codigo_mesa` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `mesa`
--

INSERT INTO `mesa` (`id_mesa`, `estado_mesa`, `codigo_mesa`) VALUES
(1, 5, 'me001'),
(2, 5, 'me002'),
(3, 5, 'me003');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `id_pedido` int(11) NOT NULL,
  `codigo_pedido` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `id_mesa` int(11) NOT NULL,
  `id_mozo` int(11) NOT NULL,
  `cliente` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `estado` int(11) NOT NULL,
  `created_at` time NOT NULL,
  `hora_entrega` time DEFAULT NULL,
  `precio_final` int(11) NOT NULL,
  `activo` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `foto` varchar(200) DEFAULT NULL,
  `entrega_demorada` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'NO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `pedido`
--

INSERT INTO `pedido` (`id_pedido`, `codigo_pedido`, `id_mesa`, `id_mozo`, `cliente`, `estado`, `created_at`, `hora_entrega`, `precio_final`, `activo`, `fecha`, `foto`, `entrega_demorada`) VALUES
(7, 'hjmbh', 1, 6, 'Roberto2', 5, '22:10:27', '22:11:27', 450, 1, '2022-12-06 22:10:27', NULL, 'SI'),
(8, 'k971g', 2, 6, 'Roberto2', 5, '00:13:53', '00:38:53', 800, 1, '2022-12-07 00:13:53', NULL, 'SI'),
(9, '63wsv', 3, 6, 'Roberto2', 4, '00:16:44', '00:41:44', 800, 1, '2022-12-07 00:16:44', NULL, 'SI'),
(10, 'j8vmm', 3, 6, 'Ezequiel', 5, '21:10:40', '21:35:40', 2350, 1, '2022-12-07 21:10:40', 'j8vmm_me003_Ezequiel.jpg', 'NO'),
(11, '8rkqm', 1, 6, 'Luciano', 5, '01:01:57', '01:26:57', 4500, 1, '2022-12-08 01:01:57', NULL, 'NO'),
(12, 'l672s', 1, 6, 'Alfredo', 5, '15:40:29', '16:10:29', 2000, 1, '2022-12-08 15:40:29', 'l672s_me001_Alfredo.png', 'NO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `precio` int(11) NOT NULL,
  `id_sector` int(11) NOT NULL,
  `tiempo_preparacion` int(11) DEFAULT 1,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id_producto`, `nombre`, `precio`, `id_sector`, `tiempo_preparacion`, `estado`) VALUES
(1, 'Milanesa a Caballo', 800, 3, 25, 1),
(2, 'Pizza', 1000, 3, 30, 1),
(3, 'Hamburguesa de Garbanzo', 500, 3, 20, 1),
(4, 'Ensalada', 600, 3, 15, 1),
(5, 'Pollo', 700, 3, 25, 1),
(6, 'Pescado', 900, 3, 30, 1),
(7, 'Coca-Cola', 100, 1, 5, 1),
(8, 'Fanta', 100, 1, 5, 1),
(9, 'Sprite', 100, 1, 5, 1),
(10, 'Agua', 100, 1, 5, 1),
(11, 'Cerveza Corona', 100, 2, 5, 1),
(12, 'Gin Tonic', 100, 1, 5, 1),
(13, 'Carne al horno', 1250, 3, 30, 1),
(14, 'Daikiri', 450, 1, 1, 1),
(15, 'Cheesecake', 350, 4, 5, 1),
(16, 'Flan', 290, 4, 5, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro`
--

CREATE TABLE `registro` (
  `id_registro` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `id_usuario` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `registro`
--

INSERT INTO `registro` (`id_registro`, `fecha`, `id_usuario`) VALUES
(5, '2022-12-07 20:42:15', 1),
(6, '2022-12-07 20:42:17', 3),
(7, '2022-12-07 20:42:19', 4),
(8, '2022-12-07 20:43:36', 6),
(9, '2022-12-07 20:43:38', 5),
(10, '2022-12-07 20:43:40', 4),
(11, '2022-12-07 20:43:43', 3),
(12, '2022-12-07 20:49:01', 1),
(13, '2022-12-08 00:34:15', 1),
(14, '2022-12-08 15:39:21', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sector`
--

CREATE TABLE `sector` (
  `id_sector` int(11) NOT NULL,
  `nombre` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `sector`
--

INSERT INTO `sector` (`id_sector`, `nombre`) VALUES
(1, 'Barra'),
(2, 'Choperia'),
(3, 'Cocina'),
(4, 'Candy Bar');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo`
--

CREATE TABLE `tipo` (
  `id` int(11) NOT NULL,
  `nombre` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `tipo`
--

INSERT INTO `tipo` (`id`, `nombre`) VALUES
(1, 'Bartender'),
(2, 'Cervecero'),
(3, 'Cocinero'),
(4, 'Mozo'),
(5, 'Socio');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comanda`
--
ALTER TABLE `comanda`
  ADD PRIMARY KEY (`id_comanda`);

--
-- Indices de la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD PRIMARY KEY (`id_empleado`);

--
-- Indices de la tabla `encuesta`
--
ALTER TABLE `encuesta`
  ADD PRIMARY KEY (`id_encuesta`);

--
-- Indices de la tabla `estados_comanda`
--
ALTER TABLE `estados_comanda`
  ADD PRIMARY KEY (`id_estado_comanda`);

--
-- Indices de la tabla `estados_mesa`
--
ALTER TABLE `estados_mesa`
  ADD PRIMARY KEY (`id_estado_mesa`);

--
-- Indices de la tabla `estados_pedido`
--
ALTER TABLE `estados_pedido`
  ADD PRIMARY KEY (`id_estado_pedido`);

--
-- Indices de la tabla `mesa`
--
ALTER TABLE `mesa`
  ADD PRIMARY KEY (`id_mesa`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id_pedido`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id_producto`);

--
-- Indices de la tabla `registro`
--
ALTER TABLE `registro`
  ADD PRIMARY KEY (`id_registro`);

--
-- Indices de la tabla `sector`
--
ALTER TABLE `sector`
  ADD PRIMARY KEY (`id_sector`);

--
-- Indices de la tabla `tipo`
--
ALTER TABLE `tipo`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comanda`
--
ALTER TABLE `comanda`
  MODIFY `id_comanda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `empleado`
--
ALTER TABLE `empleado`
  MODIFY `id_empleado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `encuesta`
--
ALTER TABLE `encuesta`
  MODIFY `id_encuesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `estados_comanda`
--
ALTER TABLE `estados_comanda`
  MODIFY `id_estado_comanda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `estados_mesa`
--
ALTER TABLE `estados_mesa`
  MODIFY `id_estado_mesa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `estados_pedido`
--
ALTER TABLE `estados_pedido`
  MODIFY `id_estado_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `mesa`
--
ALTER TABLE `mesa`
  MODIFY `id_mesa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `registro`
--
ALTER TABLE `registro`
  MODIFY `id_registro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `sector`
--
ALTER TABLE `sector`
  MODIFY `id_sector` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipo`
--
ALTER TABLE `tipo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
