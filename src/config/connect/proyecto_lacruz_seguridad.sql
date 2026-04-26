-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-04-2026 a las 18:36:28
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
-- Base de datos: `proyecto_lacruz_seguridad`
--
CREATE DATABASE IF NOT EXISTS `proyecto_lacruz_seguridad` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `proyecto_lacruz_seguridad`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accesos`
--

CREATE TABLE `accesos` (
  `id_acceso` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `accesos`
--

INSERT INTO `accesos` (`id_acceso`, `id_rol`, `id_modulo`, `id_permiso`, `status`) VALUES
(813, 1, 251, 618, 1),
(814, 1, 251, 619, 1),
(815, 1, 251, 620, 1),
(816, 1, 251, 621, 1),
(817, 1, 251, 622, 1),
(818, 1, 18, 622, 1),
(819, 1, 18, 621, 1),
(820, 1, 18, 620, 1),
(821, 1, 18, 619, 1),
(822, 1, 18, 618, 1),
(823, 1, 249, 618, 1),
(824, 1, 249, 619, 1),
(825, 1, 249, 620, 1),
(826, 1, 249, 621, 1),
(827, 1, 249, 622, 1),
(828, 1, 1, 621, 1),
(829, 1, 1, 622, 1),
(830, 1, 1, 620, 1),
(831, 1, 1, 619, 1),
(832, 1, 1, 618, 1),
(833, 1, 8, 618, 1),
(834, 1, 8, 619, 1),
(835, 1, 8, 620, 1),
(836, 1, 8, 621, 1),
(837, 1, 8, 622, 1),
(838, 1, 250, 622, 1),
(839, 1, 250, 621, 1),
(840, 1, 250, 620, 1),
(841, 1, 250, 619, 1),
(842, 1, 250, 618, 1),
(843, 1, 25, 618, 1),
(844, 1, 25, 619, 1),
(845, 1, 25, 620, 1),
(846, 1, 25, 621, 1),
(847, 1, 25, 622, 1),
(848, 1, 24, 622, 1),
(849, 1, 24, 621, 1),
(850, 1, 24, 619, 1),
(851, 1, 24, 620, 1),
(852, 1, 24, 618, 1),
(853, 1, 19, 618, 1),
(854, 1, 23, 618, 1),
(855, 1, 23, 619, 1),
(856, 1, 19, 619, 1),
(857, 1, 19, 620, 1),
(858, 1, 23, 620, 1),
(859, 1, 23, 621, 1),
(860, 1, 19, 621, 1),
(861, 1, 19, 622, 1),
(862, 1, 23, 622, 1),
(863, 1, 248, 618, 1),
(864, 1, 248, 619, 1),
(865, 1, 248, 620, 1),
(866, 1, 248, 621, 1),
(867, 1, 248, 622, 1),
(868, 1, 28, 622, 1),
(869, 1, 28, 621, 1),
(870, 1, 28, 620, 1),
(871, 1, 28, 619, 1),
(872, 1, 28, 618, 1),
(873, 1, 21, 618, 1),
(874, 1, 21, 619, 1),
(875, 1, 21, 620, 1),
(876, 1, 21, 621, 1),
(877, 1, 21, 622, 1),
(878, 1, 246, 622, 1),
(879, 1, 246, 621, 1),
(880, 1, 246, 620, 1),
(881, 1, 246, 619, 1),
(882, 1, 246, 618, 1),
(883, 1, 4, 618, 1),
(884, 1, 4, 619, 1),
(885, 1, 4, 620, 1),
(886, 1, 4, 621, 1),
(887, 1, 4, 622, 1),
(888, 1, 15, 622, 1),
(889, 1, 15, 621, 1),
(890, 1, 15, 620, 1),
(891, 1, 15, 619, 1),
(892, 1, 15, 618, 1),
(893, 1, 7, 618, 1),
(894, 1, 7, 619, 1),
(895, 1, 7, 620, 1),
(896, 1, 7, 621, 1),
(897, 1, 7, 622, 1),
(898, 1, 252, 618, 1),
(899, 1, 17, 618, 1),
(900, 1, 247, 618, 1),
(901, 1, 247, 619, 1),
(902, 1, 17, 619, 1),
(903, 1, 252, 619, 1),
(904, 1, 252, 620, 1),
(905, 1, 17, 620, 1),
(906, 1, 247, 620, 1),
(907, 1, 247, 621, 1),
(908, 1, 17, 621, 1),
(909, 1, 252, 621, 1),
(910, 1, 252, 622, 1),
(911, 1, 17, 622, 1),
(912, 1, 247, 622, 1),
(913, 1, 5, 618, 1),
(914, 1, 22, 618, 1),
(915, 1, 9, 618, 1),
(916, 1, 3, 618, 1),
(917, 1, 3, 619, 1),
(918, 1, 9, 619, 1),
(919, 1, 22, 619, 1),
(920, 1, 5, 619, 1),
(921, 1, 5, 620, 1),
(922, 1, 22, 620, 1),
(923, 1, 9, 620, 1),
(924, 1, 3, 620, 1),
(925, 1, 3, 621, 1),
(926, 1, 9, 621, 1),
(927, 1, 22, 621, 1),
(928, 1, 5, 621, 1),
(929, 1, 5, 622, 1),
(930, 1, 22, 622, 1),
(931, 1, 9, 622, 1),
(932, 1, 3, 622, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acciones_resagadas_usuarios`
--

