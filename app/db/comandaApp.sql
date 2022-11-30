-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 27-06-2022 a las 02:53:25
-- Versión del servidor: 10.4.21-MariaDB
-- Versión de PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "-03:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `comandaApp`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) COLLATE utf8_spanish2_ci NOT NULL,
  `activo` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuesta`
--

CREATE TABLE `encuesta` (
  `id` int(11) NOT NULL,
  `cliente` int(11) NOT NULL,
  `pedido` int(11) NOT NULL,
  `nota_restaurante` int(11) NOT NULL,
  `nota_mozo` int(11) NOT NULL,
  `nota_cocinero` int(11) NOT NULL,
  `texto` varchar(255) COLLATE utf8_spanish2_ci NOT NULL,
  `activo` varchar(25) COLLATE utf8_spanish2_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log`
--

CREATE TABLE `log` (
  `id` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `accion` varchar(255) COLLATE utf8_spanish2_ci NOT NULL,
  `activo` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesa`
--

CREATE TABLE `mesa` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) COLLATE utf8_spanish2_ci NOT NULL,
  `activo` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `mesa`
--

INSERT INTO `mesa` (`id`, `nombre`, `activo`, `created_at`, `updated_at`) VALUES
(10001, 'roja', 1, '2022-06-26 20:01:42', '2022-06-26 20:01:55'),
(10002, 'verde', 1, '2022-06-26 20:02:29', '2022-06-26 20:02:29'),
(10003, 'morada', 1, '2022-06-26 20:02:56', '2022-06-26 20:02:56'),
(10004, 'azul', 1, '2022-06-26 20:02:50', '2022-06-26 20:02:50'),
(10005, 'amarilla', 1, '2022-06-26 20:02:37', '2022-06-26 20:02:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `id` int(5) NOT NULL,
  `id_mesa` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `estado` int(11) NOT NULL,
  `fecha_prevista` datetime NOT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `precio_final` float DEFAULT NULL,
  `activo` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `foto` varchar(255) COLLATE utf8_spanish2_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_producto`
--

CREATE TABLE `pedido_producto` (
  `id` int(11) NOT NULL,
  `id_pedido` int(5) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `estado` int(11) NOT NULL,
  `fecha_prevista` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `activo` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id` int(11) NOT NULL,
  `id_sector` int(11) NOT NULL,
  `nombre` varchar(255) COLLATE utf8_spanish2_ci NOT NULL,
  `precio` float NOT NULL,
  `activo` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id`, `id_sector`, `nombre`, `precio`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 'milanesa a caballo', 1000, 1, '2022-06-26 20:05:00', '2022-06-26 20:05:00'),
(2, 1, 'hamburguesa de garbanzo', 850, 1, '2022-06-26 20:05:36', '2022-06-26 20:05:36'),
(3, 1, 'empanada humita', 250, 1, '2022-06-26 20:06:17', '2022-06-26 20:06:17'),
(4, 3, 'coronita', 500, 1, '2022-06-26 20:06:33', '2022-06-26 20:08:16'),
(5, 3, 'IPA', 500, 1, '2022-06-26 20:08:43', '2022-06-26 20:22:13'),
(6, 2, 'daikiri', 500, 1, '2022-06-26 20:10:32', '2022-06-26 20:10:32'),
(7, 2, 'spritz', 500, 1, '2022-06-26 20:10:39', '2022-06-26 20:10:39'),
(8, 4, 'chocotorta', 900, 1, '2022-06-26 20:11:21', '2022-06-26 20:11:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sector`
--

CREATE TABLE `sector` (
  `id` int(11) NOT NULL,
  `nombre` char(100) COLLATE utf8_spanish2_ci NOT NULL,
  `activo` varchar(25) COLLATE utf8_spanish2_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sector`
--

INSERT INTO `sector` (`id`, `nombre`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'cocina', '1', '2022-06-26 19:50:28', '2022-06-26 19:55:56'),
(2, 'barra', '1', '2022-06-26 19:50:57', '2022-06-26 19:50:57'),
(3, 'choperas', '1', '2022-06-26 19:51:04', '2022-06-26 19:55:37'),
(4, 'candybar', '1', '2022-06-26 19:56:03', '2022-06-26 19:56:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_usuario`
--

CREATE TABLE `tipo_usuario` (
  `id` int(11) NOT NULL,
  `nombre` char(100) COLLATE utf8_spanish2_ci NOT NULL,
  `activo` varchar(25) COLLATE utf8_spanish2_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `tipo_usuario`
--

INSERT INTO `tipo_usuario` (`id`, `nombre`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'mozo', '1', '2022-06-26 19:39:29', '2022-06-26 19:39:29'),
(2, 'socio', '1', '2022-06-26 19:39:38', '2022-06-26 19:39:38'),
(3, 'bartender', '1', '2022-06-26 19:40:00', '2022-06-26 19:40:00'),
(4, 'cervecero', '1', '2022-06-26 19:40:07', '2022-06-26 19:40:07'),
(5, 'cocinero', '1', '2022-06-26 19:40:12', '2022-06-26 19:40:12'),
(6, 'repostero', '1', '2022-06-26 19:40:21', '2022-06-26 19:40:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `dni` varchar(9) COLLATE utf8_spanish2_ci NOT NULL,
  `clave` varchar(255) COLLATE utf8_spanish2_ci NOT NULL,
  `tipo` int(11) NOT NULL,
  `activo` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `dni`, `clave`, `tipo`, `activo`, `created_at`, `updated_at`) VALUES
(1, '20000', '$2y$10$OxQ8riVH7G4z/2pRYnTKt.TRImni82W25gwuE0wUHP0JD2XPLhOCW', 5, 1, '2022-06-26 21:06:58', '2022-06-26 21:06:58'),
(2, '20001', '$2y$10$hPXW/v2jbJAs7aqM00L8J.qTisJ2v3EBvGSiooe13KrpvJk2t32Zy', 1, 1, '2022-06-26 21:39:12', '2022-06-26 21:39:12'),
(3, '20002', '$2y$10$nrcixc5DobNb/o89nMixceYiuu4zT4ZuFpVVs4TsYYljfXU6D9l9a', 2, 1, '2022-06-26 21:39:23', '2022-06-26 21:39:23'),
(4, '20003', '$2y$10$x.U3b19jqzI7unowU.jCQODvQBXWaOPYMQ79SVaMHk5OYAtYoYIYC', 4, 1, '2022-06-26 21:39:34', '2022-06-26 21:39:42'),
(5, '20004', '$2y$10$kU0rNdEjSJ0mMcheMNBndOJQqHk3.bValxSlCCPcZexoKwvrmejlq', 3, 1, '2022-06-26 21:41:12', '2022-06-26 21:41:12'),
(6, '20005', '$2y$10$aQJ2KEsbtjwHrBMPZbTvd.sAGtnqwD06sEgO/MUQEx.QYLGw4SuLi', 6, 1, '2022-06-26 21:41:45', '2022-06-26 21:41:45');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `encuesta`
--
ALTER TABLE `encuesta`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mesa`
--
ALTER TABLE `mesa`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedido_producto`
--
ALTER TABLE `pedido_producto`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sector`
--
ALTER TABLE `sector`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipo_usuario`
--
ALTER TABLE `tipo_usuario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `encuesta`
--
ALTER TABLE `encuesta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `log`
--
ALTER TABLE `log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mesa`
--
ALTER TABLE `mesa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10006;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `pedido_producto`
--
ALTER TABLE `pedido_producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `sector`
--
ALTER TABLE `sector`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipo_usuario`
--
ALTER TABLE `tipo_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
