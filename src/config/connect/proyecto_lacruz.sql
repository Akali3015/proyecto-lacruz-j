-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-04-2026 a las 18:36:12
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
(8, 'BANCO PROVINCIALES', 0);

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cambios_iva`
--

CREATE TABLE `cambios_iva` (
  `id_cambio_iva` int(11) NOT NULL,
  `monto_cambio_iva` float NOT NULL,
  `fecha_cambio_iva` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cambios_iva`
--

INSERT INTO `cambios_iva` (`id_cambio_iva`, `monto_cambio_iva`, `fecha_cambio_iva`, `status`) VALUES
(1, 12, '2025-12-05 18:15:15', 1),
(129, 12, '2026-04-24 15:34:11', 1),
(130, 12, '2026-04-24 15:34:17', 1),
(131, 12, '2026-04-24 15:36:14', 1),
(132, 12, '2026-04-24 15:36:50', 1),
(133, 12, '2026-04-24 15:36:54', 1),
(134, 12, '2026-04-24 15:41:00', 1),
(135, 24, '2026-04-24 15:46:47', 1),
(136, 12, '2026-04-24 15:49:02', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cambios_monedas`
--

CREATE TABLE `cambios_monedas` (
  `id_cambio_moneda` int(11) NOT NULL,
  `id_moneda` int(11) NOT NULL,
  `valor_moneda` float NOT NULL,
  `fecha_cambio` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cambios_monedas`
--

INSERT INTO `cambios_monedas` (`id_cambio_moneda`, `id_moneda`, `valor_moneda`, `fecha_cambio`, `status`) VALUES
(1, 1, 17, '2025-12-06 20:12:19', 1),
(2, 2, 2, '2025-12-06 20:13:37', 1),
(3, 3, 3, '2025-12-06 20:13:48', 1),
(4, 1, 257.93, '2025-12-06 20:14:15', 1),
(5, 1, 250, '2025-12-11 18:35:07', 1),
(6, 3, 250, '2025-12-11 19:04:46', 1),
(7, 2, 1, '2025-12-14 16:30:34', 1),
(8, 1, 23, '2026-02-07 20:44:34', 1),
(9, 1, 12, '2026-02-09 20:46:09', 1),
(10, 1, 250, '2026-02-09 20:46:55', 1),
(11, 1, 279, '2026-03-30 10:22:47', 1),
(12, 2, 22, '2026-03-30 10:22:56', 1),
(13, 3, 500, '2026-03-30 10:23:05', 1),
(14, 1, 12, '2026-03-30 10:29:39', 1),
(15, 3, 1234, '2026-03-30 10:29:58', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_productos`
--

CREATE TABLE `categorias_productos` (
  `id_categoria_producto` int(11) NOT NULL,
  `nombre_categoria` varchar(50) NOT NULL,
  `necesitan_materias_primas` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias_productos`
--

INSERT INTO `categorias_productos` (`id_categoria_producto`, `nombre_categoria`, `necesitan_materias_primas`, `status`) VALUES
(1, 'FABRICADOS', 1, 1),
(2, 'NO FABRICADOS', 0, 1),
(3, 'INSUMOS', 0, 1);

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
('30485684', 'ANDEROSN FREITEZ', '04169484649', 'andersonfreitez6@gmail.com', 'SANARE', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id_compra` varchar(20) NOT NULL,
  `rif_proveedor` varchar(20) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `fecha_compra` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id_compra`, `rif_proveedor`, `cedula_usuario`, `fecha_compra`, `status`) VALUES
('10', '30485684', '30485684', '2026-02-18 21:04:00', 1),
('11', '123456789', '30485684', '2026-04-05 14:43:00', 1),
('12', '123456789', '30485684', '2026-04-05 14:49:00', 1),
('13', '123456789', '30485684', '2026-04-05 14:44:00', 1),
('4', '30485684', '30485684', '2026-02-15 15:02:00', 1),
('5', '30485684', '30485684', '2026-02-15 15:08:00', 1),
('7', '30485684', '30485684', '2026-02-18 20:56:00', 1),
('8', '30485684', '30485684', '2026-02-18 20:59:00', 1),
('9', '30485684', '30485684', '2026-02-18 20:59:00', 1);

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deliveries`
--

CREATE TABLE `deliveries` (
  `id_delivery` varchar(20) NOT NULL,
  `id_factura` varchar(20) NOT NULL,
  `cedula_repartidor` varchar(20) DEFAULT NULL,
  `id_direccion` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_pagos`
--

CREATE TABLE `detalles_pagos` (
  `id_detalle_pago` int(11) NOT NULL,
  `id_pago` varchar(20) NOT NULL,
  `id_metodo_pago` int(11) NOT NULL,
  `id_moneda` int(11) NOT NULL,
  `monto_pago` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direcciones`
--

CREATE TABLE `direcciones` (
  `id_direccion` int(11) NOT NULL,
  `id_ruta` int(11) NOT NULL,
  `id_latitud_direccion` int(11) NOT NULL,
  `id_longitud_direccion` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas_envios`
--

CREATE TABLE `empresas_envios` (
  `id_empresa_envios` int(11) NOT NULL,
  `nombre_empresa` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `envios_terceros`
--

CREATE TABLE `envios_terceros` (
  `id_envio_tercero` varchar(20) NOT NULL,
  `id_factura` varchar(20) NOT NULL,
  `id_sucursal_empresa_envios` int(11) NOT NULL,
  `precio_envio` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id_factura` varchar(20) NOT NULL,
  `rif_cedula_cliente` varchar(20) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `id_cambio_iva` int(11) NOT NULL,
  `fecha_factura` datetime NOT NULL,
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `longitudes_direcciones`
--

CREATE TABLE `longitudes_direcciones` (
  `id_longitud_direccion` int(11) NOT NULL,
  `coordenada_longitud` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_primas`
--

CREATE TABLE `materias_primas` (
  `id_materia_prima` varchar(20) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `nombre_materia_prima` varchar(50) NOT NULL,
  `stock_materia_prima` int(11) NOT NULL,
  `stock_minimo_materia_prima` int(11) NOT NULL,
  `precio_materia_prima` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias_primas`
--

INSERT INTO `materias_primas` (`id_materia_prima`, `id_unidad_medida`, `nombre_materia_prima`, `stock_materia_prima`, `stock_minimo_materia_prima`, `precio_materia_prima`, `status`) VALUES
('1', 2, 'SULFURO', 796, 0, 10, 1),
('2', 2, 'HIPOCLORITO', 11, 0, 100, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_primas_compras`
--

CREATE TABLE `materias_primas_compras` (
  `id_materia_prima_compra` int(11) NOT NULL,
  `id_materia_prima` varchar(20) NOT NULL,
  `id_compra` varchar(20) NOT NULL,
  `cantidad_materia_prima` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias_primas_compras`
--

INSERT INTO `materias_primas_compras` (`id_materia_prima_compra`, `id_materia_prima`, `id_compra`, `cantidad_materia_prima`, `status`) VALUES
(1, '1', '7', 1, 1),
(2, '1', '11', 20, 1),
(3, '2', '11', 1, 1),
(4, '1', '12', 12, 1),
(5, '1', '13', 1, 1),
(6, '1', '13', 1, 1),
(7, '1', '13', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_primas_presentaciones`
--

CREATE TABLE `materias_primas_presentaciones` (
  `id_materia_prima_presentacion` int(11) NOT NULL,
  `id_materia_prima` varchar(20) NOT NULL,
  `id_presentacion` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias_primas_presentaciones`
--

INSERT INTO `materias_primas_presentaciones` (`id_materia_prima_presentacion`, `id_materia_prima`, `id_presentacion`, `status`) VALUES
(1, '1', 3, 1),
(2, '1', 2, 1),
(4, '2', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_primas_producciones`
--

CREATE TABLE `materias_primas_producciones` (
  `id_materia_prima_produccion` int(11) NOT NULL,
  `id_materia_prima` varchar(20) NOT NULL,
  `id_produccion` varchar(20) NOT NULL,
  `cantidad_materia_prima` float NOT NULL,
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
  `cantidad_materia_prima` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias_primas_productos`
--

INSERT INTO `materias_primas_productos` (`id_materia_prima_producto`, `id_materia_prima`, `id_producto`, `cantidad_materia_prima`, `status`) VALUES
(4, '1', 'PROD-26114-00001-80', 1, 0),
(5, '2', 'PROD-26114-00002-35', 1, 0),
(38, '2', 'PROD-26114-00003-42', 1.9, 0),
(40, '1', 'PROD-26115-00001-16', 12, 0),
(41, '1', 'PROD-26115-00002-14', 10, 0),
(42, '1', 'PROD-26115-00003-20', 10, 0),
(43, '1', 'PROD-26115-00004-58', 1, 0),
(44, '1', 'PROD-26115-00006-00', 1, 0);

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
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `metodos_pagos`
--

INSERT INTO `metodos_pagos` (`id_metodo_pago`, `nombre_metodo_pago`, `necesita_moneda`, `necesita_banco_emisor`, `necesita_banco_receptor`, `necesita_referencia`, `status`) VALUES
(1, 'TRANSFERENCIA', 0, 1, 1, 1, 1),
(2, 'PAGO MÓVIL', 0, 0, 1, 1, 1),
(3, 'ZELLE', 1, 0, 0, 1, 1),
(4, 'ZINLI', 1, 0, 0, 1, 0),
(5, 'BINANCE', 1, 0, 0, 1, 1),
(6, 'EFECTIVO', 1, 0, 0, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `monedas`
--

CREATE TABLE `monedas` (
  `id_moneda` int(11) NOT NULL,
  `nombre_moneda` varchar(20) NOT NULL,
  `simbolo_moneda` varchar(3) NOT NULL,
  `valor_moneda` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `monedas`
--

INSERT INTO `monedas` (`id_moneda`, `nombre_moneda`, `simbolo_moneda`, `valor_moneda`, `status`) VALUES
(1, 'DÓLAR', '$', 450, 1),
(2, 'BÓLIVAR', 'BS', 1, 1),
(3, 'EURO', '€', 600, 1),
(4, 'YUAN', '¥', 500, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` varchar(20) NOT NULL,
  `id_factura` varchar(20) NOT NULL,
  `fecha_pago` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precios_materias_primas`
--

CREATE TABLE `precios_materias_primas` (
  `id_precio_materia_prima` int(11) NOT NULL,
  `id_materia_prima` varchar(20) NOT NULL,
  `precio_materia_prima` float NOT NULL,
  `fecha_cambio` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precios_productos`
--

CREATE TABLE `precios_productos` (
  `id_precio_producto` int(11) NOT NULL,
  `id_producto` varchar(20) NOT NULL,
  `precio_producto` float NOT NULL,
  `fecha_cambio` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `precios_productos`
--

INSERT INTO `precios_productos` (`id_precio_producto`, `id_producto`, `precio_producto`, `fecha_cambio`, `status`) VALUES
(5, 'PROD-26114-00001-80', 10, '2026-04-25 17:05:33', 0),
(6, 'PROD-26114-00002-35', 1, '2026-04-25 18:38:07', 0),
(7, 'PROD-26114-00003-42', 1, '2026-04-25 19:14:56', 0),
(8, 'PROD-26114-00003-42', 0, '2026-04-26 09:21:32', 0),
(9, 'PROD-26115-00001-16', 7.89, '2026-04-26 09:25:30', 0),
(10, 'PROD-26115-00001-16', 89, '2026-04-26 10:31:29', 0),
(11, 'PROD-26115-00002-14', 1, '2026-04-26 11:52:38', 0),
(12, 'PROD-26115-00003-20', 120, '2026-04-26 12:01:43', 0),
(13, 'PROD-26115-00004-58', 10, '2026-04-26 12:02:45', 0),
(14, 'PROD-26115-00005-85', 1, '2026-04-26 12:03:56', 0),
(15, 'PROD-26115-00006-00', 1, '2026-04-26 12:21:23', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precios_rutas`
--

CREATE TABLE `precios_rutas` (
  `id_precio_ruta` int(11) NOT NULL,
  `id_ruta` int(11) NOT NULL,
  `precio_ruta` float NOT NULL,
  `fecha_cambio` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precios_servicios`
--

CREATE TABLE `precios_servicios` (
  `id_precio_servicio` int(11) NOT NULL,
  `id_servicio` varchar(20) NOT NULL,
  `precio_servicio` float NOT NULL,
  `fecha_cambio` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentaciones`
--

CREATE TABLE `presentaciones` (
  `id_presentacion` int(11) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `nombre_presentacion` varchar(50) NOT NULL,
  `cantidad_pmp` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `presentaciones`
--

INSERT INTO `presentaciones` (`id_presentacion`, `id_unidad_medida`, `nombre_presentacion`, `cantidad_pmp`, `status`) VALUES
(1, 2, 'POR LITRO', 1, 1),
(2, 2, 'BIDÓN', 20, 1),
(3, 2, 'PIPAS', 200, 1),
(32, 2, 'ANDERON', 100, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentaciones_productos`
--

CREATE TABLE `presentaciones_productos` (
  `id_producto_presentacion` int(11) NOT NULL,
  `id_presentacion` int(11) NOT NULL,
  `id_producto` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `presentaciones_productos`
--

INSERT INTO `presentaciones_productos` (`id_producto_presentacion`, `id_presentacion`, `id_producto`, `status`) VALUES
(16, 1, 'PROD-26114-00001-80', 0),
(17, 2, 'PROD-26114-00001-80', 0),
(18, 3, 'PROD-26114-00001-80', 0),
(19, 1, 'PROD-26114-00002-35', 0),
(57, 2, 'PROD-26114-00003-42', 0),
(58, 3, 'PROD-26114-00003-42', 0),
(60, 1, 'PROD-26115-00001-16', 0),
(61, 1, 'PROD-26115-00002-14', 0),
(62, 2, 'PROD-26115-00002-14', 0),
(63, 3, 'PROD-26115-00002-14', 0),
(64, 1, 'PROD-26115-00003-20', 0),
(65, 2, 'PROD-26115-00003-20', 0),
(66, 3, 'PROD-26115-00003-20', 0),
(67, 1, 'PROD-26115-00004-58', 0),
(68, 2, 'PROD-26115-00004-58', 0),
(69, 3, 'PROD-26115-00004-58', 0),
(70, 1, 'PROD-26115-00005-85', 0),
(71, 2, 'PROD-26115-00005-85', 0),
(72, 3, 'PROD-26115-00005-85', 0),
(73, 1, 'PROD-26115-00006-00', 0),
(74, 2, 'PROD-26115-00006-00', 0),
(75, 3, 'PROD-26115-00006-00', 0);

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
('26', '2026-02-25 18:10:13', 1),
('27', '2026-02-25 18:10:55', 1),
('28', '2026-02-25 18:11:29', 1),
('29', '2026-02-25 18:28:00', 1),
('30', '2026-03-01 08:15:32', 1),
('31', '2026-03-01 08:16:17', 1),
('32', '2026-03-01 08:44:57', 1),
('34', '2026-03-01 09:00:33', 1),
('35', '2026-03-01 09:32:25', 1),
('36', '2026-03-29 13:11:33', 1),
('37', '2026-03-29 13:13:01', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` varchar(20) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `id_categoria_producto` int(11) NOT NULL,
  `nombre_producto` varchar(50) NOT NULL,
  `precio_producto` float NOT NULL,
  `stock_producto` int(15) NOT NULL,
  `stock_minimo_producto` int(15) NOT NULL,
  `mostrar_ecommerce` tinyint(1) NOT NULL,
  `foto_producto` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `id_unidad_medida`, `id_categoria_producto`, `nombre_producto`, `precio_producto`, `stock_producto`, `stock_minimo_producto`, `mostrar_ecommerce`, `foto_producto`, `status`) VALUES
('PROD-26114-00001-80', 2, 1, 'JABÓN LÍQUIDO', 10, 100, 5, 1, '', 0),
('PROD-26114-00002-35', 2, 1, 'JABÓN LÍQUIDO', 1, 100, 5, 1, 'productos_2026-04-25_18_38_07_43.jpg?v=2026-04-25_18_39_39', 0),
('PROD-26114-00003-42', 1, 1, 'JABÓN LÍQUIDO', 0, 1008, 5, 1, 'productos_2026-04-25_19_19_48.jpg?v=2026-04-26_09_03_50', 0),
('PROD-26115-00001-16', 2, 1, 'JBNA LÍQUIDO', 89, 100, 5, 1, 'productos_2026-04-26_09_25_30_58.png', 0),
('PROD-26115-00002-14', 2, 1, 'ANDERSON', 1, 1000, 5, 1, 'productos_2026-04-26_11_52_38_9.jpg', 0),
('PROD-26115-00003-20', 2, 1, 'hjjj', 120, 100, 5, 1, 'productos_2026-04-26_12_01_43_14.jpg', 0),
('PROD-26115-00004-58', 1, 1, 'JBNA LÍQUIDO', 10, 100, 5, 1, 'productos_2026-04-26_12_02_44_85.jpg', 0),
('PROD-26115-00005-85', 2, 2, 'JBNA LÍQUIDO', 1, 100, 5, 1, '', 0),
('PROD-26115-00006-00', 2, 1, 'JABÓN LÍQUIDO', 1, 0, 5, 1, '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_compras`
--

CREATE TABLE `productos_compras` (
  `id_producto_compra` int(11) NOT NULL,
  `id_producto` varchar(20) NOT NULL,
  `id_compra` varchar(20) NOT NULL,
  `cantidad_producto` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_facturas`
--

CREATE TABLE `productos_facturas` (
  `id_producto_factura` int(11) NOT NULL,
  `id_producto` varchar(20) NOT NULL,
  `id_factura` varchar(20) NOT NULL,
  `cantidad_producto` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_producciones`
--

CREATE TABLE `productos_producciones` (
  `id_producto_produccion` int(11) NOT NULL,
  `id_produccion` varchar(20) NOT NULL,
  `id_producto` varchar(20) NOT NULL,
  `cantidad_producida` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_servicios_facturas`
--

CREATE TABLE `productos_servicios_facturas` (
  `id_producto_servicio_factura` int(11) NOT NULL,
  `id_producto` varchar(20) NOT NULL,
  `id_servicio_factura` int(11) NOT NULL,
  `cantidad_producto` float NOT NULL,
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
('123456789', 'ANDERSO', '04169484640', 'andersonfreitez60@gmail.com', 'HHHHBHBH', 1),
('1234567890', 'ANDERSONM', '04169484641', 'andersonfreitez6@gmail.com', 'DIRECCION', 1),
('30485684', 'ANDERSON', '04169484649', 'ANDERSONFREITEZ@GMAIL.COM', 'sxaxasas', 1),
('J12345611', 'ANDERSON9K', '04169484699', 'andersonfreitez6@gmail.co', 'kmkmkm', 0),
('J12345678', 'ANDERS', '04169484688', 'andersonfreitez9@gmail.com', 'eejejej', 0),
('J30485684', 'ANDERSON9', '04169484645', 'andersonfreitez61@gmail.co', 'jxjnjnj', 0);

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
('V30485684', 'ANDERSON', 'FREITEZ', '04169484649', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rutas`
--

CREATE TABLE `rutas` (
  `id_ruta` int(11) NOT NULL,
  `nombre_ruta` varchar(50) NOT NULL,
  `precio_ruta` float NOT NULL,
  `minimo_km_ruta` float NOT NULL,
  `maximo_km_ruta` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rutas`
--

INSERT INTO `rutas` (`id_ruta`, `nombre_ruta`, `precio_ruta`, `minimo_km_ruta`, `maximo_km_ruta`, `status`) VALUES
(1, 'CERCANO', 0.5, 0, 2, 1),
(2, 'LEJANO', 1, 3, 5, 1),
(3, 'FUERA DEL ALCANCE', 2, 6, 1000, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id_servicio` varchar(20) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `nombre_servicio` varchar(100) NOT NULL,
  `precio_servicio` float NOT NULL,
  `mostrar_ecommerce` tinyint(1) NOT NULL,
  `foto_servicio` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id_servicio`, `id_unidad_medida`, `nombre_servicio`, `precio_servicio`, `mostrar_ecommerce`, `foto_servicio`, `status`) VALUES
('1', 2, 'FUMIGACION', 100, 0, '', 1),
('2', 1, 'FUMIGACIONT', 122, 0, '', 1),
('3', 2, 'FUMIGACION 2', 12, 0, '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios_facturas`
--

CREATE TABLE `servicios_facturas` (
  `id_servicio_factura` int(11) NOT NULL,
  `id_factura` varchar(20) NOT NULL,
  `id_servicio` varchar(20) NOT NULL,
  `cantidad_servicio` float NOT NULL,
  `es_precio_mapfre` tinyint(1) NOT NULL,
  `precio_servicio_mapfre` float DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades_medidas`
--

CREATE TABLE `unidades_medidas` (
  `id_unidad_medida` int(11) NOT NULL,
  `nombre_unidad_medida` varchar(50) NOT NULL,
  `simbolo_unidad_medida` varchar(3) NOT NULL,
  `equivalencia_ub` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `unidades_medidas`
--

INSERT INTO `unidades_medidas` (`id_unidad_medida`, `nombre_unidad_medida`, `simbolo_unidad_medida`, `equivalencia_ub`, `status`) VALUES
(1, 'KILO(S)', 'KG', 1000, 1),
(2, 'LITRO(S)', 'L', 1000, 1),
(3, 'MILLA', 'ML', 1000000, 1),
(4, 'MILL', 'ML', 1000000, 0),
(5, 'MILLA', 'M', 1000000, 0),
(6, 'MILLM', 'MIL', 1000000, 0),
(7, 'MILLA', 'ML', 2, 0),
(8, 'MILLAS', 'MLA', 1000000, 1);

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
  ADD PRIMARY KEY (`id_cambio_iva`);

--
-- Indices de la tabla `cambios_monedas`
--
ALTER TABLE `cambios_monedas`
  ADD PRIMARY KEY (`id_cambio_moneda`),
  ADD KEY `id_moneda_cambios_monedas_fk` (`id_moneda`);

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
  ADD KEY `rif_proveedor_compras_fk` (`rif_proveedor`);

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
  ADD KEY `id_factura_delivery_fk` (`id_factura`);

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
  ADD KEY `id_factura_envios_terceros_fk` (`id_factura`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id_factura`),
  ADD KEY `rif_cedula_cliente_venta_fk` (`rif_cedula_cliente`),
  ADD KEY `id_cambio_iva_venta` (`id_cambio_iva`);

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
-- Indices de la tabla `materias_primas_presentaciones`
--
ALTER TABLE `materias_primas_presentaciones`
  ADD PRIMARY KEY (`id_materia_prima_presentacion`),
  ADD KEY `id_materia_prima_materias_primas_presentaciones_fk` (`id_materia_prima`),
  ADD KEY `id_presentacion_materias_primas_presentaciones_fk` (`id_presentacion`);

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
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_venta_pago_fk` (`id_factura`);

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
-- Indices de la tabla `presentaciones_productos`
--
ALTER TABLE `presentaciones_productos`
  ADD PRIMARY KEY (`id_producto_presentacion`),
  ADD KEY `id_presentacion_productos_presentaciones_fk` (`id_presentacion`),
  ADD KEY `id_producto_productos_presentaciones_fk` (`id_producto`);

--
-- Indices de la tabla `producciones`
--
ALTER TABLE `producciones`
  ADD PRIMARY KEY (`id_produccion`);

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
  ADD KEY `id_producto_productos_compras_fk` (`id_producto`),
  ADD KEY `id_compra_productos_compras_fk` (`id_compra`);

--
-- Indices de la tabla `productos_facturas`
--
ALTER TABLE `productos_facturas`
  ADD PRIMARY KEY (`id_producto_factura`),
  ADD KEY `id_producto_productos_facturas_fk` (`id_producto`),
  ADD KEY `id_factura_productos_facturas_fk` (`id_factura`);

--
-- Indices de la tabla `productos_producciones`
--
ALTER TABLE `productos_producciones`
  ADD PRIMARY KEY (`id_producto_produccion`),
  ADD KEY `id_produccion_productos_producciones_fk` (`id_produccion`),
  ADD KEY `id_producto_productos_producciones_fk` (`id_producto`);

--
-- Indices de la tabla `productos_servicios_facturas`
--
ALTER TABLE `productos_servicios_facturas`
  ADD PRIMARY KEY (`id_producto_servicio_factura`),
  ADD KEY `id_producto_productos_servicios_facturas_fk` (`id_producto`),
  ADD KEY `id_servicio_factura_productos_servicios_facturas_fk` (`id_servicio_factura`);

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
  ADD PRIMARY KEY (`id_ruta`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id_servicio`),
  ADD KEY `id_unidad_medida_servicios_fk` (`id_unidad_medida`);

--
-- Indices de la tabla `servicios_facturas`
--
ALTER TABLE `servicios_facturas`
  ADD PRIMARY KEY (`id_servicio_factura`),
  ADD KEY `id_factura_servicios_facturas_fk` (`id_factura`),
  ADD KEY `id_servicio_servicios_facturas_fk` (`id_servicio`);

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
  MODIFY `id_banco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `bancos_detalles_pagos`
--
ALTER TABLE `bancos_detalles_pagos`
  MODIFY `id_banco_detalle_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `cambios_iva`
--
ALTER TABLE `cambios_iva`
  MODIFY `id_cambio_iva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT de la tabla `cambios_monedas`
--
ALTER TABLE `cambios_monedas`
  MODIFY `id_cambio_moneda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `categorias_productos`
--
ALTER TABLE `categorias_productos`
  MODIFY `id_categoria_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `detalles_pagos`
--
ALTER TABLE `detalles_pagos`
  MODIFY `id_detalle_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  MODIFY `id_direccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `empresas_envios`
--
ALTER TABLE `empresas_envios`
  MODIFY `id_empresa_envios` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `latitudes_direcciones`
--
ALTER TABLE `latitudes_direcciones`
  MODIFY `id_latitud_direccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `longitudes_direcciones`
--
ALTER TABLE `longitudes_direcciones`
  MODIFY `id_longitud_direccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `materias_primas_compras`
--
ALTER TABLE `materias_primas_compras`
  MODIFY `id_materia_prima_compra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `materias_primas_presentaciones`
--
ALTER TABLE `materias_primas_presentaciones`
  MODIFY `id_materia_prima_presentacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `materias_primas_producciones`
--
ALTER TABLE `materias_primas_producciones`
  MODIFY `id_materia_prima_produccion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materias_primas_productos`
--
ALTER TABLE `materias_primas_productos`
  MODIFY `id_materia_prima_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `metodos_pagos`
--
ALTER TABLE `metodos_pagos`
  MODIFY `id_metodo_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `monedas`
--
ALTER TABLE `monedas`
  MODIFY `id_moneda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `precios_materias_primas`
--
ALTER TABLE `precios_materias_primas`
  MODIFY `id_precio_materia_prima` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `precios_productos`
--
ALTER TABLE `precios_productos`
  MODIFY `id_precio_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `precios_rutas`
--
ALTER TABLE `precios_rutas`
  MODIFY `id_precio_ruta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `precios_servicios`
--
ALTER TABLE `precios_servicios`
  MODIFY `id_precio_servicio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `presentaciones`
--
ALTER TABLE `presentaciones`
  MODIFY `id_presentacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `presentaciones_productos`
--
ALTER TABLE `presentaciones_productos`
  MODIFY `id_producto_presentacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT de la tabla `productos_compras`
--
ALTER TABLE `productos_compras`
  MODIFY `id_producto_compra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `productos_facturas`
--
ALTER TABLE `productos_facturas`
  MODIFY `id_producto_factura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `productos_producciones`
--
ALTER TABLE `productos_producciones`
  MODIFY `id_producto_produccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT de la tabla `productos_servicios_facturas`
--
ALTER TABLE `productos_servicios_facturas`
  MODIFY `id_producto_servicio_factura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `referencias_detalles_pagos`
--
ALTER TABLE `referencias_detalles_pagos`
  MODIFY `id_referencia_detalle_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `rutas`
--
ALTER TABLE `rutas`
  MODIFY `id_ruta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `servicios_facturas`
--
ALTER TABLE `servicios_facturas`
  MODIFY `id_servicio_factura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sucursales_empresas_envios`
--
ALTER TABLE `sucursales_empresas_envios`
  MODIFY `id_sucursal_empresa_envios` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `unidades_medidas`
--
ALTER TABLE `unidades_medidas`
  MODIFY `id_unidad_medida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  ADD CONSTRAINT `id_factura_delivery_fk` FOREIGN KEY (`id_factura`) REFERENCES `facturas` (`id_factura`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `id_factura_envios_terceros_fk` FOREIGN KEY (`id_factura`) REFERENCES `facturas` (`id_factura`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_sucursal_empresa_envios_envios_terceros_fk` FOREIGN KEY (`id_sucursal_empresa_envios`) REFERENCES `sucursales_empresas_envios` (`id_sucursal_empresa_envios`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `id_cambio_iva_venta` FOREIGN KEY (`id_cambio_iva`) REFERENCES `cambios_iva` (`id_cambio_iva`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rif_cedula_cliente_venta_fk` FOREIGN KEY (`rif_cedula_cliente`) REFERENCES `clientes` (`rif_cedula_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Filtros para la tabla `materias_primas_presentaciones`
--
ALTER TABLE `materias_primas_presentaciones`
  ADD CONSTRAINT `id_materia_prima_materias_primas_presentaciones_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_presentacion_materias_primas_presentaciones_fk` FOREIGN KEY (`id_presentacion`) REFERENCES `presentaciones` (`id_presentacion`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `id_factura_pagos_fk` FOREIGN KEY (`id_factura`) REFERENCES `facturas` (`id_factura`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `id_producto_productos_compras_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_facturas`
--
ALTER TABLE `productos_facturas`
  ADD CONSTRAINT `id_factura_productos_facturas_fk` FOREIGN KEY (`id_factura`) REFERENCES `facturas` (`id_factura`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_producto_productos_facturas_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_producciones`
--
ALTER TABLE `productos_producciones`
  ADD CONSTRAINT `id_produccion_productos_producciones_fk` FOREIGN KEY (`id_produccion`) REFERENCES `producciones` (`id_produccion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_producto_productos_producciones_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_servicios_facturas`
--
ALTER TABLE `productos_servicios_facturas`
  ADD CONSTRAINT `id_producto_productos_servicios_facturas_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_servicio_factura_productos_servicios_facturas_fk` FOREIGN KEY (`id_servicio_factura`) REFERENCES `servicios_facturas` (`id_servicio_factura`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Filtros para la tabla `servicios_facturas`
--
ALTER TABLE `servicios_facturas`
  ADD CONSTRAINT `id_factura_servicios_facturas_fk` FOREIGN KEY (`id_factura`) REFERENCES `facturas` (`id_factura`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_servicio_servicios_facturas_fk` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`) ON DELETE CASCADE ON UPDATE CASCADE;

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
