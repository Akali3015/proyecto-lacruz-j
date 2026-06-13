-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-06-2026 a las 23:00:30
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `proyecto_lacruz`
--
CREATE DATABASE IF NOT EXISTS `proyecto_lacruz` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `proyecto_lacruz`;

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_precio_materia_prima` (IN `id_materia_prima` VARCHAR(20), IN `precio_materia_prima` DECIMAL(20,2))   BEGIN
    INSERT INTO precios_materias_primas (id_materia_prima, precio_materia_prima) 
    VALUES (id_materia_prima, precio_materia_prima);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_precio_moneda` (IN `id_moneda` INT, IN `nuevo_valor_moneda` DECIMAL(20,2))   BEGIN
	INSERT INTO cambios_monedas(id_moneda,valor_moneda)
    VALUES(id_moneda, nuevo_valor_moneda);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_precio_producto` (IN `p_id_producto` VARCHAR(20), IN `p_precio` DECIMAL(20,2))   BEGIN
    INSERT INTO precios_productos (id_producto, precio_producto) VALUES (p_id_producto, p_precio);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_precio_ruta` (IN `id_ruta` INT, IN `precio_ruta` DECIMAL(20,2))   BEGIN
    INSERT INTO precios_rutas (id_ruta, precio_ruta) VALUES (id_ruta, precio_ruta);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_precio_servicio` (IN `id_servicio` VARCHAR(20), IN `precio_servicio` DECIMAL(20,2))   BEGIN
    INSERT INTO precios_servicios (id_servicio, precio_servicio) VALUES (id_servicio, precio_servicio);
END$$

--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `funcionJJJ` () RETURNS DECIMAL(20,2) READS SQL DATA BEGIN
	DECLARE valor_mm decimal(20,2);
	SELECT valor_moneda INTO valor_mm FROM monedas WHERE id_moneda =1;
    return valor_mm;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `obtener_iva_actual` () RETURNS DECIMAL(20,2) READS SQL DATA BEGIN
    DECLARE v_iva decimal(20,2);
    SELECT monto_cambio_iva INTO v_iva 
    FROM cambios_iva 
    ORDER BY id_cambio_iva DESC
    LIMIT 1;
    RETURN v_iva;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `obtener_iva_actual2` () RETURNS DECIMAL(20,2) READS SQL DATA BEGIN
    DECLARE v_iva decimal(20,2);
    SELECT monto_cambio_iva INTO v_iva 
    FROM cambios_iva 
    ORDER BY id_cambio_iva DESC
    LIMIT 1;
    RETURN v_iva;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `obtener_iva_actual3` () RETURNS DECIMAL(20,2) READS SQL DATA BEGIN
    DECLARE v_iva decimal(20,2);
    SELECT monto_cambio_iva INTO v_iva 
    FROM cambios_iva 
    ORDER BY id_cambio_iva DESC
    LIMIT 1;
    RETURN v_iva;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bancos`
--

