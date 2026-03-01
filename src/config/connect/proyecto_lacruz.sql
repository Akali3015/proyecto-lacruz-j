-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-03-2026 a las 14:35:21
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
-- Estructura de tabla para la tabla `accesos`
--

CREATE TABLE `accesos` (
  `id_acceso` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `accesos`
--

INSERT INTO `accesos` (`id_acceso`, `id_rol`, `id_permiso`, `id_modulo`, `status`) VALUES
(397, 1, 1, 29, 1),
(398, 1, 1, 28, 1),
(399, 1, 1, 9, 1),
(400, 1, 1, 18, 1),
(401, 1, 2, 18, 1),
(402, 1, 3, 18, 1),
(403, 1, 4, 18, 1),
(404, 1, 5, 18, 1),
(405, 1, 1, 1, 1),
(406, 1, 2, 1, 1),
(407, 1, 3, 1, 1),
(408, 1, 4, 1, 1),
(409, 1, 5, 1, 1),
(410, 1, 1, 8, 1),
(411, 1, 2, 8, 1),
(412, 1, 3, 8, 1),
(413, 1, 4, 8, 1),
(414, 1, 5, 8, 1),
(415, 1, 1, 25, 1),
(416, 1, 2, 25, 1),
(417, 1, 3, 25, 1),
(418, 1, 4, 25, 1),
(419, 1, 5, 25, 1),
(420, 1, 1, 6, 1),
(421, 1, 2, 6, 1),
(422, 1, 1, 20, 1),
(423, 1, 2, 20, 1),
(424, 1, 3, 6, 1),
(425, 1, 3, 20, 1),
(426, 1, 4, 20, 1),
(427, 1, 4, 6, 1),
(428, 1, 5, 6, 1),
(429, 1, 5, 20, 1),
(430, 1, 1, 24, 1),
(431, 1, 1, 19, 1),
(432, 1, 2, 24, 1),
(433, 1, 2, 19, 1),
(434, 1, 3, 19, 1),
(435, 1, 4, 24, 1),
(436, 1, 3, 24, 1),
(437, 1, 4, 19, 1),
(438, 1, 5, 24, 1),
(439, 1, 5, 19, 1),
(440, 1, 1, 23, 1),
(441, 1, 2, 23, 1),
(442, 1, 2, 28, 1),
(443, 1, 3, 28, 1),
(444, 1, 3, 23, 1),
(445, 1, 4, 23, 1),
(446, 1, 4, 28, 1),
(447, 1, 5, 28, 1),
(448, 1, 5, 23, 1),
(449, 1, 1, 21, 1),
(450, 1, 2, 21, 1),
(451, 1, 3, 21, 1),
(452, 1, 4, 21, 1),
(453, 1, 5, 21, 1),
(454, 1, 1, 4, 1),
(455, 1, 2, 4, 1),
(456, 1, 3, 4, 1),
(457, 1, 4, 4, 1),
(458, 1, 5, 4, 1),
(459, 1, 5, 15, 1),
(460, 1, 4, 15, 1),
(461, 1, 3, 15, 1),
(462, 1, 2, 15, 1),
(463, 1, 1, 15, 1),
(464, 1, 1, 7, 1),
(465, 1, 2, 7, 1),
(466, 1, 3, 7, 1),
(467, 1, 4, 7, 1),
(468, 1, 5, 7, 1),
(469, 1, 5, 17, 1),
(470, 1, 4, 17, 1),
(471, 1, 3, 17, 1),
(472, 1, 2, 17, 1),
(473, 1, 1, 17, 1),
(474, 1, 1, 5, 1),
(475, 1, 1, 22, 1),
(476, 1, 2, 22, 1),
(477, 1, 2, 5, 1),
(478, 1, 3, 5, 1),
(479, 1, 3, 22, 1),
(480, 1, 4, 22, 1),
(481, 1, 4, 5, 1),
(482, 1, 5, 5, 1),
(483, 1, 5, 22, 1),
(484, 1, 2, 9, 1),
(485, 1, 2, 3, 1),
(486, 1, 1, 3, 1),
(487, 1, 3, 3, 1),
(488, 1, 4, 9, 1),
(489, 1, 3, 9, 1),
(490, 1, 4, 3, 1),
(491, 1, 5, 3, 1),
(492, 1, 5, 9, 1),
(493, 1, 31, 3, 1),
(494, 1, 22, 3, 1),
(495, 1, 21, 10, 1),
(496, 1, 33, 3, 1),
(497, 1, 11, 11, 1),
(498, 1, 37, 11, 1),
(499, 1, 15, 9, 1),
(500, 1, 23, 9, 1),
(501, 1, 43, 15, 1),
(502, 1, 45, 16, 1),
(503, 1, 19, 13, 1),
(504, 1, 42, 9, 1),
(505, 1, 16, 9, 1),
(506, 1, 14, 9, 1),
(507, 1, 36, 11, 1),
(508, 1, 34, 11, 1),
(509, 1, 10, 3, 1),
(510, 1, 32, 3, 1),
(511, 1, 24, 29, 1),
(512, 1, 8, 3, 1),
(513, 1, 6, 10, 1),
(514, 1, 1, 246, 1),
(515, 1, 2, 246, 1),
(516, 1, 3, 246, 1),
(517, 1, 4, 246, 1),
(518, 1, 5, 246, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acciones`
--

CREATE TABLE `acciones` (
  `id_accion` int(11) NOT NULL,
  `nombre_accion` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `acciones`
--

INSERT INTO `acciones` (`id_accion`, `nombre_accion`, `status`) VALUES
(1, 'Registrar', 1),
(2, 'Actualizar', 1),
(3, 'registrarIva', 1),
(78, 'borrarDataModuloSS', 1),
(79, 'actDT', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acciones_resagadas_usuarios`
--

CREATE TABLE `acciones_resagadas_usuarios` (
  `id_accion_resagada` int(11) NOT NULL,
  `id_accion` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `cedula_usuario` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `acciones_resagadas_usuarios`
--

INSERT INTO `acciones_resagadas_usuarios` (`id_accion_resagada`, `id_accion`, `id_modulo`, `cedula_usuario`, `status`) VALUES
(130, 78, 18, 1234567, 1),
(132, 79, 18, 1234567, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora`
--

CREATE TABLE `bitacora` (
  `id_bitacora` int(11) NOT NULL,
  `cedula_usuario` int(11) NOT NULL,
  `id_accion` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `fecha_bitacora` datetime NOT NULL,
  `resultado_accion_bitacora` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bitacora`
--

INSERT INTO `bitacora` (`id_bitacora`, `cedula_usuario`, `id_accion`, `id_modulo`, `fecha_bitacora`, `resultado_accion_bitacora`, `status`) VALUES
(1, 30485684, 1, 4, '2026-02-07 17:06:44', 'Exito', 1),
(2, 30485684, 1, 4, '2026-02-07 17:07:23', 'Exito', 1),
(3, 30485684, 1, 5, '2026-02-07 19:57:05', 'Fallido', 1),
(4, 30485684, 1, 5, '2026-02-07 20:02:30', 'Fallido', 1),
(5, 30485684, 1, 5, '2026-02-07 20:03:40', 'Exito', 1),
(6, 30485684, 2, 5, '2026-02-07 20:07:32', 'Fallido', 1),
(7, 30485684, 1, 5, '2026-02-07 20:07:46', 'Exito', 1),
(8, 30485684, 3, 18, '2026-02-07 20:38:38', 'éxito', 1),
(9, 30485684, 1, 20, '2026-02-07 20:54:24', 'Exito', 1),
(10, 30485684, 1, 25, '2026-02-07 20:56:23', 'Fallido', 1),
(11, 30485684, 1, 25, '2026-02-07 20:56:40', 'Exito', 1),
(44, 30485684, 3, 18, '2026-02-08 18:20:44', 'éxito', 1),
(45, 30485684, 3, 18, '2026-02-08 18:20:58', 'éxito', 1),
(46, 30485684, 3, 18, '2026-02-08 18:21:33', 'éxito', 1),
(47, 30485684, 3, 18, '2026-02-08 18:21:48', 'éxito', 1),
(48, 30485684, 3, 18, '2026-02-08 18:22:50', 'éxito', 1),
(49, 30485684, 3, 18, '2026-02-08 18:23:44', 'éxito', 1),
(50, 30485684, 3, 18, '2026-02-08 18:25:42', 'éxito', 1),
(51, 30485684, 3, 18, '2026-02-08 18:25:48', 'éxito', 1),
(52, 30485684, 3, 18, '2026-02-08 18:26:53', 'éxito', 1),
(53, 30485684, 3, 18, '2026-02-08 18:27:39', 'éxito', 1),
(54, 30485684, 3, 18, '2026-02-08 18:30:10', 'éxito', 1),
(55, 30485684, 3, 18, '2026-02-08 18:30:33', 'éxito', 1),
(56, 30485684, 3, 18, '2026-02-08 18:30:49', 'éxito', 1),
(57, 30485684, 3, 18, '2026-02-08 18:31:29', 'éxito', 1),
(58, 30485684, 3, 18, '2026-02-08 18:32:19', 'éxito', 1),
(59, 30485684, 3, 18, '2026-02-08 18:33:16', 'éxito', 1),
(60, 30485684, 3, 18, '2026-02-08 18:33:38', 'éxito', 1),
(61, 30485684, 3, 18, '2026-02-08 18:34:13', 'éxito', 1),
(62, 30485684, 3, 18, '2026-02-08 18:35:10', 'éxito', 1),
(63, 30485684, 3, 18, '2026-02-08 18:35:45', 'éxito', 1),
(64, 30485684, 3, 18, '2026-02-08 18:36:37', 'éxito', 1),
(65, 30485684, 3, 18, '2026-02-08 19:30:15', 'éxito', 1),
(66, 30485684, 3, 18, '2026-02-08 19:32:01', 'éxito', 1),
(67, 30485684, 3, 18, '2026-02-08 19:32:07', 'éxito', 1),
(68, 30485684, 3, 18, '2026-02-08 19:32:13', 'éxito', 1),
(69, 30485684, 3, 18, '2026-02-08 19:32:30', 'éxito', 1),
(70, 30485684, 3, 18, '2026-02-08 19:32:46', 'éxito', 1),
(71, 30485684, 3, 18, '2026-02-08 19:33:00', 'éxito', 1),
(72, 30485684, 3, 18, '2026-02-08 19:36:24', 'éxito', 1),
(73, 30485684, 3, 18, '2026-02-08 19:42:39', 'éxito', 1),
(74, 30485684, 3, 18, '2026-02-08 19:42:44', 'éxito', 1),
(75, 30485684, 3, 18, '2026-02-08 19:44:45', 'éxito', 1),
(76, 30485684, 3, 18, '2026-02-08 19:46:45', 'éxito', 1),
(77, 30485684, 3, 18, '2026-02-08 19:53:48', 'éxito', 1),
(78, 30485684, 3, 18, '2026-02-08 19:58:48', 'éxito', 1),
(79, 30485684, 3, 18, '2026-02-08 19:59:03', 'éxito', 1),
(80, 30485684, 3, 18, '2026-02-08 20:00:23', 'éxito', 1),
(81, 30485684, 3, 18, '2026-02-08 20:00:31', 'éxito', 1),
(82, 30485684, 3, 18, '2026-02-08 20:01:57', 'éxito', 1),
(83, 30485684, 3, 18, '2026-02-08 20:02:03', 'éxito', 1),
(84, 30485684, 3, 18, '2026-02-08 20:07:58', 'éxito', 1),
(85, 30485684, 3, 18, '2026-02-08 20:23:55', 'éxito', 1),
(86, 30485684, 3, 18, '2026-02-08 20:30:07', 'éxito', 1),
(87, 30485684, 3, 18, '2026-02-08 20:31:23', 'éxito', 1),
(88, 30485684, 3, 18, '2026-02-08 20:49:03', 'éxito', 1),
(89, 30485684, 3, 18, '2026-02-08 20:55:09', 'éxito', 1),
(90, 30485684, 3, 18, '2026-02-08 20:55:14', 'éxito', 1),
(91, 30485684, 3, 18, '2026-02-08 20:58:27', 'éxito', 1),
(92, 30485684, 3, 18, '2026-02-08 20:58:32', 'éxito', 1),
(93, 30485684, 3, 18, '2026-02-08 20:58:36', 'éxito', 1),
(94, 30485684, 3, 18, '2026-02-08 21:11:37', 'éxito', 1),
(95, 30485684, 3, 18, '2026-02-08 21:16:50', 'éxito', 1),
(96, 30485684, 3, 18, '2026-02-08 21:16:54', 'éxito', 1),
(97, 30485684, 3, 18, '2026-02-08 21:23:29', 'éxito', 1),
(98, 30485684, 3, 18, '2026-02-08 21:23:37', 'éxito', 1),
(99, 30485684, 3, 18, '2026-02-08 21:23:56', 'éxito', 1),
(100, 30485684, 3, 18, '2026-02-08 21:24:01', 'éxito', 1),
(101, 30485684, 3, 18, '2026-02-08 21:29:03', 'éxito', 1),
(102, 30485684, 3, 18, '2026-02-08 21:29:08', 'éxito', 1),
(103, 30485684, 3, 18, '2026-02-08 21:29:39', 'éxito', 1),
(104, 30485684, 3, 18, '2026-02-08 21:30:08', 'éxito', 1),
(105, 30485684, 3, 18, '2026-02-08 21:44:06', 'éxito', 1),
(106, 30485684, 3, 18, '2026-02-08 21:44:10', 'éxito', 1),
(107, 30485684, 3, 18, '2026-02-08 21:45:55', 'éxito', 1),
(108, 30485684, 3, 18, '2026-02-08 21:46:07', 'éxito', 1),
(109, 30485684, 3, 18, '2026-02-08 21:46:40', 'éxito', 1),
(110, 30485684, 3, 18, '2026-02-08 21:47:35', 'éxito', 1),
(111, 30485684, 3, 18, '2026-02-08 21:48:07', 'éxito', 1),
(112, 30485684, 3, 18, '2026-02-09 08:58:10', 'éxito', 1),
(113, 30485684, 3, 18, '2026-02-09 09:08:28', 'éxito', 1),
(114, 30485684, 3, 18, '2026-02-09 09:08:37', 'éxito', 1),
(115, 30485684, 3, 18, '2026-02-09 09:08:44', 'éxito', 1),
(116, 30485684, 3, 18, '2026-02-09 09:08:53', 'éxito', 1),
(117, 30485684, 3, 18, '2026-02-09 09:11:21', 'éxito', 1),
(118, 30485684, 3, 18, '2026-02-09 09:20:39', 'éxito', 1),
(119, 30485684, 3, 18, '2026-02-09 09:26:57', 'éxito', 1),
(120, 30485684, 3, 18, '2026-02-09 09:27:20', 'éxito', 1),
(121, 30485684, 3, 18, '2026-02-09 09:27:41', 'éxito', 1),
(122, 30485684, 3, 18, '2026-02-09 21:11:01', 'éxito', 1),
(123, 30485684, 3, 18, '2026-02-09 21:11:34', 'éxito', 1),
(124, 30485684, 3, 18, '2026-02-09 21:11:45', 'éxito', 1),
(125, 30485684, 1, 25, '2026-02-18 19:05:50', 'Exito', 1),
(126, 30485684, 1, 5, '2026-02-23 10:36:12', 'Exito', 1),
(127, 30485684, 2, 4, '2026-02-25 17:32:04', 'Exito', 1),
(128, 30485684, 2, 4, '2026-02-25 17:33:36', 'Exito', 1);

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
(2, 13, '2025-12-05 18:17:41', 1),
(3, 13, '2025-12-05 18:17:55', 1),
(4, 14, '2025-12-05 18:18:02', 1),
(5, 12, '2025-12-06 15:56:00', 1),
(6, 12.01, '2025-12-06 15:56:12', 1),
(7, 12, '2025-12-06 15:56:22', 1),
(8, 323, '2026-02-07 20:38:38', 1),
(9, 12, '2026-02-08 17:30:07', 1),
(10, 12, '2026-02-08 17:30:50', 1),
(11, 23, '2026-02-08 17:31:24', 1),
(12, 23, '2026-02-08 17:32:06', 1),
(13, 23, '2026-02-08 17:42:21', 1),
(14, 23, '2026-02-08 17:42:46', 1),
(15, 23, '2026-02-08 17:46:04', 1),
(16, 23, '2026-02-08 17:46:38', 1),
(17, 23, '2026-02-08 17:49:22', 1),
(18, 23, '2026-02-08 17:52:01', 1),
(19, 23, '2026-02-08 17:53:18', 1),
(20, 23, '2026-02-08 17:53:58', 1),
(21, 23, '2026-02-08 17:54:07', 1),
(22, 23, '2026-02-08 17:55:03', 1),
(23, 23, '2026-02-08 17:57:20', 1),
(24, 23, '2026-02-08 17:59:27', 1),
(25, 23, '2026-02-08 18:01:07', 1),
(26, 23, '2026-02-08 18:03:36', 1),
(27, 23, '2026-02-08 18:04:02', 1),
(28, 2, '2026-02-08 18:04:13', 1),
(29, 2, '2026-02-08 18:05:34', 1),
(30, 2, '2026-02-08 18:06:35', 1),
(31, 122, '2026-02-08 18:10:16', 1),
(32, 23, '2026-02-08 18:11:16', 1),
(33, 45, '2026-02-08 18:11:34', 1),
(34, 67, '2026-02-08 18:13:24', 1),
(35, 45, '2026-02-08 18:14:19', 1),
(36, 67, '2026-02-08 18:15:26', 1),
(37, 45, '2026-02-08 18:15:35', 1),
(38, 23, '2026-02-08 18:16:21', 1),
(39, 43, '2026-02-08 18:17:33', 1),
(40, 12, '2026-02-08 18:17:55', 1),
(41, 23, '2026-02-08 18:20:44', 1),
(42, 23, '2026-02-08 18:20:58', 1),
(43, 23, '2026-02-08 18:21:33', 1),
(44, 23, '2026-02-08 18:21:48', 1),
(45, 23, '2026-02-08 18:22:50', 1),
(46, 23, '2026-02-08 18:23:44', 1),
(47, 67, '2026-02-08 18:25:42', 1),
(48, 67, '2026-02-08 18:25:48', 1),
(49, 23, '2026-02-08 18:26:53', 1),
(50, 67, '2026-02-08 18:27:39', 1),
(51, 67, '2026-02-08 18:30:10', 1),
(52, 67, '2026-02-08 18:30:33', 1),
(53, 67, '2026-02-08 18:30:49', 1),
(54, 67, '2026-02-08 18:31:29', 1),
(55, 67, '2026-02-08 18:32:19', 1),
(56, 67, '2026-02-08 18:33:16', 1),
(57, 67, '2026-02-08 18:33:38', 1),
(58, 67, '2026-02-08 18:34:13', 1),
(59, 67, '2026-02-08 18:35:10', 1),
(60, 67, '2026-02-08 18:35:45', 1),
(61, 67, '2026-02-08 18:36:37', 1),
(62, 67, '2026-02-08 19:30:15', 1),
(63, 23, '2026-02-08 19:32:01', 1),
(64, 23, '2026-02-08 19:32:07', 1),
(65, 23, '2026-02-08 19:32:13', 1),
(66, 23, '2026-02-08 19:32:30', 1),
(67, 23, '2026-02-08 19:32:46', 1),
(68, 23, '2026-02-08 19:33:00', 1),
(69, 67, '2026-02-08 19:36:24', 1),
(70, 67, '2026-02-08 19:42:39', 1),
(71, 67, '2026-02-08 19:42:44', 1),
(72, 67, '2026-02-08 19:44:45', 1),
(73, 23, '2026-02-08 19:46:45', 1),
(74, 23, '2026-02-08 19:53:48', 1),
(75, 23, '2026-02-08 19:58:48', 1),
(76, 23, '2026-02-08 19:59:03', 1),
(77, 23, '2026-02-08 20:00:23', 1),
(78, 45, '2026-02-08 20:00:31', 1),
(79, 67, '2026-02-08 20:01:57', 1),
(80, 45, '2026-02-08 20:02:03', 1),
(81, 23, '2026-02-08 20:07:58', 1),
(82, 23, '2026-02-08 20:23:55', 1),
(83, 23, '2026-02-08 20:30:07', 1),
(84, 67, '2026-02-08 20:31:23', 1),
(85, 67, '2026-02-08 20:49:03', 1),
(86, 23, '2026-02-08 20:55:09', 1),
(87, 12, '2026-02-08 20:55:14', 1),
(88, 67, '2026-02-08 20:58:27', 1),
(89, 45, '2026-02-08 20:58:32', 1),
(90, 16, '2026-02-08 20:58:36', 1),
(91, 23, '2026-02-08 21:11:37', 1),
(92, 45, '2026-02-08 21:16:50', 1),
(93, 16, '2026-02-08 21:16:54', 1),
(94, 45, '2026-02-08 21:23:29', 1),
(95, 12, '2026-02-08 21:23:37', 1),
(96, 67, '2026-02-08 21:23:56', 1),
(97, 67, '2026-02-08 21:24:01', 1),
(98, 67, '2026-02-08 21:29:03', 1),
(99, 16, '2026-02-08 21:29:08', 1),
(100, 23, '2026-02-08 21:29:39', 1),
(101, 67, '2026-02-08 21:30:08', 1),
(102, 23, '2026-02-08 21:44:06', 1),
(103, 12, '2026-02-08 21:44:10', 1),
(104, 67, '2026-02-08 21:45:55', 1),
(105, 45, '2026-02-08 21:46:07', 1),
(106, 67, '2026-02-08 21:46:40', 1),
(107, 45, '2026-02-08 21:47:35', 1),
(108, 12, '2026-02-08 21:48:07', 1),
(109, 67, '2026-02-09 08:58:10', 1),
(110, 67, '2026-02-09 09:08:28', 1),
(111, 12, '2026-02-09 09:08:37', 1),
(112, 16, '2026-02-09 09:08:44', 1),
(113, 23, '2026-02-09 09:08:53', 1),
(114, 12, '2026-02-09 09:11:21', 1),
(115, 12, '2026-02-09 09:20:39', 1),
(116, 23, '2026-02-09 09:26:57', 1),
(117, 23, '2026-02-09 09:27:19', 1),
(118, 23, '2026-02-09 09:27:41', 1),
(119, 12, '2026-02-09 21:11:01', 1),
(120, 12, '2026-02-09 21:11:34', 1),
(121, 23, '2026-02-09 21:11:45', 1);

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
(2, 5, 2, '2025-12-06 20:13:37', 1),
(3, 2, 3, '2025-12-06 20:13:48', 1),
(4, 1, 257.93, '2025-12-06 20:14:15', 1),
(5, 1, 250, '2025-12-11 18:35:07', 1),
(6, 2, 250, '2025-12-11 19:04:46', 1),
(7, 5, 1, '2025-12-14 16:30:34', 1),
(8, 1, 23, '2026-02-07 20:44:34', 1),
(9, 1, 12, '2026-02-09 20:46:09', 1),
(10, 1, 250, '2026-02-09 20:46:55', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `rif_cedula_cliente` varchar(20) NOT NULL,
  `razon_social_cliente` varchar(50) NOT NULL,
  `telefono_cliente` varchar(11) NOT NULL,
  `correo_cliente` varchar(150) NOT NULL,
  `direccion_cliente` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`rif_cedula_cliente`, `razon_social_cliente`, `telefono_cliente`, `correo_cliente`, `direccion_cliente`, `status`) VALUES
('12345678', 'ANDERSON', '04169484648', 'andersonfreitez@gmail.com', 'SANAREsws', 1),
('123456782', 'ANDERSONA', '04169484647', 'andersonfreitz@gmail.com', 'hfgyhftgn', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id_compra` int(11) NOT NULL,
  `rif_proveedor` varchar(20) NOT NULL,
  `cedula_usuario` int(11) NOT NULL,
  `fecha_compra` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id_compra`, `rif_proveedor`, `cedula_usuario`, `fecha_compra`, `status`) VALUES
(4, '30485684', 30485684, '2026-02-15 15:02:00', 1),
(5, '30485684', 30485684, '2026-02-15 15:08:00', 1),
(7, '30485684', 30485684, '2026-02-18 20:56:00', 1),
(8, '30485684', 30485684, '2026-02-18 20:59:00', 1),
(9, '30485684', 30485684, '2026-02-18 20:59:00', 1),
(10, '30485684', 30485684, '2026-02-18 21:04:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_pagos`
--

CREATE TABLE `detalles_pagos` (
  `id_detalle_pago` int(11) NOT NULL,
  `id_pago` int(11) NOT NULL,
  `id_moneda` int(11) NOT NULL,
  `id_metodo_pago` int(11) NOT NULL,
  `monto_pago` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumos`
--

CREATE TABLE `insumos` (
  `id_insumo` int(11) NOT NULL,
  `nombre_insumo` varchar(50) NOT NULL,
  `precio_insumo` float NOT NULL,
  `stock_insumo` int(15) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `insumos`
--

INSERT INTO `insumos` (`id_insumo`, `nombre_insumo`, `precio_insumo`, `stock_insumo`, `status`) VALUES
(1, 'BOLSA', 1, 2, 1),
(2, 'BOLSASS', 233, 34, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumos_compras`
--

CREATE TABLE `insumos_compras` (
  `id_insumo_compra` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `id_insumo` int(11) NOT NULL,
  `cantidad_insumo` int(15) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `insumos_compras`
--

INSERT INTO `insumos_compras` (`id_insumo_compra`, `id_compra`, `id_insumo`, `cantidad_insumo`, `status`) VALUES
(3, 10, 1, 1, 1),
(4, 10, 2, 12, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumos_ventas`
--

CREATE TABLE `insumos_ventas` (
  `id_insumo_venta` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `id_insumo` int(11) NOT NULL,
  `cantidad_insumo` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_primas`
--

CREATE TABLE `materias_primas` (
  `id_materia_prima` int(11) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `nombre_materia_prima` varchar(50) NOT NULL,
  `stock_materia_prima` int(15) NOT NULL,
  `costo_materia_prima` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias_primas`
--

INSERT INTO `materias_primas` (`id_materia_prima`, `id_unidad_medida`, `nombre_materia_prima`, `stock_materia_prima`, `costo_materia_prima`, `status`) VALUES
(1, 2, 'SULFURO', 870, 10, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_primas_compras`
--

CREATE TABLE `materias_primas_compras` (
  `id_materia_prima_compra` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `id_materia_prima` int(11) NOT NULL,
  `cantidad_materia_prima` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias_primas_compras`
--

INSERT INTO `materias_primas_compras` (`id_materia_prima_compra`, `id_compra`, `id_materia_prima`, `cantidad_materia_prima`, `status`) VALUES
(1, 7, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_primas_presentaciones`
--

CREATE TABLE `materias_primas_presentaciones` (
  `id_materia_prima_presentacion` int(11) NOT NULL,
  `id_materia_prima` int(11) NOT NULL,
  `id_presentacion` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias_primas_presentaciones`
--

INSERT INTO `materias_primas_presentaciones` (`id_materia_prima_presentacion`, `id_materia_prima`, `id_presentacion`, `status`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 1),
(3, 1, 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_primas_productos`
--

CREATE TABLE `materias_primas_productos` (
  `id_materia_prima_producto` int(11) NOT NULL,
  `id_materia_prima` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad_materia_prima` int(15) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias_primas_productos`
--

INSERT INTO `materias_primas_productos` (`id_materia_prima_producto`, `id_materia_prima`, `id_producto`, `cantidad_materia_prima`, `status`) VALUES
(1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodos_pagos`
--

CREATE TABLE `metodos_pagos` (
  `id_metodo_pago` int(11) NOT NULL,
  `nombre_metodo_pago` varchar(50) NOT NULL,
  `necesita_moneda` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `metodos_pagos`
--

INSERT INTO `metodos_pagos` (`id_metodo_pago`, `nombre_metodo_pago`, `necesita_moneda`, `status`) VALUES
(1, 'EFECTIVO', 1, 1),
(2, 'TRANSFERENCIA', 0, 1),
(3, 'PAGO MÓVIL', 0, 1),
(4, 'ZELLE', 1, 0),
(5, 'BINANCE', 1, 0),
(6, 'BINANCE', 1, 1),
(7, 'ZELLE', 1, 1),
(8, 'RRRR', 0, 0);

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
(6, 'materias_primas', 1),
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
(20, 'Materias Primas', 1),
(21, 'presentaciones', 1),
(22, 'unidadesMedidas', 1),
(23, 'monedas', 1),
(24, 'materiasPrimas', 1),
(25, 'Insumos', 1),
(28, 'permisos', 1),
(29, 'dashboard', 1),
(246, 'producciones', 1);

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
(1, 'DÓLAR', '$', 250, 1),
(2, 'EURO', '€', 250, 1),
(3, 'YUAN', '¥', 12, 1),
(4, 'AGUACATE', '$O', 1, 0),
(5, 'BÓLIVAR', 'BS', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id_notificacion` int(11) NOT NULL,
  `titulo_notificacion` varchar(30) NOT NULL,
  `tipo_notificacion` varchar(30) NOT NULL,
  `tiempo_notificacion` int(10) NOT NULL,
  `icono_notificacion` varchar(50) NOT NULL,
  `texto_notificacion` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id_notificacion`, `titulo_notificacion`, `tipo_notificacion`, `tiempo_notificacion`, `icono_notificacion`, `texto_notificacion`, `status`) VALUES
(29, 'Precio del IVA actualizado', 'simple', 0, 'info', 'El precio del IVA ha sido actualizado', 1),
(30, 'Precio del IVA actualizado', 'info', 0, 'info', 'El precio del IVA ha sido actualizado', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones_usuarios`
--

CREATE TABLE `notificaciones_usuarios` (
  `id_notificacion_usuario` int(11) NOT NULL,
  `cedula_usuario` int(11) NOT NULL,
  `id_notificacion` int(11) NOT NULL,
  `fecha_creacion_notificacion` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones_usuarios`
--

INSERT INTO `notificaciones_usuarios` (`id_notificacion_usuario`, `cedula_usuario`, `id_notificacion`, `fecha_creacion_notificacion`, `status`) VALUES
(103, 1234567, 30, '2026-02-08 20:49:03', 1),
(104, 30485684, 30, '2026-02-08 20:49:03', 0),
(105, 1234567, 30, '2026-02-08 20:55:10', 1),
(106, 30485684, 30, '2026-02-08 20:55:10', 0),
(107, 1234567, 30, '2026-02-08 20:55:14', 1),
(108, 30485684, 30, '2026-02-08 20:55:14', 0),
(109, 1234567, 30, '2026-02-08 20:58:27', 1),
(110, 30485684, 30, '2026-02-08 20:58:27', 0),
(111, 1234567, 30, '2026-02-08 20:58:32', 1),
(112, 30485684, 30, '2026-02-08 20:58:32', 0),
(113, 1234567, 30, '2026-02-08 20:58:36', 1),
(114, 30485684, 30, '2026-02-08 20:58:36', 0),
(115, 1234567, 30, '2026-02-08 21:11:37', 1),
(116, 30485684, 30, '2026-02-08 21:11:37', 0),
(117, 1234567, 30, '2026-02-08 21:16:50', 1),
(118, 30485684, 30, '2026-02-08 21:16:50', 0),
(119, 1234567, 30, '2026-02-08 21:16:55', 1),
(120, 30485684, 30, '2026-02-08 21:16:55', 0),
(121, 1234567, 30, '2026-02-08 21:23:29', 1),
(122, 30485684, 30, '2026-02-08 21:23:29', 0),
(123, 1234567, 30, '2026-02-08 21:23:37', 1),
(124, 30485684, 30, '2026-02-08 21:23:37', 0),
(125, 1234567, 30, '2026-02-08 21:23:56', 1),
(126, 30485684, 30, '2026-02-08 21:23:56', 0),
(127, 1234567, 30, '2026-02-08 21:24:02', 1),
(128, 30485684, 30, '2026-02-08 21:24:02', 0),
(129, 1234567, 30, '2026-02-08 21:29:03', 1),
(130, 30485684, 30, '2026-02-08 21:29:03', 0),
(131, 1234567, 30, '2026-02-08 21:29:08', 1),
(132, 30485684, 30, '2026-02-08 21:29:08', 0),
(133, 1234567, 30, '2026-02-08 21:29:39', 1),
(134, 30485684, 30, '2026-02-08 21:29:39', 0),
(135, 1234567, 30, '2026-02-08 21:30:08', 1),
(136, 30485684, 30, '2026-02-08 21:30:08', 0),
(137, 1234567, 30, '2026-02-08 21:44:06', 1),
(138, 30485684, 30, '2026-02-08 21:44:06', 0),
(139, 1234567, 30, '2026-02-08 21:44:11', 1),
(140, 30485684, 30, '2026-02-08 21:44:11', 0),
(141, 1234567, 30, '2026-02-08 21:45:55', 1),
(142, 30485684, 30, '2026-02-08 21:45:55', 0),
(143, 1234567, 30, '2026-02-08 21:46:07', 1),
(144, 30485684, 30, '2026-02-08 21:46:07', 0),
(145, 1234567, 30, '2026-02-08 21:46:40', 1),
(146, 30485684, 30, '2026-02-08 21:46:40', 0),
(147, 1234567, 30, '2026-02-08 21:47:36', 1),
(148, 30485684, 30, '2026-02-08 21:47:36', 0),
(149, 1234567, 30, '2026-02-08 21:48:07', 1),
(150, 30485684, 30, '2026-02-08 21:48:07', 0),
(151, 1234567, 30, '2026-02-09 08:58:10', 1),
(152, 30485684, 30, '2026-02-09 08:58:10', 0),
(153, 1234567, 30, '2026-02-09 09:08:28', 1),
(154, 30485684, 30, '2026-02-09 09:08:28', 0),
(155, 1234567, 30, '2026-02-09 09:08:37', 1),
(156, 30485684, 30, '2026-02-09 09:08:37', 0),
(157, 1234567, 30, '2026-02-09 09:08:44', 1),
(158, 30485684, 30, '2026-02-09 09:08:44', 0),
(159, 1234567, 30, '2026-02-09 09:08:53', 1),
(160, 30485684, 30, '2026-02-09 09:08:53', 0),
(161, 1234567, 30, '2026-02-09 09:11:21', 1),
(162, 30485684, 30, '2026-02-09 09:11:21', 0),
(163, 1234567, 30, '2026-02-09 09:20:39', 1),
(164, 30485684, 30, '2026-02-09 09:20:39', 0),
(165, 1234567, 30, '2026-02-09 09:26:57', 1),
(166, 30485684, 30, '2026-02-09 09:26:57', 0),
(167, 1234567, 30, '2026-02-09 09:27:20', 1),
(168, 30485684, 30, '2026-02-09 09:27:20', 0),
(169, 1234567, 30, '2026-02-09 09:27:41', 1),
(170, 30485684, 30, '2026-02-09 09:27:41', 0),
(171, 1234567, 30, '2026-02-09 21:11:01', 1),
(172, 30485684, 30, '2026-02-09 21:11:01', 0),
(173, 1234567, 30, '2026-02-09 21:11:34', 1),
(174, 30485684, 30, '2026-02-09 21:11:34', 0),
(175, 1234567, 30, '2026-02-09 21:11:45', 1),
(176, 30485684, 30, '2026-02-09 21:11:45', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `fecha_pago` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id_permiso` int(11) NOT NULL,
  `nombre_permiso` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id_permiso`, `nombre_permiso`, `status`) VALUES
(1, 'ver', 1),
(2, 'listar', 1),
(3, 'registrar', 1),
(4, 'actualizar', 1),
(5, 'eliminar', 1),
(6, 'ver historial de cambio', 1),
(8, 'ver detalles de las ventas', 1),
(10, 'ver ventas sin cancelar', 1),
(11, 'imprimir reportes de ventas', 1),
(14, 'asignar roles a usuarios', 1),
(15, 'ver el precio del dólar', 1),
(16, 'ver notificaciones', 1),
(19, 'ver bitácora', 1),
(21, 'actualizar cambio de divisas', 1),
(22, 'ver ventas despachadas', 1),
(23, 'ver modal de ayuda', 1),
(24, 'ver dashboard', 1),
(25, 'ver dashboard', 0),
(26, 'ver historial de cambio', 0),
(27, 'actualizar cambio de divisas', 0),
(28, 'ver detalles de las ventas', 0),
(29, 'ver ventas despachadas', 0),
(30, 'ver ventas sin cancelar', 0),
(31, 'ver pedidos en espera', 1),
(32, 'ver pedidos rechazados', 1),
(33, 'gestionar ventas', 1),
(34, 'ver reportes', 1),
(35, 'imprimir reportes de ventas', 0),
(36, 'imprimir reportes de productos', 1),
(37, 'imprimir comandas', 1),
(38, 'asignar roles a usuarios', 0),
(39, 'ver el precio del dólar', 0),
(40, 'ver notificaciones', 0),
(41, 'ver modal de ayuda', 0),
(42, 'ver carrito de compra', 1),
(43, 'ver detalles de promociones', 1),
(44, 'ver bitácora', 0),
(45, 'transformar imagenes', 1),
(46, 'ver dashboard', 0),
(47, 'ver historial de cambio', 0),
(48, 'actualizar cambio de divisas', 0),
(49, 'ver detalles de las ventas', 0),
(50, 'ver ventas despachadas', 0),
(51, 'ver ventas sin cancelar', 0),
(52, 'ver pedidos en espera', 1),
(53, 'ver pedidos rechazados', 1),
(54, 'gestionar ventas', 1),
(55, 'ver reportes', 1),
(56, 'imprimir reportes de ventas', 0),
(57, 'imprimir reportes de productos', 1),
(58, 'imprimir comandas', 1),
(59, 'asignar roles a usuarios', 0),
(60, 'ver el precio del dólar', 0),
(61, 'ver notificaciones', 0),
(62, 'ver modal de ayuda', 0),
(63, 'ver carrito de compra', 1),
(64, 'ver detalles de promociones', 1),
(65, 'ver bitácora', 0),
(66, 'transformar imagenes', 1),
(67, 'ver dashboard', 0),
(68, 'ver historial de cambio', 0),
(69, 'actualizar cambio de divisas', 0),
(70, 'ver detalles de las ventas', 0),
(71, 'ver ventas despachadas', 0),
(72, 'ver ventas sin cancelar', 0),
(73, 'ver pedidos en espera', 1),
(74, 'ver pedidos rechazados', 1),
(75, 'gestionar ventas', 1),
(76, 'ver reportes', 1),
(77, 'imprimir reportes de ventas', 0),
(78, 'imprimir reportes de productos', 1),
(79, 'imprimir comandas', 1),
(80, 'asignar roles a usuarios', 0),
(81, 'ver el precio del dólar', 0),
(82, 'ver notificaciones', 0),
(83, 'ver modal de ayuda', 0),
(84, 'ver carrito de compra', 1),
(85, 'ver detalles de promociones', 1),
(86, 'ver bitácora', 0),
(87, 'transformar imagenes', 1),
(88, 'ver dashboard', 0),
(89, 'ver historial de cambio', 0),
(90, 'actualizar cambio de divisas', 0),
(91, 'ver detalles de las ventas', 0),
(92, 'ver ventas despachadas', 0),
(93, 'ver ventas sin cancelar', 0),
(94, 'ver pedidos en espera', 1),
(95, 'ver pedidos rechazados', 1),
(96, 'gestionar ventas', 1),
(97, 'ver reportes', 1),
(98, 'imprimir reportes de ventas', 0),
(99, 'imprimir reportes de productos', 1),
(100, 'imprimir comandas', 1),
(101, 'asignar roles a usuarios', 0),
(102, 'ver el precio del dólar', 0),
(103, 'ver notificaciones', 0),
(104, 'ver modal de ayuda', 0),
(105, 'ver carrito de compra', 1),
(106, 'ver detalles de promociones', 1),
(107, 'ver bitácora', 0),
(108, 'transformar imagenes', 1),
(109, 'ver dashboard', 0),
(110, 'ver historial de cambio', 0),
(111, 'actualizar cambio de divisas', 0),
(112, 'ver detalles de las ventas', 0),
(113, 'ver ventas despachadas', 0),
(114, 'ver ventas sin cancelar', 0),
(115, 'ver pedidos en espera', 1),
(116, 'ver pedidos rechazados', 1),
(117, 'gestionar ventas', 1),
(118, 'ver reportes', 1),
(119, 'imprimir reportes de ventas', 0),
(120, 'imprimir reportes de productos', 1),
(121, 'imprimir comandas', 1),
(122, 'asignar roles a usuarios', 0),
(123, 'ver el precio del dólar', 0),
(124, 'ver notificaciones', 0),
(125, 'ver modal de ayuda', 0),
(126, 'ver carrito de compra', 1),
(127, 'ver detalles de promociones', 1),
(128, 'ver bitácora', 0),
(129, 'transformar imagenes', 1),
(130, 'ver dashboard', 0),
(131, 'ver historial de cambio', 0),
(132, 'actualizar cambio de divisas', 0),
(133, 'ver detalles de las ventas', 0),
(134, 'ver ventas despachadas', 0),
(135, 'ver ventas sin cancelar', 0),
(136, 'ver pedidos en espera', 1),
(137, 'ver pedidos rechazados', 1),
(138, 'gestionar ventas', 1),
(139, 'ver reportes', 1),
(140, 'imprimir reportes de ventas', 0),
(141, 'imprimir reportes de productos', 1),
(142, 'imprimir comandas', 1),
(143, 'asignar roles a usuarios', 0),
(144, 'ver el precio del dólar', 0),
(145, 'ver notificaciones', 0),
(146, 'ver modal de ayuda', 0),
(147, 'ver carrito de compra', 1),
(148, 'ver detalles de promociones', 1),
(149, 'ver bitácora', 0),
(150, 'transformar imagenes', 1),
(151, 'ver dashboard', 0),
(152, 'ver historial de cambio', 0),
(153, 'actualizar cambio de divisas', 0),
(154, 'ver detalles de las ventas', 0),
(155, 'ver ventas despachadas', 0),
(156, 'ver ventas sin cancelar', 0),
(157, 'ver pedidos en espera', 1),
(158, 'ver pedidos rechazados', 1),
(159, 'gestionar ventas', 1),
(160, 'ver reportes', 1),
(161, 'imprimir reportes de ventas', 0),
(162, 'imprimir reportes de productos', 1),
(163, 'imprimir comandas', 1),
(164, 'asignar roles a usuarios', 0),
(165, 'ver el precio del dólar', 0),
(166, 'ver notificaciones', 0),
(167, 'ver modal de ayuda', 0),
(168, 'ver carrito de compra', 1),
(169, 'ver detalles de promociones', 1),
(170, 'ver bitácora', 0),
(171, 'transformar imagenes', 1),
(172, 'ver dashboard', 0),
(173, 'ver historial de cambio', 0),
(174, 'actualizar cambio de divisas', 0),
(175, 'ver detalles de las ventas', 0),
(176, 'ver ventas despachadas', 0),
(177, 'ver ventas sin cancelar', 0),
(178, 'ver pedidos en espera', 1),
(179, 'ver pedidos rechazados', 1),
(180, 'gestionar ventas', 1),
(181, 'ver reportes', 1),
(182, 'imprimir reportes de ventas', 0),
(183, 'imprimir reportes de productos', 1),
(184, 'imprimir comandas', 1),
(185, 'asignar roles a usuarios', 0),
(186, 'ver el precio del dólar', 0),
(187, 'ver notificaciones', 0),
(188, 'ver modal de ayuda', 0),
(189, 'ver carrito de compra', 1),
(190, 'ver detalles de promociones', 1),
(191, 'ver bitácora', 0),
(192, 'transformar imagenes', 1),
(193, 'ver dashboard', 0),
(194, 'ver historial de cambio', 0),
(195, 'actualizar cambio de divisas', 0),
(196, 'ver detalles de las ventas', 0),
(197, 'ver ventas despachadas', 0),
(198, 'ver ventas sin cancelar', 0),
(199, 'ver pedidos en espera', 1),
(200, 'ver pedidos rechazados', 1),
(201, 'gestionar ventas', 1),
(202, 'ver reportes', 1),
(203, 'imprimir reportes de ventas', 0),
(204, 'imprimir reportes de productos', 1),
(205, 'imprimir comandas', 1),
(206, 'asignar roles a usuarios', 0),
(207, 'ver el precio del dólar', 0),
(208, 'ver notificaciones', 0),
(209, 'ver modal de ayuda', 0),
(210, 'ver carrito de compra', 1),
(211, 'ver detalles de promociones', 1),
(212, 'ver bitácora', 0),
(213, 'transformar imagenes', 1),
(214, 'ver dashboard', 0),
(215, 'ver historial de cambio', 0),
(216, 'actualizar cambio de divisas', 0),
(217, 'ver detalles de las ventas', 0),
(218, 'ver ventas despachadas', 0),
(219, 'ver ventas sin cancelar', 0),
(220, 'ver pedidos en espera', 1),
(221, 'ver pedidos rechazados', 1),
(222, 'gestionar ventas', 1),
(223, 'ver reportes', 1),
(224, 'imprimir reportes de ventas', 0),
(225, 'imprimir reportes de productos', 1),
(226, 'imprimir comandas', 1),
(227, 'asignar roles a usuarios', 0),
(228, 'ver el precio del dólar', 0),
(229, 'ver notificaciones', 0),
(230, 'ver modal de ayuda', 0),
(231, 'ver carrito de compra', 1),
(232, 'ver detalles de promociones', 1),
(233, 'ver bitácora', 0),
(234, 'transformar imagenes', 1),
(235, 'ver dashboard', 0),
(236, 'ver historial de cambio', 0),
(237, 'actualizar cambio de divisas', 0),
(238, 'ver detalles de las ventas', 0),
(239, 'ver ventas despachadas', 0),
(240, 'ver ventas sin cancelar', 0),
(241, 'ver pedidos en espera', 1),
(242, 'ver pedidos rechazados', 1),
(243, 'gestionar ventas', 1),
(244, 'ver reportes', 1),
(245, 'imprimir reportes de ventas', 0),
(246, 'imprimir reportes de productos', 1),
(247, 'imprimir comandas', 1),
(248, 'asignar roles a usuarios', 0),
(249, 'ver el precio del dólar', 0),
(250, 'ver notificaciones', 0),
(251, 'ver modal de ayuda', 0),
(252, 'ver carrito de compra', 1),
(253, 'ver detalles de promociones', 1),
(254, 'ver bitácora', 0),
(255, 'transformar imagenes', 1),
(256, 'ver dashboard', 0),
(257, 'ver historial de cambio', 0),
(258, 'actualizar cambio de divisas', 0),
(259, 'ver detalles de las ventas', 0),
(260, 'ver ventas despachadas', 0),
(261, 'ver ventas sin cancelar', 0),
(262, 'ver pedidos en espera', 1),
(263, 'ver pedidos rechazados', 1),
(264, 'gestionar ventas', 1),
(265, 'ver reportes', 1),
(266, 'imprimir reportes de ventas', 0),
(267, 'imprimir reportes de productos', 1),
(268, 'imprimir comandas', 1),
(269, 'asignar roles a usuarios', 0),
(270, 'ver el precio del dólar', 0),
(271, 'ver notificaciones', 0),
(272, 'ver modal de ayuda', 0),
(273, 'ver carrito de compra', 1),
(274, 'ver detalles de promociones', 1),
(275, 'ver bitácora', 0),
(276, 'transformar imagenes', 1),
(277, 'ver dashboard', 0),
(278, 'ver historial de cambio', 0),
(279, 'actualizar cambio de divisas', 0),
(280, 'ver detalles de las ventas', 0),
(281, 'ver ventas despachadas', 0),
(282, 'ver ventas sin cancelar', 0),
(283, 'ver pedidos en espera', 1),
(284, 'ver pedidos rechazados', 1),
(285, 'gestionar ventas', 1),
(286, 'ver reportes', 1),
(287, 'imprimir reportes de ventas', 0),
(288, 'imprimir reportes de productos', 1),
(289, 'imprimir comandas', 1),
(290, 'asignar roles a usuarios', 0),
(291, 'ver el precio del dólar', 0),
(292, 'ver notificaciones', 0),
(293, 'ver modal de ayuda', 0),
(294, 'ver carrito de compra', 1),
(295, 'ver detalles de promociones', 1),
(296, 'ver bitácora', 0),
(297, 'transformar imagenes', 1),
(298, 'ver dashboard', 0),
(299, 'ver historial de cambio', 0),
(300, 'actualizar cambio de divisas', 0),
(301, 'ver detalles de las ventas', 0),
(302, 'ver ventas despachadas', 0),
(303, 'ver ventas sin cancelar', 0),
(304, 'ver pedidos en espera', 1),
(305, 'ver pedidos rechazados', 1),
(306, 'gestionar ventas', 1),
(307, 'ver reportes', 1),
(308, 'imprimir reportes de ventas', 0),
(309, 'imprimir reportes de productos', 1),
(310, 'imprimir comandas', 1),
(311, 'asignar roles a usuarios', 0),
(312, 'ver el precio del dólar', 0),
(313, 'ver notificaciones', 0),
(314, 'ver modal de ayuda', 0),
(315, 'ver carrito de compra', 1),
(316, 'ver detalles de promociones', 1),
(317, 'ver bitácora', 0),
(318, 'transformar imagenes', 1),
(319, 'ver dashboard', 0),
(320, 'ver historial de cambio', 0),
(321, 'actualizar cambio de divisas', 0),
(322, 'ver detalles de las ventas', 0),
(323, 'ver ventas despachadas', 0),
(324, 'ver ventas sin cancelar', 0),
(325, 'ver pedidos en espera', 1),
(326, 'ver pedidos rechazados', 1),
(327, 'gestionar ventas', 1),
(328, 'ver reportes', 1),
(329, 'imprimir reportes de ventas', 0),
(330, 'imprimir reportes de productos', 1),
(331, 'imprimir comandas', 1),
(332, 'asignar roles a usuarios', 0),
(333, 'ver el precio del dólar', 0),
(334, 'ver notificaciones', 0),
(335, 'ver modal de ayuda', 0),
(336, 'ver carrito de compra', 1),
(337, 'ver detalles de promociones', 1),
(338, 'ver bitácora', 0),
(339, 'transformar imagenes', 1),
(340, 'ver dashboard', 0),
(341, 'ver historial de cambio', 0),
(342, 'actualizar cambio de divisas', 0),
(343, 'ver detalles de las ventas', 0),
(344, 'ver ventas despachadas', 0),
(345, 'ver ventas sin cancelar', 0),
(346, 'ver pedidos en espera', 1),
(347, 'ver pedidos rechazados', 1),
(348, 'gestionar ventas', 1),
(349, 'ver reportes', 1),
(350, 'imprimir reportes de ventas', 0),
(351, 'imprimir reportes de productos', 1),
(352, 'imprimir comandas', 1),
(353, 'asignar roles a usuarios', 0),
(354, 'ver el precio del dólar', 0),
(355, 'ver notificaciones', 0),
(356, 'ver modal de ayuda', 0),
(357, 'ver carrito de compra', 1),
(358, 'ver detalles de promociones', 1),
(359, 'ver bitácora', 0),
(360, 'transformar imagenes', 1),
(361, 'ver dashboard', 0),
(362, 'ver historial de cambio', 0),
(363, 'actualizar cambio de divisas', 0),
(364, 'ver detalles de las ventas', 0),
(365, 'ver ventas despachadas', 0),
(366, 'ver ventas sin cancelar', 0),
(367, 'ver pedidos en espera', 1),
(368, 'ver pedidos rechazados', 1),
(369, 'gestionar ventas', 1),
(370, 'ver reportes', 1),
(371, 'imprimir reportes de ventas', 0),
(372, 'imprimir reportes de productos', 1),
(373, 'imprimir comandas', 1),
(374, 'asignar roles a usuarios', 0),
(375, 'ver el precio del dólar', 0),
(376, 'ver notificaciones', 0),
(377, 'ver modal de ayuda', 0),
(378, 'ver carrito de compra', 1),
(379, 'ver detalles de promociones', 1),
(380, 'ver bitácora', 0),
(381, 'transformar imagenes', 1),
(382, 'ver dashboard', 0),
(383, 'ver historial de cambio', 0),
(384, 'actualizar cambio de divisas', 0),
(385, 'ver detalles de las ventas', 0),
(386, 'ver ventas despachadas', 0),
(387, 'ver ventas sin cancelar', 0),
(388, 'ver pedidos en espera', 1),
(389, 'ver pedidos rechazados', 1),
(390, 'gestionar ventas', 1),
(391, 'ver reportes', 1),
(392, 'imprimir reportes de ventas', 0),
(393, 'imprimir reportes de productos', 1),
(394, 'imprimir comandas', 1),
(395, 'asignar roles a usuarios', 0),
(396, 'ver el precio del dólar', 0),
(397, 'ver notificaciones', 0),
(398, 'ver modal de ayuda', 0),
(399, 'ver carrito de compra', 1),
(400, 'ver detalles de promociones', 1),
(401, 'ver bitácora', 0),
(402, 'transformar imagenes', 1),
(403, 'ver dashboard', 0),
(404, 'ver historial de cambio', 0),
(405, 'actualizar cambio de divisas', 0),
(406, 'ver detalles de las ventas', 0),
(407, 'ver ventas despachadas', 0),
(408, 'ver ventas sin cancelar', 0),
(409, 'ver pedidos en espera', 1),
(410, 'ver pedidos rechazados', 1),
(411, 'gestionar ventas', 1),
(412, 'ver reportes', 1),
(413, 'imprimir reportes de ventas', 0),
(414, 'imprimir reportes de productos', 1),
(415, 'imprimir comandas', 1),
(416, 'asignar roles a usuarios', 0),
(417, 'ver el precio del dólar', 0),
(418, 'ver notificaciones', 0),
(419, 'ver modal de ayuda', 0),
(420, 'ver carrito de compra', 1),
(421, 'ver detalles de promociones', 1),
(422, 'ver bitácora', 0),
(423, 'transformar imagenes', 1),
(424, 'ver dashboard', 0),
(425, 'ver historial de cambio', 0),
(426, 'actualizar cambio de divisas', 0),
(427, 'ver detalles de las ventas', 0),
(428, 'ver ventas despachadas', 0),
(429, 'ver ventas sin cancelar', 0),
(430, 'ver pedidos en espera', 1),
(431, 'ver pedidos rechazados', 1),
(432, 'gestionar ventas', 1),
(433, 'ver reportes', 1),
(434, 'imprimir reportes de ventas', 0),
(435, 'imprimir reportes de productos', 1),
(436, 'imprimir comandas', 1),
(437, 'asignar roles a usuarios', 0),
(438, 'ver el precio del dólar', 0),
(439, 'ver notificaciones', 0),
(440, 'ver modal de ayuda', 0),
(441, 'ver carrito de compra', 1),
(442, 'ver detalles de promociones', 1),
(443, 'ver bitácora', 0),
(444, 'transformar imagenes', 1),
(445, 'ver dashboard', 0),
(446, 'ver historial de cambio', 0),
(447, 'actualizar cambio de divisas', 0),
(448, 'ver detalles de las ventas', 0),
(449, 'ver ventas despachadas', 0),
(450, 'ver ventas sin cancelar', 0),
(451, 'ver pedidos en espera', 1),
(452, 'ver pedidos rechazados', 1),
(453, 'gestionar ventas', 1),
(454, 'ver reportes', 1),
(455, 'imprimir reportes de ventas', 0),
(456, 'imprimir reportes de productos', 1),
(457, 'imprimir comandas', 1),
(458, 'asignar roles a usuarios', 0),
(459, 'ver el precio del dólar', 0),
(460, 'ver notificaciones', 0),
(461, 'ver modal de ayuda', 0),
(462, 'ver carrito de compra', 1),
(463, 'ver detalles de promociones', 1),
(464, 'ver bitácora', 0),
(465, 'transformar imagenes', 1),
(466, 'ver dashboard', 0),
(467, 'ver historial de cambio', 0),
(468, 'actualizar cambio de divisas', 0),
(469, 'ver detalles de las ventas', 0),
(470, 'ver ventas despachadas', 0),
(471, 'ver ventas sin cancelar', 0),
(472, 'ver pedidos en espera', 1),
(473, 'ver pedidos rechazados', 1),
(474, 'gestionar ventas', 1),
(475, 'ver reportes', 1),
(476, 'imprimir reportes de ventas', 0),
(477, 'imprimir reportes de productos', 1),
(478, 'imprimir comandas', 1),
(479, 'asignar roles a usuarios', 0),
(480, 'ver el precio del dólar', 0),
(481, 'ver notificaciones', 0),
(482, 'ver modal de ayuda', 0),
(483, 'ver carrito de compra', 1),
(484, 'ver detalles de promociones', 1),
(485, 'ver bitácora', 0),
(486, 'transformar imagenes', 1),
(487, 'ver dashboard', 0),
(488, 'ver historial de cambio', 0),
(489, 'actualizar cambio de divisas', 0),
(490, 'ver detalles de las ventas', 0),
(491, 'ver ventas despachadas', 0),
(492, 'ver ventas sin cancelar', 0),
(493, 'ver pedidos en espera', 1),
(494, 'ver pedidos rechazados', 1),
(495, 'gestionar ventas', 1),
(496, 'ver reportes', 1),
(497, 'imprimir reportes de ventas', 0),
(498, 'imprimir reportes de productos', 1),
(499, 'imprimir comandas', 1),
(500, 'asignar roles a usuarios', 0),
(501, 'ver el precio del dólar', 0),
(502, 'ver notificaciones', 0),
(503, 'ver modal de ayuda', 0),
(504, 'ver carrito de compra', 1),
(505, 'ver detalles de promociones', 1),
(506, 'ver bitácora', 0),
(507, 'transformar imagenes', 1),
(508, 'ver dashboard', 0),
(509, 'ver historial de cambio', 0),
(510, 'actualizar cambio de divisas', 0),
(511, 'ver detalles de las ventas', 0),
(512, 'ver ventas despachadas', 0),
(513, 'ver ventas sin cancelar', 0),
(514, 'ver pedidos en espera', 1),
(515, 'ver pedidos rechazados', 1),
(516, 'gestionar ventas', 1),
(517, 'ver reportes', 1),
(518, 'imprimir reportes de ventas', 0),
(519, 'imprimir reportes de productos', 1),
(520, 'imprimir comandas', 1),
(521, 'asignar roles a usuarios', 0),
(522, 'ver el precio del dólar', 0),
(523, 'ver notificaciones', 0),
(524, 'ver modal de ayuda', 0),
(525, 'ver carrito de compra', 1),
(526, 'ver detalles de promociones', 1),
(527, 'ver bitácora', 0),
(528, 'transformar imagenes', 1),
(529, 'ver dashboard', 0),
(530, 'ver historial de cambio', 0),
(531, 'actualizar cambio de divisas', 0),
(532, 'ver detalles de las ventas', 0),
(533, 'ver ventas despachadas', 0),
(534, 'ver ventas sin cancelar', 0),
(535, 'ver pedidos en espera', 1),
(536, 'ver pedidos rechazados', 1),
(537, 'gestionar ventas', 1),
(538, 'ver reportes', 1),
(539, 'imprimir reportes de ventas', 0),
(540, 'imprimir reportes de productos', 1),
(541, 'imprimir comandas', 1),
(542, 'asignar roles a usuarios', 0),
(543, 'ver el precio del dólar', 0),
(544, 'ver notificaciones', 0),
(545, 'ver modal de ayuda', 0),
(546, 'ver carrito de compra', 1),
(547, 'ver detalles de promociones', 1),
(548, 'ver bitácora', 0),
(549, 'transformar imagenes', 1),
(550, 'ver dashboard', 0),
(551, 'ver historial de cambio', 0),
(552, 'actualizar cambio de divisas', 0),
(553, 'ver detalles de las ventas', 0),
(554, 'ver ventas despachadas', 0),
(555, 'ver ventas sin cancelar', 0),
(556, 'ver pedidos en espera', 1),
(557, 'ver pedidos rechazados', 1),
(558, 'gestionar ventas', 1),
(559, 'ver reportes', 1),
(560, 'imprimir reportes de ventas', 0),
(561, 'imprimir reportes de productos', 1),
(562, 'imprimir comandas', 1),
(563, 'asignar roles a usuarios', 0),
(564, 'ver el precio del dólar', 0),
(565, 'ver notificaciones', 0),
(566, 'ver modal de ayuda', 0),
(567, 'ver carrito de compra', 1),
(568, 'ver detalles de promociones', 1),
(569, 'ver bitácora', 0),
(570, 'transformar imagenes', 1),
(571, 'ver dashboard', 0),
(572, 'ver historial de cambio', 0),
(573, 'actualizar cambio de divisas', 0),
(574, 'ver detalles de las ventas', 0),
(575, 'ver ventas despachadas', 0),
(576, 'ver ventas sin cancelar', 0),
(577, 'ver pedidos en espera', 1),
(578, 'ver pedidos rechazados', 1),
(579, 'gestionar ventas', 1),
(580, 'ver reportes', 1),
(581, 'imprimir reportes de ventas', 0),
(582, 'imprimir reportes de productos', 1),
(583, 'imprimir comandas', 1),
(584, 'asignar roles a usuarios', 0),
(585, 'ver el precio del dólar', 0),
(586, 'ver notificaciones', 0),
(587, 'ver modal de ayuda', 0),
(588, 'ver carrito de compra', 1),
(589, 'ver detalles de promociones', 1),
(590, 'ver bitácora', 0),
(591, 'transformar imagenes', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentaciones`
--

CREATE TABLE `presentaciones` (
  `id_presentacion` int(11) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `nombre_presentacion` varchar(50) NOT NULL,
  `cantidad_pmp` int(15) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `presentaciones`
--

INSERT INTO `presentaciones` (`id_presentacion`, `id_unidad_medida`, `nombre_presentacion`, `cantidad_pmp`, `status`) VALUES
(1, 2, 'PIPA', 200, 1),
(2, 2, 'BIDÓN', 20, 1),
(3, 2, 'GRANEL', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producciones`
--

CREATE TABLE `producciones` (
  `id_produccion` int(11) NOT NULL,
  `fecha_produccion` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producciones`
--

INSERT INTO `producciones` (`id_produccion`, `fecha_produccion`, `status`) VALUES
(26, '2026-02-25 18:10:13', 1),
(27, '2026-02-25 18:10:55', 1),
(28, '2026-02-25 18:11:29', 1),
(29, '2026-02-25 18:28:00', 1),
(30, '2026-03-01 08:15:32', 1),
(31, '2026-03-01 08:16:17', 1),
(32, '2026-03-01 08:44:57', 1),
(34, '2026-03-01 09:00:33', 1),
(35, '2026-03-01 09:32:25', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `nombre_producto` varchar(100) NOT NULL,
  `precio_producto_detal` float NOT NULL,
  `precio_producto_mayor` float NOT NULL,
  `stock_producto` int(15) NOT NULL,
  `producto_es_fabricado` tinyint(1) NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `id_unidad_medida`, `nombre_producto`, `precio_producto_detal`, `precio_producto_mayor`, `stock_producto`, `producto_es_fabricado`, `status`) VALUES
(1, 2, 'JABÓN', 100, 90, 192, 1, 1),
(2, 2, 'CLORO', 10, 5, 20, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_compras`
--

CREATE TABLE `productos_compras` (
  `id_producto_compra` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `cantidad_producto` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos_compras`
--

INSERT INTO `productos_compras` (`id_producto_compra`, `id_producto`, `id_compra`, `cantidad_producto`, `status`) VALUES
(1, 1, 4, 1, 1),
(2, 1, 5, 1, 1),
(3, 1, 7, 22, 1),
(4, 2, 8, 1, 1),
(5, 2, 8, 1, 1),
(9, 1, 9, 15, 1),
(10, 1, 10, 12, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_presentaciones`
--

CREATE TABLE `productos_presentaciones` (
  `id_producto_presentacion` int(11) NOT NULL,
  `id_presentacion` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos_presentaciones`
--

INSERT INTO `productos_presentaciones` (`id_producto_presentacion`, `id_presentacion`, `id_producto`, `status`) VALUES
(1, 1, 1, 0),
(2, 2, 1, 0),
(3, 3, 1, 0),
(4, 1, 2, 1),
(5, 2, 2, 1),
(6, 3, 2, 1),
(7, 3, 1, 0),
(8, 3, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_producciones`
--

CREATE TABLE `productos_producciones` (
  `id_producto_produccion` int(11) NOT NULL,
  `id_produccion` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad_producida` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos_producciones`
--

INSERT INTO `productos_producciones` (`id_producto_produccion`, `id_produccion`, `id_producto`, `cantidad_producida`, `status`) VALUES
(25, 26, 1, 1, 1),
(26, 27, 1, 1, 1),
(27, 28, 1, 100, 1),
(28, 29, 1, 1, 0),
(29, 29, 1, 6, 0),
(31, 29, 1, 12, 1),
(32, 29, 2, 12, 1),
(33, 30, 2, 1, 1),
(34, 30, 1, 1, 1),
(35, 30, 1, 1, 1),
(36, 31, 2, 1, 1),
(37, 31, 1, 1, 1),
(38, 32, 1, 1, 1),
(41, 34, 1, 10, 1),
(42, 35, 1, 3, 1),
(43, 35, 2, 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_servicios_ventas`
--

CREATE TABLE `productos_servicios_ventas` (
  `id_producto_servicio_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_servicio_venta` int(11) NOT NULL,
  `cantidad_producto` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_ventas`
--

CREATE TABLE `productos_ventas` (
  `id_producto_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `cantidad_producto` int(10) NOT NULL,
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
('30485684', 'ANDERSON', '04169484649', 'ANDERSONFREITEZ@GMAIL.COM', 'sxaxasas', 1);

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
(2, 'OFICINISTA', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id_servicio` int(11) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `nombre_servicio` varchar(100) NOT NULL,
  `costo_servicio` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id_servicio`, `id_unidad_medida`, `nombre_servicio`, `costo_servicio`, `status`) VALUES
(1, 2, 'FUMIGACION', 100, 1),
(2, 1, 'FUMIGACIONT', 122, 1),
(3, 2, 'FUMIGACION 2', 12, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios_ventas`
--

CREATE TABLE `servicios_ventas` (
  `id_servicios_ventas` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL,
  `cantidad_servicio` int(10) NOT NULL,
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
(2, 'LITRO(S)', 'L', 1000, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `cedula_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `apellido_usuario` varchar(50) NOT NULL,
  `usuario_usuario` varchar(50) NOT NULL,
  `contrasena_usuario` varchar(255) NOT NULL,
  `telefono_usuario` varchar(11) NOT NULL,
  `correo_usuario` varchar(150) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`cedula_usuario`, `id_rol`, `nombre_usuario`, `apellido_usuario`, `usuario_usuario`, `contrasena_usuario`, `telefono_usuario`, `correo_usuario`, `status`) VALUES
(1234567, 1, 'ANDERS', 'DAVID', 'ander1234', '$2y$10$tnkCgC.SA4fIWZ1jyJ1Nc.at2Ke5PF0PpZbGwmnBxjFZAeAltJjMW', '12345678901', 'ander2@gmail.com', 1),
(30485684, 1, 'ANDERSON', 'FREITEZ', 'Ander123', '$2y$10$3xuW0Z34n9oScdoEDKoo1.OWgDGnch8iJQn2zbeI/Ci0PRP9qCQke', '04169484649', 'andersonfreitez6@gmail.com', 1),
(30485685, 1, 'ANDERSON', 'APELLIDO', 'Ander4321', '$2y$10$dZ.w3dX5fWLZgwRx1KOaz.kop/X/Qjb.oiHikcChNvFVbGypxkMT6', '04169484649', 'andersonfreitez16@gmail.com', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL,
  `rif_cedula_cliente` varchar(20) NOT NULL,
  `id_cambio_iva` int(11) NOT NULL,
  `fecha_venta` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accesos`
--
ALTER TABLE `accesos`
  ADD PRIMARY KEY (`id_acceso`),
  ADD KEY `id_rol_acceso_fk` (`id_rol`),
  ADD KEY `id_permiso_acceso_fk` (`id_permiso`),
  ADD KEY `id_modulo_acceso_fk` (`id_modulo`);

--
-- Indices de la tabla `acciones`
--
ALTER TABLE `acciones`
  ADD PRIMARY KEY (`id_accion`);

--
-- Indices de la tabla `acciones_resagadas_usuarios`
--
ALTER TABLE `acciones_resagadas_usuarios`
  ADD PRIMARY KEY (`id_accion_resagada`),
  ADD KEY `id_accion_acciones_resagadas_fk` (`id_accion`),
  ADD KEY `id_modulo_acciones_resagadas_fk` (`id_modulo`),
  ADD KEY `cedula_usuario_acciones_resagadas_fk` (`cedula_usuario`);

--
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`id_bitacora`),
  ADD KEY `cedula_usuario_bitacora_fk` (`cedula_usuario`),
  ADD KEY `id_modulo_bitacora_fk` (`id_modulo`),
  ADD KEY `id_accion_bitacora_fk` (`id_accion`);

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
  ADD KEY `id_moneda_cambio_fk` (`id_moneda`);

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
-- Indices de la tabla `detalles_pagos`
--
ALTER TABLE `detalles_pagos`
  ADD PRIMARY KEY (`id_detalle_pago`),
  ADD KEY `id_pago_detalle_pago_fk` (`id_pago`),
  ADD KEY `id_moneda_detalle_pago_fk` (`id_moneda`),
  ADD KEY `id_metodo_pago_detalle_pago_fk` (`id_metodo_pago`);

--
-- Indices de la tabla `insumos`
--
ALTER TABLE `insumos`
  ADD PRIMARY KEY (`id_insumo`);

--
-- Indices de la tabla `insumos_compras`
--
ALTER TABLE `insumos_compras`
  ADD PRIMARY KEY (`id_insumo_compra`),
  ADD KEY `id_compra_insumos_compras_fk` (`id_compra`),
  ADD KEY `id_insumo_insumos_compras_fk` (`id_insumo`);

--
-- Indices de la tabla `insumos_ventas`
--
ALTER TABLE `insumos_ventas`
  ADD PRIMARY KEY (`id_insumo_venta`),
  ADD KEY `id_venta_insumos_ventas_fk` (`id_venta`),
  ADD KEY `id_insumo_insumos_ventas_fk` (`id_insumo`);

--
-- Indices de la tabla `materias_primas`
--
ALTER TABLE `materias_primas`
  ADD PRIMARY KEY (`id_materia_prima`),
  ADD KEY `id_unidad_medida_materia_prima_fk` (`id_unidad_medida`);

--
-- Indices de la tabla `materias_primas_compras`
--
ALTER TABLE `materias_primas_compras`
  ADD PRIMARY KEY (`id_materia_prima_compra`),
  ADD KEY `id_compra_materias_primas_compras_fk` (`id_compra`),
  ADD KEY `id_materia_prima_materias_primas_compras_fk` (`id_materia_prima`);

--
-- Indices de la tabla `materias_primas_presentaciones`
--
ALTER TABLE `materias_primas_presentaciones`
  ADD PRIMARY KEY (`id_materia_prima_presentacion`),
  ADD KEY `id_materia_prima_materias_primas_presentaciones_fk` (`id_materia_prima`),
  ADD KEY `id_presentacion_materias_primas_presentaciones_fk` (`id_presentacion`);

--
-- Indices de la tabla `materias_primas_productos`
--
ALTER TABLE `materias_primas_productos`
  ADD PRIMARY KEY (`id_materia_prima_producto`),
  ADD KEY `id_materia_primas_materias_primas_productos_fk` (`id_materia_prima`),
  ADD KEY `id_producto_materias_primas_productos_fk` (`id_producto`);

--
-- Indices de la tabla `metodos_pagos`
--
ALTER TABLE `metodos_pagos`
  ADD PRIMARY KEY (`id_metodo_pago`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id_modulo`);

--
-- Indices de la tabla `monedas`
--
ALTER TABLE `monedas`
  ADD PRIMARY KEY (`id_moneda`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id_notificacion`);

--
-- Indices de la tabla `notificaciones_usuarios`
--
ALTER TABLE `notificaciones_usuarios`
  ADD PRIMARY KEY (`id_notificacion_usuario`),
  ADD KEY `cedula_usuario_notificacion_fk` (`cedula_usuario`),
  ADD KEY `id_notificacion_notificacion_fk` (`id_notificacion`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_venta_pago_fk` (`id_venta`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id_permiso`);

--
-- Indices de la tabla `presentaciones`
--
ALTER TABLE `presentaciones`
  ADD PRIMARY KEY (`id_presentacion`),
  ADD KEY `id_unidad_medida_presentaciones_fk` (`id_unidad_medida`);

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
  ADD KEY `id_unidad_medida_producto_fk` (`id_unidad_medida`);

--
-- Indices de la tabla `productos_compras`
--
ALTER TABLE `productos_compras`
  ADD PRIMARY KEY (`id_producto_compra`),
  ADD KEY `id_compra_productos_compras_fk` (`id_compra`),
  ADD KEY `id_producto_productos_compras_fk` (`id_producto`);

--
-- Indices de la tabla `productos_presentaciones`
--
ALTER TABLE `productos_presentaciones`
  ADD PRIMARY KEY (`id_producto_presentacion`),
  ADD KEY `id_producto_presentacion_fk` (`id_producto`),
  ADD KEY `id_presentacion_productos_presentacion_fk` (`id_presentacion`);

--
-- Indices de la tabla `productos_producciones`
--
ALTER TABLE `productos_producciones`
  ADD PRIMARY KEY (`id_producto_produccion`),
  ADD KEY `id_produccion_productos_produccion_fk` (`id_produccion`),
  ADD KEY `id_producto_productos_produccion_fk` (`id_producto`);

--
-- Indices de la tabla `productos_servicios_ventas`
--
ALTER TABLE `productos_servicios_ventas`
  ADD PRIMARY KEY (`id_producto_servicio_venta`),
  ADD KEY `id_producto_productos_servicios_ventas_fk` (`id_producto`),
  ADD KEY `id_servicio_venta_productos_servicios_ventas_fk` (`id_servicio_venta`);

--
-- Indices de la tabla `productos_ventas`
--
ALTER TABLE `productos_ventas`
  ADD PRIMARY KEY (`id_producto_venta`),
  ADD KEY `id_venta_productos_ventas_fk` (`id_venta`),
  ADD KEY `id_producto_productos_ventas_fk` (`id_producto`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`rif_proveedor`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id_servicio`),
  ADD KEY `id_unidad_medida_servicios_fk` (`id_unidad_medida`);

--
-- Indices de la tabla `servicios_ventas`
--
ALTER TABLE `servicios_ventas`
  ADD PRIMARY KEY (`id_servicios_ventas`),
  ADD KEY `id_venta_servicios_ventas_fk` (`id_venta`),
  ADD KEY `id_servicio_servicios_ventas_fk` (`id_servicio`);

--
-- Indices de la tabla `unidades_medidas`
--
ALTER TABLE `unidades_medidas`
  ADD PRIMARY KEY (`id_unidad_medida`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`cedula_usuario`),
  ADD KEY `id_rol_usuario_fk` (`id_rol`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `rif_cedula_cliente_venta_fk` (`rif_cedula_cliente`),
  ADD KEY `id_cambio_iva_venta` (`id_cambio_iva`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accesos`
--
ALTER TABLE `accesos`
  MODIFY `id_acceso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=519;

--
-- AUTO_INCREMENT de la tabla `acciones`
--
ALTER TABLE `acciones`
  MODIFY `id_accion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT de la tabla `acciones_resagadas_usuarios`
--
ALTER TABLE `acciones_resagadas_usuarios`
  MODIFY `id_accion_resagada` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=254;

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id_bitacora` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT de la tabla `cambios_iva`
--
ALTER TABLE `cambios_iva`
  MODIFY `id_cambio_iva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT de la tabla `cambios_monedas`
--
ALTER TABLE `cambios_monedas`
  MODIFY `id_cambio_moneda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id_compra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `detalles_pagos`
--
ALTER TABLE `detalles_pagos`
  MODIFY `id_detalle_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `insumos`
--
ALTER TABLE `insumos`
  MODIFY `id_insumo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `insumos_compras`
--
ALTER TABLE `insumos_compras`
  MODIFY `id_insumo_compra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `insumos_ventas`
--
ALTER TABLE `insumos_ventas`
  MODIFY `id_insumo_venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materias_primas`
--
ALTER TABLE `materias_primas`
  MODIFY `id_materia_prima` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `materias_primas_compras`
--
ALTER TABLE `materias_primas_compras`
  MODIFY `id_materia_prima_compra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `materias_primas_presentaciones`
--
ALTER TABLE `materias_primas_presentaciones`
  MODIFY `id_materia_prima_presentacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `materias_primas_productos`
--
ALTER TABLE `materias_primas_productos`
  MODIFY `id_materia_prima_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `metodos_pagos`
--
ALTER TABLE `metodos_pagos`
  MODIFY `id_metodo_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;

--
-- AUTO_INCREMENT de la tabla `monedas`
--
ALTER TABLE `monedas`
  MODIFY `id_moneda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `notificaciones_usuarios`
--
ALTER TABLE `notificaciones_usuarios`
  MODIFY `id_notificacion_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=177;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=592;

--
-- AUTO_INCREMENT de la tabla `presentaciones`
--
ALTER TABLE `presentaciones`
  MODIFY `id_presentacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `producciones`
--
ALTER TABLE `producciones`
  MODIFY `id_produccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `productos_compras`
--
ALTER TABLE `productos_compras`
  MODIFY `id_producto_compra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `productos_presentaciones`
--
ALTER TABLE `productos_presentaciones`
  MODIFY `id_producto_presentacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `productos_producciones`
--
ALTER TABLE `productos_producciones`
  MODIFY `id_producto_produccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de la tabla `productos_servicios_ventas`
--
ALTER TABLE `productos_servicios_ventas`
  MODIFY `id_producto_servicio_venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos_ventas`
--
ALTER TABLE `productos_ventas`
  MODIFY `id_producto_venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `servicios_ventas`
--
ALTER TABLE `servicios_ventas`
  MODIFY `id_servicios_ventas` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `unidades_medidas`
--
ALTER TABLE `unidades_medidas`
  MODIFY `id_unidad_medida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `accesos`
--
ALTER TABLE `accesos`
  ADD CONSTRAINT `id_modulo_acceso_fk` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_permiso_acceso_fk` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id_permiso`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_rol_acceso_fk` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `acciones_resagadas_usuarios`
--
ALTER TABLE `acciones_resagadas_usuarios`
  ADD CONSTRAINT `cedula_usuario_acciones_resagadas_fk` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_accion_acciones_resagadas_fk` FOREIGN KEY (`id_accion`) REFERENCES `acciones` (`id_accion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_modulo_acciones_resagadas_fk` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD CONSTRAINT `cedula_usuario_bitacora_fk` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_accion_bitacora_fk` FOREIGN KEY (`id_accion`) REFERENCES `acciones` (`id_accion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_modulo_bitacora_fk` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cambios_monedas`
--
ALTER TABLE `cambios_monedas`
  ADD CONSTRAINT `id_moneda_cambio_fk` FOREIGN KEY (`id_moneda`) REFERENCES `monedas` (`id_moneda`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `rif_proveedor_compras_fk` FOREIGN KEY (`rif_proveedor`) REFERENCES `proveedores` (`rif_proveedor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalles_pagos`
--
ALTER TABLE `detalles_pagos`
  ADD CONSTRAINT `id_metodo_pago_detalle_pago_fk` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodos_pagos` (`id_metodo_pago`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_moneda_detalle_pago_fk` FOREIGN KEY (`id_moneda`) REFERENCES `monedas` (`id_moneda`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_pago_detalle_pago_fk` FOREIGN KEY (`id_pago`) REFERENCES `pagos` (`id_pago`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `insumos_compras`
--
ALTER TABLE `insumos_compras`
  ADD CONSTRAINT `id_compra_insumos_compras_fk` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_insumo_insumos_compras_fk` FOREIGN KEY (`id_insumo`) REFERENCES `insumos` (`id_insumo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `insumos_ventas`
--
ALTER TABLE `insumos_ventas`
  ADD CONSTRAINT `id_insumo_insumos_ventas_fk` FOREIGN KEY (`id_insumo`) REFERENCES `insumos` (`id_insumo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_venta_insumos_ventas_fk` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `materias_primas`
--
ALTER TABLE `materias_primas`
  ADD CONSTRAINT `id_unidad_medida_materia_prima_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medidas` (`id_unidad_medida`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Filtros para la tabla `materias_primas_productos`
--
ALTER TABLE `materias_primas_productos`
  ADD CONSTRAINT `id_materia_primas_materias_primas_productos_fk` FOREIGN KEY (`id_materia_prima`) REFERENCES `materias_primas` (`id_materia_prima`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_producto_materias_primas_productos_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `notificaciones_usuarios`
--
ALTER TABLE `notificaciones_usuarios`
  ADD CONSTRAINT `cedula_usuario_notificacion_fk` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_notificacion_notificacion_fk` FOREIGN KEY (`id_notificacion`) REFERENCES `notificaciones` (`id_notificacion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `id_venta_pago_fk` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `presentaciones`
--
ALTER TABLE `presentaciones`
  ADD CONSTRAINT `id_unidad_medida_presentaciones_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medidas` (`id_unidad_medida`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `id_unidad_medida_producto_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medidas` (`id_unidad_medida`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_compras`
--
ALTER TABLE `productos_compras`
  ADD CONSTRAINT `id_compra_productos_compras_fk` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_producto_productos_compras_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_presentaciones`
--
ALTER TABLE `productos_presentaciones`
  ADD CONSTRAINT `id_presentacion_productos_presentacion_fk` FOREIGN KEY (`id_presentacion`) REFERENCES `presentaciones` (`id_presentacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_producto_presentacion_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_producciones`
--
ALTER TABLE `productos_producciones`
  ADD CONSTRAINT `id_produccion_productos_produccion_fk` FOREIGN KEY (`id_produccion`) REFERENCES `producciones` (`id_produccion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_producto_productos_produccion_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_servicios_ventas`
--
ALTER TABLE `productos_servicios_ventas`
  ADD CONSTRAINT `id_producto_productos_servicios_ventas_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_servicio_venta_productos_servicios_ventas_fk` FOREIGN KEY (`id_servicio_venta`) REFERENCES `servicios_ventas` (`id_servicios_ventas`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_ventas`
--
ALTER TABLE `productos_ventas`
  ADD CONSTRAINT `id_producto_productos_ventas_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_venta_productos_ventas_fk` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD CONSTRAINT `id_unidad_medida_servicios_fk` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medidas` (`id_unidad_medida`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `servicios_ventas`
--
ALTER TABLE `servicios_ventas`
  ADD CONSTRAINT `id_servicio_servicios_ventas_fk` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_venta_servicios_ventas_fk` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `id_rol_usuario_fk` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `id_cambio_iva_venta` FOREIGN KEY (`id_cambio_iva`) REFERENCES `cambios_iva` (`id_cambio_iva`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rif_cedula_cliente_venta_fk` FOREIGN KEY (`rif_cedula_cliente`) REFERENCES `clientes` (`rif_cedula_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