CREATE TABLE `acciones_resagadas_usuarios` (
  `id_accion_resagada_usuario` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `accion_resagada` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `acciones_resagadas_usuarios`
--

INSERT INTO `acciones_resagadas_usuarios` (`id_accion_resagada_usuario`, `id_modulo`, `cedula_usuario`, `accion_resagada`, `status`) VALUES
(269, 18, '30485684', 'borrarDataModuloSS', 1),
(270, 18, '30485684', 'actDT', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora`
--

CREATE TABLE `bitacora` (
  `id_bitacora` int(11) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `resultado_bitacora` varchar(50) NOT NULL,
  `accion` varchar(100) NOT NULL,
  `fecha_bitacora` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bitacora`
--

INSERT INTO `bitacora` (`id_bitacora`, `cedula_usuario`, `id_modulo`, `resultado_bitacora`, `accion`, `fecha_bitacora`, `status`) VALUES
(136, '30485684', 17, 'Éxito', 'Seleccionar uno', '2026-04-24 14:39:44', 1),
(137, '30485684', 17, 'Éxito', 'Seleccionar uno', '2026-04-24 14:39:51', 1),
(141, '30485684', 17, 'Éxito', 'actualizar rol con id: 3', '2026-04-24 14:54:34', 1),
(142, '30485684', 17, 'Éxito', 'actualizar rol con id: 2', '2026-04-24 15:29:55', 1),
(143, '30485684', 17, 'Éxito', 'actualizar rol con id: 3', '2026-04-24 15:30:03', 1),
(144, '30485684', 17, 'Éxito', 'actualizar rol con id: 2', '2026-04-24 15:30:09', 1),
(145, '30485684', 18, 'éxito', 'registrarIva', '2026-04-24 15:34:11', 1),
(146, '30485684', 18, 'éxito', 'registrarIva', '2026-04-24 15:34:17', 1),
(147, '30485684', 18, 'éxito', 'registrarIva', '2026-04-24 15:36:14', 1),
(148, '30485684', 18, 'éxito', 'registrarIva', '2026-04-24 15:36:50', 1),
(149, '30485684', 18, 'éxito', 'registrarIva', '2026-04-24 15:36:54', 1),
(150, '30485684', 18, 'éxito', 'registrarIva', '2026-04-24 15:41:00', 1),
(151, '30485684', 18, 'éxito', 'registrarIva', '2026-04-24 15:46:47', 1),
(152, '30485684', 18, 'éxito', 'registrarIva', '2026-04-24 15:49:02', 1),
(153, '30485684', 4, 'éxito', 'registrar', '2026-04-25 17:05:33', 1),
(157, '30485684', 4, 'éxito', 'registrar', '2026-04-25 18:38:07', 1),
(158, '30485684', 4, 'Éxito', 'Eliminar', '2026-04-25 18:40:01', 1),
(159, '30485684', 4, 'éxito', 'registrar', '2026-04-25 19:14:56', 1),
(160, '30485684', 4, 'fallido', 'actualizar', '2026-04-25 22:02:56', 1),
(162, '30485684', 4, 'fallido', 'actualizar', '2026-04-25 22:30:34', 1),
(164, '30485684', 4, 'fallido', 'actualizar', '2026-04-25 22:30:59', 1),
(167, '30485684', 4, 'éxito', 'actualizar', '2026-04-25 22:32:37', 1),
(168, '30485684', 4, 'éxito', 'actualizar', '2026-04-25 22:32:50', 1),
(169, '30485684', 4, 'éxito', 'actualizar', '2026-04-25 22:33:17', 1),
(170, '30485684', 4, 'éxito', 'actualizar', '2026-04-25 22:33:45', 1),
(171, '30485684', 4, 'éxito', 'actualizar', '2026-04-25 22:33:58', 1),
(172, '30485684', 4, 'éxito', 'actualizar', '2026-04-25 22:34:33', 1),
(173, '30485684', 4, 'éxito', 'actualizar', '2026-04-25 22:35:54', 1),
(174, '30485684', 4, 'éxito', 'actualizar', '2026-04-26 08:36:30', 1),
(175, '30485684', 4, 'éxito', 'actualizar', '2026-04-26 08:40:21', 1),
(176, '30485684', 4, 'éxito', 'actualizar', '2026-04-26 08:54:20', 1),
(177, '30485684', 4, 'éxito', 'actualizar', '2026-04-26 09:03:10', 1),
(178, '30485684', 4, 'éxito', 'actualizar', '2026-04-26 09:03:18', 1),
(179, '30485684', 4, 'éxito', 'actualizar', '2026-04-26 09:03:25', 1),
(180, '30485684', 4, 'éxito', 'actualizar', '2026-04-26 09:04:09', 1),
(181, '30485684', 4, 'éxito', 'actualizar', '2026-04-26 09:21:32', 1),
(182, '30485684', 4, 'éxito', 'registrar', '2026-04-26 09:25:30', 1),
(183, '30485684', 4, 'éxito', 'actualizar', '2026-04-26 10:31:29', 1),
(184, '30485684', 4, 'éxito', 'registrar', '2026-04-26 11:52:39', 1),
(185, '30485684', 4, 'éxito', 'registrar', '2026-04-26 12:01:43', 1),
(186, '30485684', 4, 'Éxito', 'Eliminar', '2026-04-26 12:02:11', 1),
(187, '30485684', 4, 'Éxito', 'Eliminar', '2026-04-26 12:02:13', 1),
(188, '30485684', 4, 'Éxito', 'Eliminar', '2026-04-26 12:02:15', 1),
(189, '30485684', 4, 'Éxito', 'Eliminar', '2026-04-26 12:02:17', 1),
(190, '30485684', 4, 'éxito', 'registrar', '2026-04-26 12:02:45', 1),
(191, '30485684', 4, 'Éxito', 'Eliminar', '2026-04-26 12:03:26', 1),
(192, '30485684', 4, 'éxito', 'registrar', '2026-04-26 12:03:56', 1),
(193, '30485684', 4, 'Fallido', 'Eliminar', '2026-04-26 12:04:04', 1),
(194, '30485684', 4, 'Fallido', 'Eliminar', '2026-04-26 12:06:24', 1),
(195, '30485684', 4, 'Fallido', 'Eliminar', '2026-04-26 12:06:36', 1),
(196, '30485684', 4, 'Fallido', 'Eliminar', '2026-04-26 12:08:09', 1),
(197, '30485684', 4, 'Fallido', 'Eliminar', '2026-04-26 12:12:45', 1),
(198, '30485684', 4, 'Fallido', 'Eliminar', '2026-04-26 12:13:02', 1),
(199, '30485684', 4, 'Fallido', 'Eliminar', '2026-04-26 12:17:59', 1),
(200, '30485684', 4, 'Éxito', 'Eliminar', '2026-04-26 12:19:43', 1),
(201, '30485684', 4, 'éxito', 'registrar', '2026-04-26 12:21:23', 1),
(202, '30485684', 4, 'Éxito', 'Eliminar', '2026-04-26 12:21:27', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iconos_notificaciones`
--

CREATE TABLE `iconos_notificaciones` (
  `id_icono_notificacion` int(11) NOT NULL,
  `path_icono_notificacion` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `iconos_notificaciones`
--

INSERT INTO `iconos_notificaciones` (`id_icono_notificacion`, `path_icono_notificacion`, `status`) VALUES
(1, 'info', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id_modulo` int(11) NOT NULL,
  `nombre_modulo` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id_modulo`, `nombre_modulo`, `status`) VALUES
(1, 'clientes', 1),
(3, 'ventas', 1),
(4, 'productos', 1),
(5, 'servicios', 1),
(7, 'proveedores', 1),
(8, 'compras', 1),
(9, 'usuarios', 1),
(10, 'cambios', 1),
(11, 'reportes', 1),
(13, 'bitacora', 1),
(15, 'promociones', 1),
(16, 'imagenes', 1),
(17, 'roles', 1),
(18, 'cambiosIva', 1),
(19, 'metodos-pago', 1),
(21, 'presentaciones', 1),
(22, 'unidadesMedidas', 1),
(23, 'monedas', 1),
(24, 'materiasPrimas', 1),
(25, 'insumos', 1),
(28, 'permisos', 1),
(29, 'dashboard', 1),
(246, 'producciones', 1),
(247, 'rutas', 1),
(248, 'pedidos', 1),
(249, 'categoriasProductos', 1),
(250, 'empresasEnvios', 1),
(251, 'bancos', 1),
(252, 'repartidores', 1),
(253, 'inventario', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id_notificacion` int(11) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `id_icono_notificacion` int(11) NOT NULL,
  `id_tipo_notificacion` int(11) NOT NULL,
  `tiempo_notificacion` int(11) NOT NULL,
  `titulo_notificacion` varchar(30) NOT NULL,
  `texto_notificacion` varchar(255) NOT NULL,
  `fecha_creacion_notificacion` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id_notificacion`, `cedula_usuario`, `id_icono_notificacion`, `id_tipo_notificacion`, `tiempo_notificacion`, `titulo_notificacion`, `texto_notificacion`, `fecha_creacion_notificacion`, `status`) VALUES
(1, '30485684', 1, 2, 0, 'Precio del IVA actualizado', 'El precio del IVA ha sido actualizado', '2026-04-24 15:41:00', 0),
(2, '30485684', 1, 2, 0, 'Precio del IVA actualizado', 'El precio del IVA ha sido actualizado', '2026-04-24 15:46:47', 0),
(3, '30485684', 1, 2, 0, 'Precio del IVA actualizado', 'El precio del IVA ha sido actualizado', '2026-04-24 15:49:02', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id_permiso` int(11) NOT NULL,
  `nombre_permiso` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id_permiso`, `nombre_permiso`, `status`) VALUES
(618, 'ver', 1),
(619, 'listar', 1),
(620, 'registrar', 1),
(621, 'actualizar', 1),
(622, 'eliminar', 1),
(623, 'ver dashboard', 1),
(624, 'ver historial de cambio', 1),
(625, 'actualizar cambio de divisas', 1),
(626, 'ver historial de cambio del iva', 1),
(627, 'actualizar cambio del iva', 1),
(628, 'ver detalles de las ventas', 1),
(629, 'ver ventas despachadas', 1),
(630, 'ver ventas sin cancelar', 1),
(631, 'ver pedidos en espera', 1),
(632, 'ver pedidos rechazados', 1),
(633, 'gestionar ventas', 1),
(634, 'ver reportes', 1),
(635, 'imprimir reportes de ventas', 1),
(636, 'imprimir reportes de productos', 1),
(637, 'imprimir comandas', 1),
(638, 'asignar roles a usuarios', 1),
(639, 'ver el precio del dólar', 1),
(640, 'ver notificaciones', 1),
(641, 'ver modal de ayuda', 1),
(642, 'ver carrito de compra', 1),
(643, 'ver inventario', 1),
(644, 'ver detalles de promociones', 1),
(645, 'ver bitácora', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`, `status`) VALUES
(1, 'ADMINISTRADOR', 1),
(2, 'OFICINISTA', 1),
(3, 'CLIENTES', 1),
(4, 'DJJ', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_notificaciones`
--

CREATE TABLE `tipos_notificaciones` (
  `id_tipo_notificacion` int(11) NOT NULL,
  `nombre_tipo_notificacion` varchar(30) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_notificaciones`
--

INSERT INTO `tipos_notificaciones` (`id_tipo_notificacion`, `nombre_tipo_notificacion`, `status`) VALUES
(1, 'info', 1),
(2, 'simple', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `cedula_usuario` varchar(20) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `apellido_usuario` varchar(50) NOT NULL,
  `usuario_usuario` varchar(50) NOT NULL,
  `contrasena_usuario` varchar(255) NOT NULL,
  `telefono_usuario` varchar(11) NOT NULL,
  `correo_usuario` varchar(150) NOT NULL,
  `foto_usuario` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`cedula_usuario`, `id_rol`, `nombre_usuario`, `apellido_usuario`, `usuario_usuario`, `contrasena_usuario`, `telefono_usuario`, `correo_usuario`, `foto_usuario`, `status`) VALUES
('12121212', 3, 'ANDER', 'FREITEZ', 'Ander1234', '$2y$10$wlJJpoPxUW08gNRFImD3tuyZFY8tdalmvILxE1N5AdbFKalU9FRsu', '04169000000', 'ander@gmail.com', '', 0),
('1234567', 1, 'ANDERS', 'DAVID', 'ander1234', '$2y$10$tnkCgC.SA4fIWZ1jyJ1Nc.at2Ke5PF0PpZbGwmnBxjFZAeAltJjMW', '12345678901', 'ander2@gmail.com', '', 0),
('30485682', 1, 'Anderson', 'Freitez', 'Ander129', '$2y$10$yhWPXNolGJLGZTlHnyg.w.ZdNCdiU9zpJn..QIYX/NJ3zfGi1h/i.', '04169484649', 'andersonfreitez6@gmail.con', '', 0),
('30485683', 3, 'ANDERSON', 'FREITEZ', 'Ander1234', '$2y$10$VnBXa/h1NQ3Ohj7Upz6M5uywadRezaoEu0t7zeUpHtXc3m1jsJls.', '04169484649', 'andersonfreitez16@gmail.com', '', 0),
('30485684', 1, 'ANDERSON', 'FREITEZ', 'Ander123', '$2y$10$3xuW0Z34n9oScdoEDKoo1.OWgDGnch8iJQn2zbeI/Ci0PRP9qCQke', '04169484649', 'andersonfreitez6@gmail.com', 'usuarios_2026-04-25_09_08_15.jpg?v=2026-04-25_18_39_01', 1),
('30485685', 1, 'ANDERSON', 'APELLIDO', 'Ander4321', '$2y$10$dZ.w3dX5fWLZgwRx1KOaz.kop/X/Qjb.oiHikcChNvFVbGypxkMT6', '04169484649', 'andersonfreitez16@gmail.com', '', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accesos`
--
ALTER TABLE `accesos`
  ADD PRIMARY KEY (`id_acceso`),
  ADD KEY `id_rol_accesos_fk` (`id_rol`),
  ADD KEY `id_modulo` (`id_modulo`),
  ADD KEY `id_permiso_accesos_fk` (`id_permiso`);

--
-- Indices de la tabla `acciones_resagadas_usuarios`
--
ALTER TABLE `acciones_resagadas_usuarios`
  ADD PRIMARY KEY (`id_accion_resagada_usuario`),
  ADD KEY `id_modulo_acciones_resagadas_usuarios_fk` (`id_modulo`),
  ADD KEY `cedula_usuario_acciones_resagadas_usuarios_fk` (`cedula_usuario`);

--
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`id_bitacora`),
  ADD KEY `cedula_usuario_bitacora_fk` (`cedula_usuario`),
  ADD KEY `id_modulo_bitacora_fk` (`id_modulo`);

--
-- Indices de la tabla `iconos_notificaciones`
--
ALTER TABLE `iconos_notificaciones`
  ADD PRIMARY KEY (`id_icono_notificacion`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id_modulo`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id_notificacion`),
  ADD KEY `cedula_usuario_notificaciones_fk` (`cedula_usuario`),
  ADD KEY `icono_notificacion_notificaciones_fk` (`id_icono_notificacion`),
  ADD KEY `id_tipo_notificacion_notificaciones_fk` (`id_tipo_notificacion`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id_permiso`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `tipos_notificaciones`
--
ALTER TABLE `tipos_notificaciones`
  ADD PRIMARY KEY (`id_tipo_notificacion`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`cedula_usuario`),
  ADD KEY `id_rol_usuarios_fk` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accesos`
--
ALTER TABLE `accesos`
  MODIFY `id_acceso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=933;

--
-- AUTO_INCREMENT de la tabla `acciones_resagadas_usuarios`
--
ALTER TABLE `acciones_resagadas_usuarios`
  MODIFY `id_accion_resagada_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=271;

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id_bitacora` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=203;

--
-- AUTO_INCREMENT de la tabla `iconos_notificaciones`
--
ALTER TABLE `iconos_notificaciones`
  MODIFY `id_icono_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=257;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=646;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipos_notificaciones`
--
ALTER TABLE `tipos_notificaciones`
  MODIFY `id_tipo_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `accesos`
--
ALTER TABLE `accesos`
  ADD CONSTRAINT `id_modulo` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_permiso_accesos_fk` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id_permiso`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_rol_accesos_fk` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `acciones_resagadas_usuarios`
--
ALTER TABLE `acciones_resagadas_usuarios`
  ADD CONSTRAINT `cedula_usuario_acciones_resagadas_usuarios_fk` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_modulo_acciones_resagadas_usuarios_fk` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD CONSTRAINT `cedula_usuario_bitacora_fk` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_modulo_bitacora_fk` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `cedula_usuario_notificaciones_fk` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `icono_notificacion_notificaciones_fk` FOREIGN KEY (`id_icono_notificacion`) REFERENCES `iconos_notificaciones` (`id_icono_notificacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_tipo_notificacion_notificaciones_fk` FOREIGN KEY (`id_tipo_notificacion`) REFERENCES `tipos_notificaciones` (`id_tipo_notificacion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `id_rol_usuarios_fk` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