CREATE TABLE `bancos` (
  `id_banco` int(11) NOT NULL,
  `nombre_banco` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bancos`
--

INSERT INTO `bancos` (`id_banco`, `nombre_banco`, `status`) VALUES
(1, 'BANCO DE VENEZUELA', 1),
(2, 'BANCO PROVINCIAL', 1),
(93, 'bando de ejemplo', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bancos_detalles_pagos`
--

CREATE TABLE `bancos_detalles_pagos` (
  `id_banco_detalle_pago` int(11) NOT NULL,
  `id_detalle_pago` int(11) NOT NULL,
  `id_banco` int(11) NOT NULL,
  `es_emisor` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bancos_detalles_pagos`
--

INSERT INTO `bancos_detalles_pagos` (`id_banco_detalle_pago`, `id_detalle_pago`, `id_banco`, `es_emisor`, `status`) VALUES
(189, 161, 1, 1, 1),
(190, 161, 2, 0, 1),
(191, 162, 1, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cambios_iva`
--

CREATE TABLE `cambios_iva` (
  `id_cambio_iva` int(11) NOT NULL,
  `monto_cambio_iva` decimal(20,2) NOT NULL,
  `fecha_cambio_iva` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cambios_iva`
--

INSERT INTO `cambios_iva` (`id_cambio_iva`, `monto_cambio_iva`, `fecha_cambio_iva`, `status`) VALUES
(1, 12.00, '2025-12-05 18:15:15', 1),
(129, 12.00, '2026-04-24 15:34:11', 1),
(130, 12.00, '2026-04-24 15:34:17', 1),
(131, 12.00, '2026-04-24 15:36:14', 1),
(132, 12.00, '2026-04-24 15:36:50', 1),
(133, 12.00, '2026-04-24 15:36:54', 1),
(134, 12.00, '2026-04-24 15:41:00', 1),
(135, 24.00, '2026-04-24 15:46:47', 1),
(136, 12.00, '2026-04-24 15:49:02', 1),
(137, 122.00, '2026-05-05 20:28:20', 1),
(138, 16.00, '2026-05-17 10:12:05', 1),
(161, 77.00, '2026-05-25 11:55:35', 1),
(162, 15.00, '2026-05-25 12:00:18', 1),
(163, 19.00, '2026-05-25 12:05:19', 1),
(164, 78.00, '2026-05-25 12:06:32', 1),
(165, 15.00, '2026-05-25 12:07:45', 1),
(166, 15.00, '2026-05-25 12:18:15', 1),
(168, 12.00, '2026-05-25 12:48:10', 1),
(178, 23.00, '2026-05-25 13:22:39', 1),
(179, 12.00, '2026-05-25 13:23:25', 1),
(180, 15.00, '2026-05-25 13:24:59', 1),
(181, 23.00, '2026-05-25 13:30:57', 1),
(182, 78.00, '2026-05-25 13:32:03', 1),
(183, 89.00, '2026-05-25 13:32:20', 1),
(186, 34.00, '2026-05-25 13:44:22', 1),
(187, 12.00, '2026-05-25 13:49:17', 1),
(188, 15.00, '2026-05-25 13:49:48', 1),
(189, 16.00, '2026-05-25 19:41:09', 1),
(190, 18.00, '2026-05-25 19:42:29', 1),
(191, 20.00, '2026-05-26 20:46:10', 1),
(192, 22.00, '2026-05-26 20:47:31', 1),
(195, 24.00, '2026-05-26 20:49:45', 1),
(197, 16.00, '2026-05-29 08:10:36', 1),
(198, 20.00, '2026-05-29 08:17:38', 1),
(199, 16.00, '2026-05-29 08:18:08', 1),
(200, 23.00, '2026-05-29 08:19:05', 1),
(201, 24.00, '2026-05-29 08:20:54', 1),
(234, 1.00, '2026-06-09 00:56:53', 1),
(235, 1250000000000.00, '2026-06-13 16:27:13', 1),
(236, 1250000000000.00, '2026-06-13 16:27:13', 1),
(237, 1250.00, '2026-06-13 16:28:00', 1),
(238, 1250.00, '2026-06-13 16:28:00', 1),
(239, 1250.50, '2026-06-13 16:28:06', 1),
(240, 1250.50, '2026-06-13 16:28:06', 1),
(250, 77.00, '2026-06-13 16:47:54', 1),
(251, 77.00, '2026-06-13 16:48:00', 1),
(252, 87.00, '2026-06-13 16:48:18', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cambios_monedas`
--

CREATE TABLE `cambios_monedas` (
  `id_cambio_moneda` int(11) NOT NULL,
  `id_moneda` int(11) NOT NULL,
  `valor_moneda` decimal(20,2) NOT NULL,
  `fecha_cambio` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cambios_monedas`
--

INSERT INTO `cambios_monedas` (`id_cambio_moneda`, `id_moneda`, `valor_moneda`, `fecha_cambio`, `status`) VALUES
(1, 1, 17.00, '2025-12-06 20:12:19', 1),
(2, 2, 2.00, '2025-12-06 20:13:37', 1),
(3, 3, 3.00, '2025-12-06 20:13:48', 1),
(4, 1, 257.93, '2025-12-06 20:14:15', 1),
(5, 1, 250.00, '2025-12-11 18:35:07', 1),
(6, 3, 250.00, '2025-12-11 19:04:46', 1),
(7, 2, 1.00, '2025-12-14 16:30:34', 1),
(8, 1, 23.00, '2026-02-07 20:44:34', 1),
(9, 1, 12.00, '2026-02-09 20:46:09', 1),
(10, 1, 250.00, '2026-02-09 20:46:55', 1),
(11, 1, 279.00, '2026-03-30 10:22:47', 1),
(12, 2, 22.00, '2026-03-30 10:22:56', 1),
(13, 3, 500.00, '2026-03-30 10:23:05', 1),
(14, 1, 12.00, '2026-03-30 10:29:39', 1),
(15, 3, 1234.00, '2026-03-30 10:29:58', 1),
(20, 1, 1200.00, '2026-05-06 10:25:10', 1),
(21, 3, 1200.00, '2026-05-06 10:25:20', 1),
(22, 1, 49338.00, '2026-05-06 16:29:26', 1),
(23, 1, 496.83, '2026-05-06 18:29:34', 1),
(24, 1, 2500.00, '2026-05-06 18:43:11', 1),
(25, 2, 120.00, '2026-05-06 18:44:28', 1),
(26, 1, 250.01, '2026-05-06 18:44:42', 1),
(27, 1, 500.00, '2026-05-06 18:44:53', 1),
(28, 2, 1.00, '2026-05-17 08:25:25', 1),
(29, 3, 600.00, '2026-05-17 08:26:06', 1),
(30, 1, 700.00, '2026-05-17 15:41:04', 1),
(31, 2, 1000.00, '2026-05-26 20:32:32', 1),
(32, 1, 600.00, '2026-05-28 14:01:03', 1),
(33, 8, 700.00, '2026-05-28 14:01:59', 1),
(57, 8, 0.12, '2026-05-30 20:05:20', 1),
(88, 2, 1.00, '2026-06-03 15:24:41', 1),
(89, 1, 750.00, '2026-06-04 08:31:28', 1),
(105, 1, 567.00, '2026-06-08 21:19:55', 1),
(133, 4, 1250000000000.00, '2026-06-13 16:27:13', 1),
(134, 4, 1250.00, '2026-06-13 16:28:00', 1),
(135, 4, 1250.50, '2026-06-13 16:28:06', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_productos`
--

CREATE TABLE `categorias_productos` (
  `id_categoria_producto` int(11) NOT NULL,
  `nombre_categoria_producto` varchar(50) NOT NULL,
  `necesitan_materias_primas` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias_productos`
--

INSERT INTO `categorias_productos` (`id_categoria_producto`, `nombre_categoria_producto`, `necesitan_materias_primas`, `status`) VALUES
(1, 'FABRICADOS', 1, 1),
(2, 'NO FABRICADOS', 0, 1),
(3, 'INSUMOS', 1, 1),
(20, 'mmmm', 0, 0),
(21, 'NOMBRE', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `rif_cedula_cliente` varchar(20) NOT NULL,
  `razon_social_cliente` varchar(100) NOT NULL,
  `telefono_cliente` varchar(11) NOT NULL,
  `correo_cliente` varchar(150) NOT NULL,
  `direccion_cliente` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`rif_cedula_cliente`, `razon_social_cliente`, `telefono_cliente`, `correo_cliente`, `direccion_cliente`, `status`) VALUES
('30485684', 'ANDEROSN FREITEZ', '04169484649', 'andersonfreitez6@gmail.com', 'SANARE', 0),
('E25252525', 'ANDEROSN FREITE', '04169484647', 'andersonfreite5z6@gmail.com', '5454', 0),
('E30485684', 'ANDEROSN FREIT', '04169484645', 'andersonfreitez6@gmail.co', 'SANARE', 0),
('V30485682', 'NADA', '04141234567', 'andersonfreitez6@gmail.co', 'SANARE', 1),
('V30485683', 'ANDEROSN FREIT', '04161234568', 'andersonfreitez6@gmail.conk', 'SANARE', 0),
('V30485684', 'ANDEROSN FREITE', '04169484649', 'andersonfreitez6@gmail.com', 'SANARE', 1),
('V304856845', '54114', '04169484646', 'andersonfreitez66@gmail.com', 'hhhh', 0),
('V30485689', 'ANDEROSN FREITEZ', '04169484648', 'andersonfreitez6@gmail.con', 'David', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id_compra` varchar(20) NOT NULL,
  `rif_proveedor` varchar(20) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `fecha_compra` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id_compra`, `rif_proveedor`, `cedula_usuario`, `fecha_compra`, `status`) VALUES
('10', '30485684', '30485684', '2026-02-18 21:04:00', 0),
('11', '30485684', '30485684', '2026-05-18 10:00:00', 0),
('12', '30485684', '30485684', '2026-05-19 16:27:00', 0),
('13', '30485684', '30485684', '2026-05-30 10:13:00', 0),
('14', '30485684', '30485684', '2026-05-30 10:33:00', 0),
('15', '30485684', '30485684', '2026-05-30 18:08:00', 0),
('16', '30485684', '30485684', '2026-05-30 18:08:00', 0),
('17', '30485684', '30485684', '2026-06-07 14:06:00', 0),
('18', '30485684', '30485684', '2026-06-07 14:07:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comprobantes_pagos`
--

CREATE TABLE `comprobantes_pagos` (
  `id_comprobante_pago` int(11) NOT NULL,
  `id_pago` varchar(20) NOT NULL,
  `path_comprobante` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comprobantes_pagos`
--

INSERT INTO `comprobantes_pagos` (`id_comprobante_pago`, `id_pago`, `path_comprobante`, `status`) VALUES
(45, 'PAG-26158-00001-63', 'comprobantes_pagos_2026_06_08_21_23_00_68.jpg', 1),
(46, 'PAG-26159-00001-82', 'comprobantes_pagos_2026_06_09_11_02_36_100.jpg', 1),
(47, 'PAG-26160-00001-75', 'comprobantes_pagos_2026_06_10_17_23_16_62.jpg', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deliveries`
--

CREATE TABLE `deliveries` (
  `id_delivery` varchar(20) NOT NULL,
  `id_orden_entrega_presupuesto` varchar(20) NOT NULL,
  `id_direccion` int(11) NOT NULL,
  `cedula_repartidor` varchar(20) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `deliveries`
--

INSERT INTO `deliveries` (`id_delivery`, `id_orden_entrega_presupuesto`, `id_direccion`, `cedula_repartidor`, `status`) VALUES
('DELI-26158-00001-45', 'FACT-26158-00001-94', 64, NULL, 1),
('DELI-26159-00001-49', 'FACT-26159-00001-27', 65, 'V30485684', 1),
('DELI-26160-00001-18', 'FACT-26160-00001-42', 66, 'V30485684', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_pagos`
--

CREATE TABLE `detalles_pagos` (
  `id_detalle_pago` int(11) NOT NULL,
  `id_pago` varchar(20) NOT NULL,
  `id_metodo_pago` int(11) NOT NULL,
  `id_moneda` int(11) NOT NULL,
  `monto_pago` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalles_pagos`
--

INSERT INTO `detalles_pagos` (`id_detalle_pago`, `id_pago`, `id_metodo_pago`, `id_moneda`, `monto_pago`, `status`) VALUES
(153, 'PAG-26158-00001-63', 3, 1, 121.52, 1),
(161, 'PAG-26159-00001-82', 1, 2, 10000.00, 1),
(162, 'PAG-26160-00001-75', 2, 2, 4009.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direcciones`
--

CREATE TABLE `direcciones` (
  `id_direccion` int(11) NOT NULL,
  `id_latitud_direccion` int(11) NOT NULL,
  `id_longitud_direccion` int(11) NOT NULL,
  `id_ruta` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `direcciones`
--

INSERT INTO `direcciones` (`id_direccion`, `id_latitud_direccion`, `id_longitud_direccion`, `id_ruta`, `status`) VALUES
(28, 58, 54, 6, 1),
(29, 59, 55, 6, 1),
(30, 60, 56, 6, 1),
(31, 61, 57, 6, 1),
(32, 62, 58, 2, 1),
(33, 63, 59, 6, 1),
(49, 79, 75, 6, 1),
(50, 80, 76, 6, 1),
(51, 81, 77, 6, 1),
(52, 81, 77, 11, 1),
(53, 82, 78, 11, 1),
(54, 83, 79, 11, 1),
(55, 84, 80, 11, 1),
(56, 85, 81, 11, 1),
(59, 88, 84, 11, 1),
(60, 89, 85, 11, 1),
(61, 90, 86, 10, 1),
(62, 91, 87, 10, 1),
(63, 92, 88, 10, 1),
(64, 93, 89, 10, 1),
(65, 94, 90, 10, 1),
(66, 95, 91, 10, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas_envios`
--

CREATE TABLE `empresas_envios` (
  `id_empresa_envios` int(11) NOT NULL,
  `nombre_empresa` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresas_envios`
--

INSERT INTO `empresas_envios` (`id_empresa_envios`, `nombre_empresa`, `status`) VALUES
(1, 'ZOOMO', 0),
(2, 'ZOOM', 0),
(3, 'ZOOMh', 0),
(4, 'ZOOM', 0),
(5, 'ALIBABAB', 0),
(6, 'ZOOM', 1),
(7, 'ZOOM 2', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `envios_terceros`
--

CREATE TABLE `envios_terceros` (
  `id_envio_tercero` varchar(20) NOT NULL,
  `id_orden_entrega_presupuesto` varchar(20) NOT NULL,
  `id_sucursal_empresa_envios` int(11) NOT NULL,
  `precio_envio_tercero` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `latitudes_direcciones`
--

CREATE TABLE `latitudes_direcciones` (
  `id_latitud_direccion` int(11) NOT NULL,
  `coordenada_latitud` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `latitudes_direcciones`
--

INSERT INTO `latitudes_direcciones` (`id_latitud_direccion`, `coordenada_latitud`, `status`) VALUES
(58, '10.0626331', 1),
(59, '10.0630566', 1),
(60, '10.0632884', 1),
(61, '10.072417', 1),
(62, '10.0670697', 1),
(63, '10.039655', 1),
(79, '10.053758704512', 1),
(80, '9.8599964012514', 1),
(81, '10.123456', 1),
(82, '9.8595170293252', 1),
(83, '9.8600828102787', 1),
(84, '9.8600797557029', 1),
(85, '9.8603294672703', 1),
(88, '9.8616122673031', 1),
(89, '9.8616276041428', 1),
(90, '10.051815346816', 1),
(91, '10.050653', 1),
(92, '9.8613761802134', 1),
(93, '9.8600626405522', 1),
(94, '10.050305879849', 1),
(95, '10.05010893544', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `longitudes_direcciones`
--

CREATE TABLE `longitudes_direcciones` (
  `id_longitud_direccion` int(11) NOT NULL,
  `coordenada_longitud` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `longitudes_direcciones`
--

INSERT INTO `longitudes_direcciones` (`id_longitud_direccion`, `coordenada_longitud`, `status`) VALUES
(54, '-69.3654583', 1),
(55, '-69.3644222', 1),
(56, '-69.3648094', 1),
(57, '-69.3757301', 1),
(58, '-69.3441849', 1),
(59, '-69.4294816', 1),
(75, '-69.273433685303', 1),
(76, '-69.611978291735', 1),
(77, '10.123456', 1),
(78, '-69.611947939329', 1),
(79, '-69.611985050712', 1),
(80, '-69.611983887064', 1),
(81, '-69.612001773886', 1),
(84, '-69.612089384389', 1),
(85, '-69.612089481661', 1),
(86, '-69.366774559021', 1),
(87, '-69.362676', 1),
(88, '-69.612072884695', 1),
(89, '-69.611984815715', 1),
(90, '-69.360347986221', 1),
(91, '-69.326858521672', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_primas`
--

CREATE TABLE `materias_primas` (
  `id_materia_prima` varchar(20) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `nombre_materia_prima` varchar(50) NOT NULL,
  `stock_materia_prima` decimal(20,2) NOT NULL,
  `stock_minimo_materia_prima` decimal(20,2) NOT NULL,
  `precio_materia_prima` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias_primas`
--

INSERT INTO `materias_primas` (`id_materia_prima`, `id_unidad_medida`, `nombre_materia_prima`, `stock_materia_prima`, `stock_minimo_materia_prima`, `precio_materia_prima`, `status`) VALUES
('MATE-26123-00001-66', 2, 'HIPOCLORITO', 100.00, 10.00, 50.00, 1),
('MATE-26149-00001-45', 1, 'HIPOCLORITOm', 10.00, 8.00, 10.00, 0),
('MATE-26149-00002-90', 1, 'HIPOCLORITOSS', 222.00, 222.00, 111.00, 0),
('MATE-26157-00001-95', 2, 'HIPOCLORITOm', 1.00, 1.00, 1.00, 1);

--
-- Disparadores `materias_primas`
--
DELIMITER $$
CREATE TRIGGER `tg_guardar_precio_materia_prima_act` AFTER UPDATE ON `materias_primas` FOR EACH ROW BEGIN
IF NEW.PRECIO_MATERIA_PRIMA <> OLD.PRECIO_MATERIA_PRIMA THEN 
	CALL sp_guardar_precio_materia_prima(
    	NEW.id_materia_prima,
        NEW.precio_materia_prima
    );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tg_guardar_precio_materia_prima_reg` AFTER INSERT ON `materias_primas` FOR EACH ROW BEGIN
	CALL sp_guardar_precio_materia_prima(
    	NEW.id_materia_prima,
        NEW.precio_materia_prima
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_primas_compras`
--

CREATE TABLE `materias_primas_compras` (
  `id_materia_prima_compra` int(11) NOT NULL,
  `id_materia_prima` varchar(20) NOT NULL,
  `id_compra` varchar(20) NOT NULL,
  `cantidad_materia_prima` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias_primas_compras`
--

INSERT INTO `materias_primas_compras` (`id_materia_prima_compra`, `id_materia_prima`, `id_compra`, `cantidad_materia_prima`, `status`) VALUES
(1, 'MATE-26123-00001-66', '10', 2.00, 0),
(2, 'MATE-26123-00001-66', '10', 5.00, 0),
(10, 'MATE-26123-00001-66', '12', 12.00, 0),
(11, 'MATE-26123-00001-66', '12', 13.00, 0),
(12, 'MATE-26123-00001-66', '13', 20.00, 0),
(14, 'MATE-26123-00001-66', '14', 10.00, 0),
(15, 'MATE-26123-00001-66', '15', 1.00, 0),
(16, 'MATE-26123-00001-66', '16', 1.00, 0),
(17, 'MATE-26123-00001-66', '17', 2.00, 0),
(18, 'MATE-26123-00001-66', '18', 10.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_primas_producciones`
--

CREATE TABLE `materias_primas_producciones` (
  `id_materia_prima_produccion` int(11) NOT NULL,
  `id_materia_prima` varchar(20) NOT NULL,
  `id_produccion` varchar(20) NOT NULL,
  `cantidad_materia_prima` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_primas_productos`
--

CREATE TABLE `materias_primas_productos` (
  `id_materia_prima_producto` int(11) NOT NULL,
  `id_materia_prima` varchar(20) NOT NULL,
  `id_producto` varchar(20) NOT NULL,
  `cantidad_materia_prima` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias_primas_productos`
--

INSERT INTO `materias_primas_productos` (`id_materia_prima_producto`, `id_materia_prima`, `id_producto`, `cantidad_materia_prima`, `status`) VALUES
(170, 'MATE-26123-00001-66', 'PROD-26150-00001-39', 20.00, 0),
(171, 'MATE-26123-00001-66', 'PROD-26150-00001-39', 20.00, 0),
(172, 'MATE-26123-00001-66', 'PROD-26150-00001-39', 20.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodos_pagos`
--

CREATE TABLE `metodos_pagos` (
  `id_metodo_pago` int(11) NOT NULL,
  `nombre_metodo_pago` varchar(50) NOT NULL,
  `necesita_moneda` tinyint(1) NOT NULL,
  `necesita_banco_emisor` tinyint(1) NOT NULL,
  `necesita_banco_receptor` tinyint(1) NOT NULL,
  `necesita_referencia` tinyint(1) NOT NULL,
  `mostrar_ecommerce` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `metodos_pagos`
--

INSERT INTO `metodos_pagos` (`id_metodo_pago`, `nombre_metodo_pago`, `necesita_moneda`, `necesita_banco_emisor`, `necesita_banco_receptor`, `necesita_referencia`, `mostrar_ecommerce`, `status`) VALUES
(1, 'TRANSFERENCIA', 0, 1, 1, 1, 1, 1),
(2, 'PAGO MÓVIL', 0, 0, 1, 1, 1, 1),
(3, 'ZELLE', 1, 0, 0, 1, 1, 1),
(4, 'ZINLI', 1, 0, 0, 1, 0, 0),
(5, 'BINANCE', 1, 0, 0, 1, 1, 1),
(6, 'EFECTIVO', 1, 0, 0, 0, 0, 1),
(12, 'TRANSFERENCI', 1, 1, 1, 1, 0, 0),
(13, 'ZELLEL', 1, 1, 1, 1, 0, 0),
(17, 'TOPOmm', 1, 1, 1, 1, 1, 0),
(18, 'TOPOm', 1, 0, 0, 0, 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `monedas`
--

CREATE TABLE `monedas` (
  `id_moneda` int(11) NOT NULL,
  `nombre_moneda` varchar(20) NOT NULL,
  `simbolo_moneda` varchar(3) NOT NULL,
  `valor_moneda` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `monedas`
--

INSERT INTO `monedas` (`id_moneda`, `nombre_moneda`, `simbolo_moneda`, `valor_moneda`, `status`) VALUES
(1, 'DÓLAR', '$', 567.00, 1),
(2, 'BÓLIVAR', 'BS', 1.00, 1),
(3, 'EURO', '€', 600.00, 1),
(4, 'YUAN', '¥', 1250.50, 1),
(8, 'VALOR', 'U', 0.12, 0);

--
-- Disparadores `monedas`
--
DELIMITER $$
CREATE TRIGGER `tg_guardar_precio_moneda_act` AFTER UPDATE ON `monedas` FOR EACH ROW BEGIN
IF NEW.VALOR_MONEDA <> OLD.VALOR_MONEDA THEn
CALL sp_guardar_precio_moneda(
	NEW.id_moneda,
    NEW.valor_moneda
);
END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tg_guardar_precio_moneda_reg` AFTER INSERT ON `monedas` FOR EACH ROW BEGIN
CALL sp_guardar_precio_moneda(
	NEW.id_moneda,
    NEW.valor_moneda
);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tg_trigger` AFTER UPDATE ON `monedas` FOR EACH ROW BEGIN 

INSERT INTO cambios_iva(monto_cambio_iva)VALUES(NEW.valor_moneda);

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tg_trigger2` AFTER UPDATE ON `monedas` FOR EACH ROW BEGIN 

INSERT INTO cambios_iva(monto_cambio_iva)VALUES(NEW.valor_moneda);

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tgg` AFTER INSERT ON `monedas` FOR EACH ROW BEGIN
INSERT INTO cambios_monedas(id_moneda,valor_moneda) VALUES(NOW.id_moneda, NOW.valor_moneda);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_anomalos_materias_primas`
--

CREATE TABLE `movimientos_anomalos_materias_primas` (
  `id_movimiento_anomalo_materia_prima` int(11) NOT NULL,
  `id_materia_prima` varchar(20) NOT NULL,
  `cantidad_movimiento` int(11) NOT NULL,
  `tipo_movimiento` tinyint(1) NOT NULL,
  `motivo_movimiento` varchar(50) NOT NULL,
  `fecha_movimiento` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movimientos_anomalos_materias_primas`
--

INSERT INTO `movimientos_anomalos_materias_primas` (`id_movimiento_anomalo_materia_prima`, `id_materia_prima`, `cantidad_movimiento`, `tipo_movimiento`, `motivo_movimiento`, `fecha_movimiento`, `status`) VALUES
(0, 'MATE-26123-00001-66', 5, 1, 'llego', '2026-05-23 12:49:34', 1),
(0, 'MATE-26123-00001-66', 10, 1, 'asd', '2026-05-23 13:54:10', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_anomalos_productos`
--

CREATE TABLE `movimientos_anomalos_productos` (
  `id_movimiento_anomalo_producto` int(11) NOT NULL,
  `id_presentacion_producto` varchar(20) NOT NULL,
  `cantidad_movimiento` int(11) NOT NULL,
  `tipo_movimiento` tinyint(1) NOT NULL,
  `motivo_movimiento` varchar(50) NOT NULL,
  `fecha_movimiento` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes_entregas_presupuestos`
--

CREATE TABLE `ordenes_entregas_presupuestos` (
  `id_orden_entrega_presupuesto` varchar(20) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `id_cambio_iva` int(11) NOT NULL,
  `rif_cedula_cliente` varchar(20) NOT NULL,
  `fecha_orden_entrega_presupuesto` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ordenes_entregas_presupuestos`
--

INSERT INTO `ordenes_entregas_presupuestos` (`id_orden_entrega_presupuesto`, `cedula_usuario`, `id_cambio_iva`, `rif_cedula_cliente`, `fecha_orden_entrega_presupuesto`, `status`) VALUES
('FACT-26158-00001-94', '', 201, '30485684', '2026-06-08 21:23:00', 6),
('FACT-26159-00001-27', '', 234, '30485684', '2026-06-09 11:02:36', 8),
('FACT-26160-00001-42', '30485684', 234, '30485684', '2026-06-10 17:23:16', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` varchar(20) NOT NULL,
  `id_orden_entrega_presupuesto` varchar(20) NOT NULL,
  `fecha_pago` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id_pago`, `id_orden_entrega_presupuesto`, `fecha_pago`, `status`) VALUES
('PAG-26158-00001-63', 'FACT-26158-00001-94', '2026-06-08 00:00:00', 0),
('PAG-26159-00001-82', 'FACT-26159-00001-27', '2026-06-09 00:00:00', 1),
('PAG-26160-00001-75', 'FACT-26160-00001-42', '2026-06-10 00:00:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precios_materias_primas`
--

CREATE TABLE `precios_materias_primas` (
  `id_precio_materia_prima` int(11) NOT NULL,
  `id_materia_prima` varchar(20) NOT NULL,
  `precio_materia_prima` decimal(20,2) NOT NULL,
  `fecha_cambio` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `precios_materias_primas`
--

INSERT INTO `precios_materias_primas` (`id_precio_materia_prima`, `id_materia_prima`, `precio_materia_prima`, `fecha_cambio`, `status`) VALUES
(1, 'MATE-26123-00001-66', 10.00, '2026-05-27 18:28:29', 1),
(2, 'MATE-26123-00001-66', 50.00, '2026-05-28 14:29:54', 1),
(4, 'MATE-26149-00001-45', 10.00, '2026-05-30 11:21:55', 1),
(5, 'MATE-26149-00002-90', 111.00, '2026-05-30 18:44:44', 1),
(46, 'MATE-26157-00001-95', 1.00, '2026-06-07 13:17:51', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precios_productos`
--

CREATE TABLE `precios_productos` (
  `id_precio_producto` int(11) NOT NULL,
  `id_producto` varchar(20) NOT NULL,
  `precio_producto` decimal(20,2) NOT NULL,
  `fecha_cambio` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `precios_productos`
--

INSERT INTO `precios_productos` (`id_precio_producto`, `id_producto`, `precio_producto`, `fecha_cambio`, `status`) VALUES
(42, 'PROD-26150-00001-39', 1.00, '2026-05-31 12:51:12', 1),
(63, 'PROD-26150-00001-39', 2.00, '2026-06-04 10:44:02', 1),
(65, 'PROD-26150-00001-39', 3.00, '2026-06-04 11:04:31', 1),
(74, 'PROD-26150-00001-39', 1.00, '2026-06-08 21:20:19', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precios_rutas`
--

CREATE TABLE `precios_rutas` (
  `id_precio_ruta` int(11) NOT NULL,
  `id_ruta` int(11) NOT NULL,
  `precio_ruta` decimal(20,2) NOT NULL,
  `fecha_cambio` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `precios_rutas`
--

INSERT INTO `precios_rutas` (`id_precio_ruta`, `id_ruta`, `precio_ruta`, `fecha_cambio`, `status`) VALUES
(4, 9, 1.00, '2026-05-24 15:58:00', 1),
(5, 10, 2.00, '2026-05-24 15:58:22', 1),
(6, 11, 3.00, '2026-05-24 15:58:36', 1),
(7, 9, 1200.00, '2026-05-27 18:29:44', 1),
(43, 11, 5.00, '2026-06-04 10:04:22', 1),
(52, 11, 3.00, '2026-06-08 21:21:37', 1),
(53, 9, 0.50, '2026-06-08 21:21:46', 1),
(54, 10, 1.00, '2026-06-08 21:21:54', 1),
(55, 11, 2.00, '2026-06-08 21:22:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precios_servicios`
--

CREATE TABLE `precios_servicios` (
  `id_precio_servicio` int(11) NOT NULL,
  `id_servicio` varchar(20) NOT NULL,
  `precio_servicio` decimal(20,2) NOT NULL,
  `fecha_cambio` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `precios_servicios`
--

INSERT INTO `precios_servicios` (`id_precio_servicio`, `id_servicio`, `precio_servicio`, `fecha_cambio`, `status`) VALUES
(2, 'SERV-26125-00001-08', 22.00, '2026-05-28 14:34:00', 1),
(3, '', 10.00, '2026-05-28 14:35:20', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentaciones`
--

CREATE TABLE `presentaciones` (
  `id_presentacion` varchar(20) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `nombre_presentacion` varchar(50) NOT NULL,
  `cantidad_pmp` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `presentaciones`
--

INSERT INTO `presentaciones` (`id_presentacion`, `id_unidad_medida`, `nombre_presentacion`, `cantidad_pmp`, `status`) VALUES
('PRES-26123-00001-28', 2, 'POR LITRO', 1.00, 1),
('PRES-26159-00001-42', 2, 'BIDÓN', 20.00, 1),
('PRES-26159-00002-78', 2, 'PIPA', 200.00, 1),
('PRES-26159-00003-24', 2, 'GALÓN', 4.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentaciones_materias_primas`
--

CREATE TABLE `presentaciones_materias_primas` (
  `id_materia_prima_presentacion` int(11) NOT NULL,
  `id_presentacion` varchar(20) NOT NULL,
  `id_materia_prima` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `presentaciones_materias_primas`
--

INSERT INTO `presentaciones_materias_primas` (`id_materia_prima_presentacion`, `id_presentacion`, `id_materia_prima`, `status`) VALUES
(11, 'PRES-26123-00001-28', 'MATE-26123-00001-66', 1),
(17, 'PRES-26123-00001-28', 'MATE-26149-00002-90', 0),
(58, 'PRES-26123-00001-28', 'MATE-26149-00001-45', 0),
(59, 'PRES-26123-00001-28', 'MATE-26157-00001-95', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentaciones_productos`
--

CREATE TABLE `presentaciones_productos` (
  `id_presentacion_producto` varchar(20) NOT NULL,
  `id_producto` varchar(20) NOT NULL,
  `id_presentacion` varchar(20) NOT NULL,
  `mostrar_ecommerce` tinyint(1) NOT NULL,
  `foto_presentacion` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `presentaciones_productos`
--

INSERT INTO `presentaciones_productos` (`id_presentacion_producto`, `id_producto`, `id_presentacion`, `mostrar_ecommerce`, `foto_presentacion`, `status`) VALUES
('PRPR-26158-00001-06', 'PROD-26150-00001-39', 'PRES-26123-00001-28', 1, '', 0),
('PRPR-26159-00001-70', 'PROD-26150-00001-39', 'PRES-26159-00001-42', 1, 'presentaciones_productos_2026-06-10_11_27_45.jpg?v=2026-06-10_18_03_23', 1),
('PRPR-26159-00002-84', 'PROD-26150-00001-39', 'PRES-26159-00002-78', 1, 'presentaciones_productos_2026-06-10_18_06_25.jpg?v=2026-06-10_18_06_25', 1),
('PRPR-26159-00003-15', 'PROD-26150-00001-39', 'PRES-26159-00003-24', 1, '', 0),
('PRPR-26160-00001-60', 'PROD-26150-00001-39', 'PRES-26123-00001-28', 1, 'presentaciones_productos_2026-06-10_11_15_34.jpg?v=2026-06-10_17_54_04', 1),
('PRPR-26160-00002-49', 'PROD-26150-00001-39', 'PRES-26159-00003-24', 0, 'presentaciones_productos_2026-06-10_11_15_21.jpg?v=2026-06-10_18_04_00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producciones`
--

CREATE TABLE `producciones` (
  `id_produccion` varchar(20) NOT NULL,
  `fecha_produccion` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producciones`
--

INSERT INTO `producciones` (`id_produccion`, `fecha_produccion`, `status`) VALUES
('PROD-26125-00001-76', '2026-05-06 10:36:22', 1),
('PROD-26137-00001-39', '2026-05-18 09:22:25', 1),
('PROD-26149-00001-99', '2026-05-30 13:29:38', 1),
('PROD-26150-00001-73', '2026-05-31 12:56:16', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` varchar(20) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `id_categoria_producto` int(11) NOT NULL,
  `nombre_producto` varchar(50) NOT NULL,
  `precio_producto` decimal(20,2) NOT NULL,
  `stock_producto` decimal(20,2) NOT NULL,
  `stock_minimo_producto` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `id_unidad_medida`, `id_categoria_producto`, `nombre_producto`, `precio_producto`, `stock_producto`, `stock_minimo_producto`, `status`) VALUES
('PROD-26150-00001-39', 2, 1, 'CLORO', 1.00, 9.00, 2.00, 1);

--
-- Disparadores `productos`
--
DELIMITER $$
CREATE TRIGGER `tg_guardar_precio_producto_act` AFTER UPDATE ON `productos` FOR EACH ROW BEGIN
IF NEW.precio_producto <> OLD.precio_producto THEN
	CALL sp_guardar_precio_producto(
    	NEW.id_producto,
        NEW.precio_producto
    );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tg_guardar_precio_producto_reg` AFTER INSERT ON `productos` FOR EACH ROW BEGIN
	CALL sp_guardar_precio_producto(
    	NEW.id_producto,
        NEW.precio_producto
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_compras`
--

CREATE TABLE `productos_compras` (
  `id_producto_compra` int(11) NOT NULL,
  `id_presentacion_producto` varchar(20) NOT NULL,
  `id_compra` varchar(20) NOT NULL,
  `cantidad_producto` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_ordenes_entregas_presupuestos`
--

CREATE TABLE `productos_ordenes_entregas_presupuestos` (
  `id_producto_factura` int(11) NOT NULL,
  `id_orden_entrega_presupuesto` varchar(20) NOT NULL,
  `id_presentacion_producto` varchar(20) NOT NULL,
  `cantidad_producto` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos_ordenes_entregas_presupuestos`
--

INSERT INTO `productos_ordenes_entregas_presupuestos` (`id_producto_factura`, `id_orden_entrega_presupuesto`, `id_presentacion_producto`, `cantidad_producto`, `status`) VALUES
(194, 'FACT-26158-00001-94', 'PRPR-26158-00001-06', 2.00, 1),
(217, 'FACT-26159-00001-27', 'PRPR-26158-00001-06', 1.00, 1),
(218, 'FACT-26160-00001-42', 'PRPR-26160-00001-60', 1.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_producciones`
--

CREATE TABLE `productos_producciones` (
  `id_producto_produccion` int(11) NOT NULL,
  `id_produccion` varchar(20) NOT NULL,
  `id_producto` varchar(20) NOT NULL,
  `cantidad_producida` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos_producciones`
--

INSERT INTO `productos_producciones` (`id_producto_produccion`, `id_produccion`, `id_producto`, `cantidad_producida`, `status`) VALUES
(105, 'PROD-26150-00001-73', 'PROD-26150-00001-39', 10.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_servicios`
--

CREATE TABLE `productos_servicios` (
  `id_producto_servicio` int(11) NOT NULL,
  `id_producto` varchar(20) NOT NULL,
  `id_servicio` varchar(20) NOT NULL,
  `cantidad_producto` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `rif_proveedor` varchar(20) NOT NULL,
  `razon_social_proveedor` varchar(150) NOT NULL,
  `telefono_proveedor` varchar(11) NOT NULL,
  `correo_proveedor` varchar(150) NOT NULL,
  `direccion_proveedor` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`rif_proveedor`, `razon_social_proveedor`, `telefono_proveedor`, `correo_proveedor`, `direccion_proveedor`, `status`) VALUES
('123456789', 'ANDERSO', '04169484640', 'andersonfreitez60@gmail.com', 'HHHHBHBH', 0),
('1234567890', 'ANDERSONM', '04169484641', 'andersonfreitez6@gmail.com', 'DIRECCION', 0),
('30485684', 'ANDERSON', '04169484649', 'ANDERSONFREITEZ@GMAIL.COM', 'SANANA', 1),
('J12345611', 'ANDERSON9K', '04169484699', 'andersonfreitez6@gmail.co', 'kmkmkm', 0),
('J12345678', 'ANDERS', '04169484688', 'andersonfreitez9@gmail.com', 'eejejej', 0),
('J30485684', 'ANDERSON9', '04169484645', 'andersonfreitez61@gmail.co', 'jxjnjnj', 0),
('V1234567899', 'ANDERSONNN', '04169484647', 'andersonfreitez677@gmail.com', 'SANARE', 1),
('V30485684', 'ANDERSONNFA', '04169484648', 'andersonfreitez6@gmail.com', 'SANARESS', 1),
('V30485685', 'ANDERSON', '04169484640', 'andersonfreitez69@gmail.com', 'SANARE', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `referencias_detalles_pagos`
--

CREATE TABLE `referencias_detalles_pagos` (
  `id_referencia_detalle_pago` int(11) NOT NULL,
  `id_detalle_pago` int(11) NOT NULL,
  `referencia_pago` int(6) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `referencias_detalles_pagos`
--

INSERT INTO `referencias_detalles_pagos` (`id_referencia_detalle_pago`, `id_detalle_pago`, `referencia_pago`, `status`) VALUES
(102, 153, 123456, 1),
(110, 161, 123456, 1),
(111, 162, 123456, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `repartidores`
--

CREATE TABLE `repartidores` (
  `cedula_repartidor` varchar(20) NOT NULL,
  `nombre_repartidor` varchar(50) NOT NULL,
  `apellido_repartidor` varchar(50) NOT NULL,
  `telefono_repartidor` varchar(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `repartidores`
--

INSERT INTO `repartidores` (`cedula_repartidor`, `nombre_repartidor`, `apellido_repartidor`, `telefono_repartidor`, `status`) VALUES
('V12344567', 'JOSE', 'JIMENEZ', '04161234567', 1),
('V30485654', 'ANDER', 'FREITEZ', '04169784646', 1),
('V30485683', 'ANDERSON', 'FREITEZ', '04169484648', 0),
('V30485684', 'ANDERSONn', 'FREITEZ', '04169484649', 1),
('V304856849', 'ANDERSON', 'FREITEZ', '04169484640', 0),
('V30485685', 'ANDERSON', 'FREITEZ', '04169484647', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rutas`
--

CREATE TABLE `rutas` (
  `id_ruta` int(11) NOT NULL,
  `nombre_ruta` varchar(50) NOT NULL,
  `precio_ruta` decimal(20,2) NOT NULL,
  `minimo_km_ruta` decimal(20,2) NOT NULL,
  `maximo_km_ruta` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rutas`
--

INSERT INTO `rutas` (`id_ruta`, `nombre_ruta`, `precio_ruta`, `minimo_km_ruta`, `maximo_km_ruta`, `status`) VALUES
(1, 'CERCANO', 1.50, 0.00, 5.00, 0),
(2, 'LEJANO', 2.50, 6.00, 10.00, 0),
(6, 'TERCERO', 3.50, 11.00, 100.00, 0),
(9, 'CERCANO', 0.50, 0.00, 5.00, 1),
(10, 'LEJANO', 1.00, 6.00, 10.00, 1),
(11, 'TERCERO', 2.00, 11.00, 100.00, 1);

--
-- Disparadores `rutas`
--
DELIMITER $$
CREATE TRIGGER `tg_guardar_precio_ruta_reg` AFTER INSERT ON `rutas` FOR EACH ROW BEGIN
	CALL sp_guardar_precio_ruta(
    	NEW.id_ruta,
        NEW.precio_ruta
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tg_registrar_precio_ruta_act` AFTER UPDATE ON `rutas` FOR EACH ROW BEGIN
	IF NEW.precio_ruta <> OLD.precio_ruta THEN
	CALL sp_guardar_precio_ruta(
    	NEW.id_ruta,
        NEW.precio_ruta
    );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id_servicio` varchar(20) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `nombre_servicio` varchar(100) NOT NULL,
  `precio_servicio` decimal(20,2) NOT NULL,
  `mostrar_ecommerce` tinyint(1) NOT NULL,
  `foto_servicio` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id_servicio`, `id_unidad_medida`, `nombre_servicio`, `precio_servicio`, `mostrar_ecommerce`, `foto_servicio`, `status`) VALUES
('', 1, 'LIMPIEZA', 10.00, 1, '', 1),
('SERV-26125-00001-08', 2, 'FUMIGACION', 22.00, 0, '', 0);

--
-- Disparadores `servicios`
--
DELIMITER $$
CREATE TRIGGER `tg_guardar_precio_servicio_act` AFTER UPDATE ON `servicios` FOR EACH ROW BEGIN
IF NEW.precio_servicio <> OLD.precio_servicio THEN
	CALL sp_guardar_precio_servicio(
    	NEW.id_servicio,
        NEW.precio_servicio
    );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tg_guardar_precio_servicio_reg` AFTER INSERT ON `servicios` FOR EACH ROW BEGIN
	CALL sp_guardar_precio_servicio(
    	NEW.id_servicio,
        NEW.precio_servicio
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios_ordenes_entregas_presupuestos`
--

CREATE TABLE `servicios_ordenes_entregas_presupuestos` (
  `id_servicio_factura` int(11) NOT NULL,
  `id_orden_entrega_presupuesto` varchar(20) NOT NULL,
  `id_servicio` varchar(20) NOT NULL,
  `id_direccion` int(11) NOT NULL,
  `cantidad_servicio` decimal(20,2) NOT NULL,
  `fecha_ejecucion` datetime NOT NULL,
  `es_precio_mapfre` tinyint(1) NOT NULL,
  `precio_servicio_mapfre` decimal(20,2) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios_ordenes_entregas_presupuestos`
--

INSERT INTO `servicios_ordenes_entregas_presupuestos` (`id_servicio_factura`, `id_orden_entrega_presupuesto`, `id_servicio`, `id_direccion`, `cantidad_servicio`, `fecha_ejecucion`, `es_precio_mapfre`, `precio_servicio_mapfre`, `status`) VALUES
(1, 'FACT-26158-00001-94', 'SERV-26125-00001-08', 29, 2.00, '2026-06-12 09:09:31', 0, NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursales_empresas_envios`
--

CREATE TABLE `sucursales_empresas_envios` (
  `id_sucursal_empresa_envios` int(11) NOT NULL,
  `id_empresa_envios` int(11) NOT NULL,
  `id_direccion` int(11) NOT NULL,
  `nombre_sucursal_empresa` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sucursales_empresas_envios`
--

INSERT INTO `sucursales_empresas_envios` (`id_sucursal_empresa_envios`, `id_empresa_envios`, `id_direccion`, `nombre_sucursal_empresa`, `status`) VALUES
(3, 6, 28, 'METROPOLIS', 0),
(4, 6, 28, 'METROPOLIS', 0),
(5, 6, 28, 'METROPOLISA', 0),
(6, 6, 28, 'METROPOLIS', 0),
(7, 6, 30, 'BRM', 1),
(8, 6, 31, 'AV INDUSTRIAS', 1),
(9, 6, 32, 'LARAPLACE', 1),
(10, 6, 33, 'AEROPUERTO JACINTO LARA', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades_medidas`
--

CREATE TABLE `unidades_medidas` (
  `id_unidad_medida` int(11) NOT NULL,
  `nombre_unidad_medida` varchar(50) NOT NULL,
  `simbolo_unidad_medida` varchar(3) NOT NULL,
  `equivalencia_ub` decimal(20,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `unidades_medidas`
--

INSERT INTO `unidades_medidas` (`id_unidad_medida`, `nombre_unidad_medida`, `simbolo_unidad_medida`, `equivalencia_ub`, `status`) VALUES
(1, 'KILO(S)', 'KG', 1000.00, 1),
(2, 'LITRO(S)', 'L', 1000.00, 1),
(3, 'MILLA', 'ML', 1000000.00, 0),
(4, 'MILL', 'ML', 1000000.00, 0),
(5, 'MILLA', 'M', 1000000.00, 0),
(6, 'MILLM', 'MIL', 1000000.00, 0),
(7, 'MILLA', 'ML', 2.00, 0),
(8, 'MILLAS', 'MLA', 1000000.00, 0),
(9, 'METRO CUADRADO', 'M2', 1000.00, 1),
(10, 'METROM', 'M', 1000.00, 0),
(11, 'METRO CUADRADOm', 'M2m', 1000.00, 0);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_cambios_iva_todos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_cambios_iva_todos` (
`id_cambio_iva` int(11)
,`monto_cambio_iva` decimal(20,2)
,`fecha_cambio_iva` datetime
,`status` tinyint(1)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_cambios_monedas_todos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_cambios_monedas_todos` (
`id_cambio_moneda` int(11)
,`nombre_moneda` varchar(20)
,`valor_moneda` decimal(20,2)
,`fecha_cambio` datetime
,`status` tinyint(1)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_categorias_productos_todas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_categorias_productos_todas` (
`id_categoria_producto` int(11)
,`nombre_categoria_producto` varchar(50)
,`necesitan_materias_primas` tinyint(1)
,`status` tinyint(1)
,`cantidad_productos` bigint(21)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_clientes_todos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_clientes_todos` (
`rif_cedula_cliente` varchar(20)
,`razon_social_cliente` varchar(100)
,`telefono_cliente` varchar(11)
,`correo_cliente` varchar(150)
,`direccion_cliente` varchar(255)
,`status` tinyint(1)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_empresas_envios_todas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_empresas_envios_todas` (
`id_empresa_envios` int(11)
,`nombre_empresa` varchar(50)
,`status` tinyint(1)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_repartidores_todos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_repartidores_todos` (
`cedula_repartidor` varchar(20)
,`nombre_repartidor` varchar(50)
,`apellido_repartidor` varchar(50)
,`telefono_repartidor` varchar(11)
,`status` tinyint(1)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_unidades_medidas_todas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_unidades_medidas_todas` (
`id_unidad_medida` int(11)
,`nombre_unidad_medida` varchar(50)
,`simbolo_unidad_medida` varchar(3)
,`equivalencia_ub` decimal(20,2)
,`status` tinyint(1)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `v_cambios_iva_todos`
--
DROP TABLE IF EXISTS `v_cambios_iva_todos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_cambios_iva_todos`  AS SELECT `cambios_iva`.`id_cambio_iva` AS `id_cambio_iva`, `cambios_iva`.`monto_cambio_iva` AS `monto_cambio_iva`, `cambios_iva`.`fecha_cambio_iva` AS `fecha_cambio_iva`, `cambios_iva`.`status` AS `status` FROM `cambios_iva` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_cambios_monedas_todos`
--
DROP TABLE IF EXISTS `v_cambios_monedas_todos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_cambios_monedas_todos`  AS SELECT `cm`.`id_cambio_moneda` AS `id_cambio_moneda`, `mo`.`nombre_moneda` AS `nombre_moneda`, `cm`.`valor_moneda` AS `valor_moneda`, `cm`.`fecha_cambio` AS `fecha_cambio`, `cm`.`status` AS `status` FROM (`cambios_monedas` `cm` join `monedas` `mo` on(`cm`.`id_moneda` = `mo`.`id_moneda`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_categorias_productos_todas`
--
DROP TABLE IF EXISTS `v_categorias_productos_todas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_categorias_productos_todas`  AS SELECT `c`.`id_categoria_producto` AS `id_categoria_producto`, `c`.`nombre_categoria_producto` AS `nombre_categoria_producto`, `c`.`necesitan_materias_primas` AS `necesitan_materias_primas`, `c`.`status` AS `status`, count(`p`.`id_producto`) AS `cantidad_productos` FROM (`categorias_productos` `c` left join `productos` `p` on(`c`.`id_categoria_producto` = `p`.`id_categoria_producto` and `p`.`status` = 1)) GROUP BY `c`.`id_categoria_producto` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_clientes_todos`
--
DROP TABLE IF EXISTS `v_clientes_todos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_clientes_todos`  AS SELECT `clientes`.`rif_cedula_cliente` AS `rif_cedula_cliente`, `clientes`.`razon_social_cliente` AS `razon_social_cliente`, `clientes`.`telefono_cliente` AS `telefono_cliente`, `clientes`.`correo_cliente` AS `correo_cliente`, `clientes`.`direccion_cliente` AS `direccion_cliente`, `clientes`.`status` AS `status` FROM `clientes` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_empresas_envios_todas`
--
DROP TABLE IF EXISTS `v_empresas_envios_todas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_empresas_envios_todas`  AS SELECT `empresas_envios`.`id_empresa_envios` AS `id_empresa_envios`, `empresas_envios`.`nombre_empresa` AS `nombre_empresa`, `empresas_envios`.`status` AS `status` FROM `empresas_envios` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_repartidores_todos`
--
DROP TABLE IF EXISTS `v_repartidores_todos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_repartidores_todos`  AS SELECT `repartidores`.`cedula_repartidor` AS `cedula_repartidor`, `repartidores`.`nombre_repartidor` AS `nombre_repartidor`, `repartidores`.`apellido_repartidor` AS `apellido_repartidor`, `repartidores`.`telefono_repartidor` AS `telefono_repartidor`, `repartidores`.`status` AS `status` FROM `repartidores` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_unidades_medidas_todas`
--
DROP TABLE IF EXISTS `v_unidades_medidas_todas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_unidades_medidas_todas`  AS SELECT `unidades_medidas`.`id_unidad_medida` AS `id_unidad_medida`, `unidades_medidas`.`nombre_unidad_medida` AS `nombre_unidad_medida`, `unidades_medidas`.`simbolo_unidad_medida` AS `simbolo_unidad_medida`, `unidades_medidas`.`equivalencia_ub` AS `equivalencia_ub`, `unidades_medidas`.`status` AS `status` FROM `unidades_medidas` ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bancos`
--
ALTER TABLE `bancos`
  ADD PRIMARY KEY (`id_banco`);

--
-- Indices de la tabla `bancos_detalles_pagos`
--
ALTER TABLE `bancos_detalles_pagos`
  ADD PRIMARY KEY (`id_banco_detalle_pago`),
  ADD KEY `id_detalle_pago_bancos_detalles_pagos_fk` (`id_detalle_pago`),
  ADD KEY `id_banco_bancos_detalles_pagos_fk` (`id_banco`);

--
-- Indices de la tabla `cambios_iva`
--
ALTER TABLE `cambios_iva`
  ADD PRIMARY KEY (`id_cambio_iva`),
  ADD KEY `fecha_cambio_iva_indice` (`fecha_cambio_iva`);

--
-- Indices de la tabla `cambios_monedas`
--
ALTER TABLE `cambios_monedas`
  ADD PRIMARY KEY (`id_cambio_moneda`),
  ADD KEY `id_moneda_cambios_monedas_fk` (`id_moneda`),
  ADD KEY `fecha_cambio_indice` (`fecha_cambio`) USING BTREE;

--
-- Indices de la tabla `categorias_productos`
--
ALTER TABLE `categorias_productos`
  ADD PRIMARY KEY (`id_categoria_producto`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`rif_cedula_cliente`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id_compra`),
  ADD KEY `rif_proveedor_compras_fk` (`rif_proveedor`),
  ADD KEY `fecha_compra_indice` (`fecha_compra`) USING BTREE;

--
-- Indices de la tabla `comprobantes_pagos`
--
ALTER TABLE `comprobantes_pagos`
  ADD PRIMARY KEY (`id_comprobante_pago`),
  ADD KEY `id_pago_comprobantes_pagos_fk` (`id_pago`);

--
-- Indices de la tabla `deliveries`
--
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`id_delivery`),
  ADD KEY `id_direccion_deliveries_fk` (`id_direccion`),
  ADD KEY `cedula_repartidor_deliveries_fk` (`cedula_repartidor`),
  ADD KEY `id_orden_entrega_presupuesto_delivery_fk` (`id_orden_entrega_presupuesto`);

--
-- Indices de la tabla `detalles_pagos`
--
ALTER TABLE `detalles_pagos`
  ADD PRIMARY KEY (`id_detalle_pago`),
  ADD KEY `id_pago_detalles_pagos_fk` (`id_pago`),
  ADD KEY `id_metodo_pago_detalles_pagos_fk` (`id_metodo_pago`),
  ADD KEY `id_moneda_detalles_pagos_fk` (`id_moneda`);

--
-- Indices de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD PRIMARY KEY (`id_direccion`),
  ADD KEY `id_ruta_direcciones_fk` (`id_ruta`),
  ADD KEY `id_latitud_direccion_direcciones_fk` (`id_latitud_direccion`),
  ADD KEY `id_longitud_direccion_direcciones_fk` (`id_longitud_direccion`);

--
-- Indices de la tabla `empresas_envios`
--
ALTER TABLE `empresas_envios`
  ADD PRIMARY KEY (`id_empresa_envios`);

--
-- Indices de la tabla `envios_terceros`
--
ALTER TABLE `envios_terceros`
  ADD PRIMARY KEY (`id_envio_tercero`),
  ADD KEY `id_sucursal_empresa_envios_envios_terceros_fk` (`id_sucursal_empresa_envios`),
  ADD KEY `id_orden_entrega_presupuesto_envios_terceros_fk` (`id_orden_entrega_presupuesto`);

--
-- Indices de la tabla `latitudes_direcciones`
--
ALTER TABLE `latitudes_direcciones`
  ADD PRIMARY KEY (`id_latitud_direccion`);

--
-- Indices de la tabla `longitudes_direcciones`
--
ALTER TABLE `longitudes_direcciones`
  ADD PRIMARY KEY (`id_longitud_direccion`);

--
-- Indices de la tabla `materias_primas`
--
ALTER TABLE `materias_primas`
  ADD PRIMARY KEY (`id_materia_prima`),
  ADD KEY `id_unidad_medida_materias_primas_fk` (`id_unidad_medida`);

--
-- Indices de la tabla `materias_primas_compras`
--
ALTER TABLE `materias_primas_compras`
  ADD PRIMARY KEY (`id_materia_prima_compra`),
  ADD KEY `id_materia_prima_materias_primas_compras_fk` (`id_materia_prima`),
  ADD KEY `id_compra_materias_primas_compras_fk` (`id_compra`);

--
-- Indices de la tabla `materias_primas_producciones`
--
ALTER TABLE `materias_primas_producciones`
  ADD PRIMARY KEY (`id_materia_prima_produccion`),
  ADD KEY `id_materia_prima_materias_primas_producciones_fk` (`id_materia_prima`),
  ADD KEY `id_produccion_materias_primas_producciones_fk` (`id_produccion`);

--
-- Indices de la tabla `materias_primas_productos`
--
ALTER TABLE `materias_primas_productos`
  ADD PRIMARY KEY (`id_materia_prima_producto`),
  ADD KEY `id_materia_prima_materias_primas_productos_fk` (`id_materia_prima`),
  ADD KEY `id_producto_materias_primas_productos_fk` (`id_producto`);

--
-- Indices de la tabla `metodos_pagos`
--
ALTER TABLE `metodos_pagos`
  ADD PRIMARY KEY (`id_metodo_pago`);

--
-- Indices de la tabla `monedas`
--
ALTER TABLE `monedas`
  ADD PRIMARY KEY (`id_moneda`);

--
-- Indices de la tabla `movimientos_anomalos_materias_primas`
--
ALTER TABLE `movimientos_anomalos_materias_primas`
  ADD KEY `id_materia_prima_movimientos_anomalos_materias_primas_fk` (`id_materia_prima`);

--
-- Indices de la tabla `movimientos_anomalos_productos`
--
ALTER TABLE `movimientos_anomalos_productos`
  ADD PRIMARY KEY (`id_movimiento_anomalo_producto`),
  ADD KEY `id_presentacion_producto_movimientos_anomalos_productos_fk` (`id_presentacion_producto`);

--
-- Indices de la tabla `ordenes_entregas_presupuestos`
--
ALTER TABLE `ordenes_entregas_presupuestos`
  ADD PRIMARY KEY (`id_orden_entrega_presupuesto`),
  ADD KEY `rif_cedula_cliente_venta_fk` (`rif_cedula_cliente`),
  ADD KEY `id_cambio_iva_venta` (`id_cambio_iva`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_venta_pago_fk` (`id_orden_entrega_presupuesto`);

--
-- Indices de la tabla `precios_materias_primas`
--
ALTER TABLE `precios_materias_primas`
  ADD PRIMARY KEY (`id_precio_materia_prima`),
  ADD KEY `id_materia_prima_precios_materias_primas_fk` (`id_materia_prima`);

--
-- Indices de la tabla `precios_productos`
--
ALTER TABLE `precios_productos`
  ADD PRIMARY KEY (`id_precio_producto`),
  ADD KEY `id_producto_precios_productos_fk` (`id_producto`);

--
-- Indices de la tabla `precios_rutas`
--
ALTER TABLE `precios_rutas`
  ADD PRIMARY KEY (`id_precio_ruta`),
  ADD KEY `id_ruta_precios_rutas_fk` (`id_ruta`);

--
-- Indices de la tabla `precios_servicios`
--
ALTER TABLE `precios_servicios`
  ADD PRIMARY KEY (`id_precio_servicio`),
  ADD KEY `id_servicio_precios_servicios_fk` (`id_servicio`);

--
-- Indices de la tabla `presentaciones`
--
ALTER TABLE `presentaciones`
  ADD PRIMARY KEY (`id_presentacion`),
  ADD KEY `id_unidad_medida_presentaciones_fk` (`id_unidad_medida`);

--
-- Indices de la tabla `presentaciones_materias_primas`
--
ALTER TABLE `presentaciones_materias_primas`
  ADD PRIMARY KEY (`id_materia_prima_presentacion`),
  ADD KEY `id_materia_prima_materias_primas_presentaciones_fk` (`id_materia_prima`),
  ADD KEY `id_presentacion_materias_primas_presentaciones_fk` (`id_presentacion`);

--
-- Indices de la tabla `presentaciones_productos`
--
ALTER TABLE `presentaciones_productos`
  ADD PRIMARY KEY (`id_presentacion_producto`),
  ADD KEY `id_presentacion_productos_presentaciones_fk` (`id_presentacion`),
  ADD KEY `id_producto_productos_presentaciones_fk` (`id_producto`);

--
-- Indices de la tabla `producciones`
--
ALTER TABLE `producciones`
  ADD PRIMARY KEY (`id_produccion`),
  ADD KEY `fecha_produccion_indice` (`fecha_produccion`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_unidad_medida_productos_fk` (`id_unidad_medida`),
  ADD KEY `id_categoria_producto_productos_fk` (`id_categoria_producto`);

--
-- Indices de la tabla `productos_compras`
--
ALTER TABLE `productos_compras`
  ADD PRIMARY KEY (`id_producto_compra`),
  ADD KEY `id_producto_productos_compras_fk` (`id_presentacion_producto`),
  ADD KEY `id_compra_productos_compras_fk` (`id_compra`);

--
-- Indices de la tabla `productos_ordenes_entregas_presupuestos`
--
ALTER TABLE `productos_ordenes_entregas_presupuestos`
  ADD PRIMARY KEY (`id_producto_factura`),
  ADD KEY `id_presentacion_producto_poep_fk` (`id_presentacion_producto`),
  ADD KEY `id_orden_entrega_presupuesto_poep_fk` (`id_orden_entrega_presupuesto`);

--
-- Indices de la tabla `productos_producciones`
--
ALTER TABLE `productos_producciones`
  ADD PRIMARY KEY (`id_producto_produccion`),
  ADD KEY `id_produccion_productos_producciones_fk` (`id_produccion`),
  ADD KEY `id_producto_productos_producciones_fk` (`id_producto`);

--
-- Indices de la tabla `productos_servicios`
--
ALTER TABLE `productos_servicios`
  ADD PRIMARY KEY (`id_producto_servicio`),
  ADD KEY `id_producto_productos_soep_fk` (`id_producto`),
  ADD KEY `id_servicio_factura_psoep_fk` (`id_servicio`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`rif_proveedor`);

--
-- Indices de la tabla `referencias_detalles_pagos`
--
ALTER TABLE `referencias_detalles_pagos`
  ADD PRIMARY KEY (`id_referencia_detalle_pago`),
  ADD KEY `id_detalle_pago_referencias_detalles_pagos_fk` (`id_detalle_pago`);

--
-- Indices de la tabla `repartidores`
--
ALTER TABLE `repartidores`
  ADD PRIMARY KEY (`cedula_repartidor`);

--
-- Indices de la tabla `rutas`
--
ALTER TABLE `rutas`
  ADD PRIMARY KEY (`id_ruta`),
  ADD KEY `minimo_km_ruta_indice` (`minimo_km_ruta`),
  ADD KEY `maximo_km_ruta_indice` (`maximo_km_ruta`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id_servicio`),
  ADD KEY `id_unidad_medida_servicios_fk` (`id_unidad_medida`);

--
-- Indices de la tabla `servicios_ordenes_entregas_presupuestos`
--
ALTER TABLE `servicios_ordenes_entregas_presupuestos`
  ADD PRIMARY KEY (`id_servicio_factura`),
  ADD KEY `id_orden_entrega_presupuesto_soep_fk` (`id_orden_entrega_presupuesto`),
  ADD KEY `id_servicio_soep_fk` (`id_servicio`),
  ADD KEY `id_direccion_soep_fk` (`id_direccion`);

--
-- Indices de la tabla `sucursales_empresas_envios`
--
ALTER TABLE `sucursales_empresas_envios`
  ADD PRIMARY KEY (`id_sucursal_empresa_envios`),
  ADD KEY `id_empresa_envios_sucursales_empresas_envios_fk` (`id_empresa_envios`),
  ADD KEY `id_direccion_sucursales_empresas_envios_fk` (`id_direccion`);

--
-- Indices de la tabla `unidades_medidas`
--
ALTER TABLE `unidades_medidas`
  ADD PRIMARY KEY (`id_unidad_medida`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bancos`
--
ALTER TABLE `bancos`
  MODIFY `id_banco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2849;

--
-- AUTO_INCREMENT de la tabla `bancos_detalles_pagos`
--
ALTER TABLE `bancos_detalles_pagos`
  MODIFY `id_banco_detalle_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=192;

--
-- AUTO_INCREMENT de la tabla `cambios_iva`
--
ALTER TABLE `cambios_iva`
  MODIFY `id_cambio_iva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=253;

--
-- AUTO_INCREMENT de la tabla `cambios_monedas`
--
ALTER TABLE `cambios_monedas`
  MODIFY `id_cambio_moneda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT de la tabla `categorias_productos`
--
ALTER TABLE `categorias_productos`
  MODIFY `id_categoria_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de la tabla `comprobantes_pagos`
--
ALTER TABLE `comprobantes_pagos`
  MODIFY `id_comprobante_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT de la tabla `detalles_pagos`
--
ALTER TABLE `detalles_pagos`
  MODIFY `id_detalle_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

--
-- AUTO_INCREMENT de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  MODIFY `id_direccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT de la tabla `empresas_envios`
--
ALTER TABLE `empresas_envios`
  MODIFY `id_empresa_envios` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `latitudes_direcciones`
--
ALTER TABLE `latitudes_direcciones`
  MODIFY `id_latitud_direccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT de la tabla `longitudes_direcciones`
--
ALTER TABLE `longitudes_direcciones`
  MODIFY `id_longitud_direccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT de la tabla `materias_primas_compras`
--
ALTER TABLE `materias_primas_compras`
  MODIFY `id_materia_prima_compra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `materias_primas_producciones`
--
ALTER TABLE `materias_primas_producciones`
  MODIFY `id_materia_prima_produccion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materias_primas_productos`
--
ALTER TABLE `materias_primas_productos`
  MODIFY `id_materia_prima_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=174;

--
-- AUTO_INCREMENT de la tabla `metodos_pagos`
--
ALTER TABLE `metodos_pagos`
  MODIFY `id_metodo_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `monedas`
--
ALTER TABLE `monedas`
  MODIFY `id_moneda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `movimientos_anomalos_productos`
--
ALTER TABLE `movimientos_anomalos_productos`
  MODIFY `id_movimiento_anomalo_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT de la tabla `precios_materias_primas`
--
ALTER TABLE `precios_materias_primas`
  MODIFY `id_precio_materia_prima` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT de la tabla `precios_productos`
--
ALTER TABLE `precios_productos`
  MODIFY `id_precio_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT de la tabla `precios_rutas`
--
ALTER TABLE `precios_rutas`
  MODIFY `id_precio_ruta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT de la tabla `precios_servicios`
--
ALTER TABLE `precios_servicios`
  MODIFY `id_precio_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `presentaciones_materias_primas`
--
ALTER TABLE `presentaciones_materias_primas`
  MODIFY `id_materia_prima_presentacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT de la tabla `productos_compras`
--
ALTER TABLE `productos_compras`
  MODIFY `id_producto_compra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `productos_ordenes_entregas_presupuestos`
--
ALTER TABLE `productos_ordenes_entregas_presupuestos`
  MODIFY `id_producto_factura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=219;

--
-- AUTO_INCREMENT de la tabla `productos_producciones`
--
ALTER TABLE `productos_producciones`
  MODIFY `id_producto_produccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT de la tabla `productos_servicios`
--
ALTER TABLE `productos_servicios`
  MODIFY `id_producto_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `referencias_detalles_pagos`
--
ALTER TABLE `referencias_detalles_pagos`
  MODIFY `id_referencia_detalle_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT de la tabla `rutas`
--
ALTER TABLE `rutas`
  MODIFY `id_ruta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de la tabla `servicios_ordenes_entregas_presupuestos`
--
ALTER TABLE `servicios_ordenes_entregas_presupuestos`
  MODIFY `id_servicio_factura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `sucursales_empresas_envios`
--
ALTER TABLE `sucursales_empresas_envios`
  MODIFY `id_sucursal_empresa_envios` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `unidades_medidas`
--
ALTER TABLE `unidades_medidas`
  MODIFY `id_unidad_medida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bancos_detalles_pagos`
--
ALTER TABLE `bancos_detalles_pagos`
  ADD CONSTRAINT `id_banco_bancos_detalles_pagos_fk` FOREIGN KEY (`id_banco`) REFERENCES `bancos` (`id_banco`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_detalle_pago_bancos_detalles_pagos_fk` FOREIGN KEY (`id_detalle_pago`) REFERENCES `detalles_pagos` (`id_detalle_pago`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cambios_monedas`
--
ALTER TABLE `cambios_monedas`
  ADD CONSTRAINT `id_moneda_cambios_monedas_fk` FOREIGN KEY (`id_moneda`) REFERENCES `monedas` (`id_moneda`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `rif_proveedor_compras_fk` FOREIGN KEY (`rif_proveedor`) REFERENCES `proveedores` (`rif_proveedor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `comprobantes_pagos`
--
ALTER TABLE `comprobantes_pagos`
  ADD CONSTRAINT `id_pago_comprobantes_pagos_fk` FOREIGN KEY (`id_pago`) REFERENCES `pagos` (`id_pago`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `deliveries`
--
ALTER TABLE `deliveries`
  ADD CONSTRAINT `cedula_repartidor_deliveries_fk` FOREIGN KEY (`cedula_repartidor`) REFERENCES `repartidores` (`cedula_repartidor`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_direccion_deliveries_fk` FOREIGN KEY (`id_direccion`) REFERENCES `direcciones` (`id_direccion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_orden_entrega_presupuesto_delivery_fk` FOREIGN KEY (`id_orden_entrega_presupuesto`) REFERENCES `ordenes_entregas_presupuestos` (`id_orden_entrega_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalles_pagos`
--
ALTER TABLE `detalles_pagos`
  ADD CONSTRAINT `id_metodo_pago_detalles_pagos_fk` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodos_pagos` (`id_metodo_pago`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_moneda_detalles_pagos_fk` FOREIGN KEY (`id_moneda`) REFERENCES `monedas` (`id_moneda`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_pago_detalles_pagos_fk` FOREIGN KEY (`id_pago`) REFERENCES `pagos` (`id_pago`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD CONSTRAINT `id_latitud_direccion_direcciones_fk` FOREIGN KEY (`id_latitud_direccion`) REFERENCES `latitudes_direcciones` (`id_latitud_direccion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_longitud_direccion_direcciones_fk` FOREIGN KEY (`id_longitud_direccion`) REFERENCES `longitudes_direcciones` (`id_longitud_direccion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_ruta_direcciones_fk` FOREIGN KEY (`id_ruta`) REFERENCES `rutas` (`id_ruta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `envios_terceros`
--
ALTER TABLE `envios_terceros`
  ADD CONSTRAINT `id_orden_entrega_presupuesto_envios_terceros_fk` FOREIGN KEY (`id_orden_entrega_presupuesto`) REFERENCES `ordenes_entregas_presupuestos` (`id_orden_entrega_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_sucursal_empresa_envios_envios_terceros_fk` FOREIGN KEY (`id_sucursal_empresa_envios`) REFERENCES `sucursales_empresas_envios` (`id_sucursal_empresa_envios`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `materias_primas`
--
ALTER TABLE `materias_primas`
  ADD CONSTRAINT `id_unidad_medida_materias_primas_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medidas` (`id_unidad_medida`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `materias_primas_compras`
--
ALTER TABLE `materias_primas_compras`
  ADD CONSTRAINT `id_compra_materias_primas_compras_fk` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_materia_prima_materias_primas_compras_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `materias_primas_producciones`
--
ALTER TABLE `materias_primas_producciones`
  ADD CONSTRAINT `id_materia_prima_materias_primas_producciones_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_produccion_materias_primas_producciones_fk` FOREIGN KEY (`id_produccion`) REFERENCES `producciones` (`id_produccion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `materias_primas_productos`
--
ALTER TABLE `materias_primas_productos`
  ADD CONSTRAINT `id_materia_prima_materias_primas_productos_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_producto_materias_primas_productos_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimientos_anomalos_materias_primas`
--
ALTER TABLE `movimientos_anomalos_materias_primas`
  ADD CONSTRAINT `id_materia_prima_movimientos_anomalos_materias_primas_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimientos_anomalos_productos`
--
ALTER TABLE `movimientos_anomalos_productos`
  ADD CONSTRAINT `id_presentacion_producto_movimientos_anomalos_productos_fk` FOREIGN KEY (`id_presentacion_producto`) REFERENCES `presentaciones_productos` (`id_presentacion_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `ordenes_entregas_presupuestos`
--
ALTER TABLE `ordenes_entregas_presupuestos`
  ADD CONSTRAINT `id_cambio_iva_venta` FOREIGN KEY (`id_cambio_iva`) REFERENCES `cambios_iva` (`id_cambio_iva`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rif_cedula_cliente_venta_fk` FOREIGN KEY (`rif_cedula_cliente`) REFERENCES `clientes` (`rif_cedula_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `id_orden_entrega_presupuesto_pagos_fk` FOREIGN KEY (`id_orden_entrega_presupuesto`) REFERENCES `ordenes_entregas_presupuestos` (`id_orden_entrega_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `precios_materias_primas`
--
ALTER TABLE `precios_materias_primas`
  ADD CONSTRAINT `id_materia_prima_precios_materias_primas_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `precios_productos`
--
ALTER TABLE `precios_productos`
  ADD CONSTRAINT `id_producto_precios_productos_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `precios_rutas`
--
ALTER TABLE `precios_rutas`
  ADD CONSTRAINT `id_ruta_precios_rutas_fk` FOREIGN KEY (`id_ruta`) REFERENCES `rutas` (`id_ruta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `precios_servicios`
--
ALTER TABLE `precios_servicios`
  ADD CONSTRAINT `id_servicio_precios_servicios_fk` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `presentaciones`
--
ALTER TABLE `presentaciones`
  ADD CONSTRAINT `id_unidad_medida_presentaciones_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medidas` (`id_unidad_medida`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `presentaciones_materias_primas`
--
ALTER TABLE `presentaciones_materias_primas`
  ADD CONSTRAINT `id_materia_prima_materias_primas_presentaciones_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_presentacion_materias_primas_presentaciones_fk` FOREIGN KEY (`id_presentacion`) REFERENCES `presentaciones` (`id_presentacion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `presentaciones_productos`
--
ALTER TABLE `presentaciones_productos`
  ADD CONSTRAINT `id_presentacion_productos_presentaciones_fk` FOREIGN KEY (`id_presentacion`) REFERENCES `presentaciones` (`id_presentacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_producto_productos_presentaciones_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `id_categoria_producto_productos_fk` FOREIGN KEY (`id_categoria_producto`) REFERENCES `categorias_productos` (`id_categoria_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_unidad_medida_productos_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medidas` (`id_unidad_medida`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_compras`
--
ALTER TABLE `productos_compras`
  ADD CONSTRAINT `id_compra_productos_compras_fk` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_presentacion_producto_productos_compras_fk` FOREIGN KEY (`id_presentacion_producto`) REFERENCES `presentaciones_productos` (`id_presentacion_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_ordenes_entregas_presupuestos`
--
ALTER TABLE `productos_ordenes_entregas_presupuestos`
  ADD CONSTRAINT `id_orden_entrega_presupuesto_poep_fk` FOREIGN KEY (`id_orden_entrega_presupuesto`) REFERENCES `ordenes_entregas_presupuestos` (`id_orden_entrega_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_presentacion_producto_poep_fk` FOREIGN KEY (`id_presentacion_producto`) REFERENCES `presentaciones_productos` (`id_presentacion_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_producciones`
--
ALTER TABLE `productos_producciones`
  ADD CONSTRAINT `id_produccion_productos_producciones_fk` FOREIGN KEY (`id_produccion`) REFERENCES `producciones` (`id_produccion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_producto_productos_producciones_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_servicios`
--
ALTER TABLE `productos_servicios`
  ADD CONSTRAINT `id_producto_productos_soep_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_serivicio_productos_servicios_fk` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `referencias_detalles_pagos`
--
ALTER TABLE `referencias_detalles_pagos`
  ADD CONSTRAINT `id_detalle_pago_referencias_detalles_pagos_fk` FOREIGN KEY (`id_detalle_pago`) REFERENCES `detalles_pagos` (`id_detalle_pago`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD CONSTRAINT `id_unidad_medida_servicios_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medidas` (`id_unidad_medida`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `servicios_ordenes_entregas_presupuestos`
--
ALTER TABLE `servicios_ordenes_entregas_presupuestos`
  ADD CONSTRAINT `id_direccion_soep_fk` FOREIGN KEY (`id_direccion`) REFERENCES `direcciones` (`id_direccion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_orden_entrega_presupuesto_soep_fk` FOREIGN KEY (`id_orden_entrega_presupuesto`) REFERENCES `ordenes_entregas_presupuestos` (`id_orden_entrega_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_servicio_soep_fk` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `sucursales_empresas_envios`
--
ALTER TABLE `sucursales_empresas_envios`
  ADD CONSTRAINT `id_direccion_sucursales_empresas_envios_fk` FOREIGN KEY (`id_direccion`) REFERENCES `direcciones` (`id_direccion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_empresa_envios_sucursales_empresas_envios_fk` FOREIGN KEY (`id_empresa_envios`) REFERENCES `empresas_envios` (`id_empresa_envios`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
