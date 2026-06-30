-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-07-2026 a las 01:04:19
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

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cambiar_estado_pedido` (IN `sp_id_pedido` VARCHAR(255), IN `sp_estado` INT)   BEGIN     
    START TRANSACTION;
    
    -- Devolver el stock de los productos del pedido
    IF(sp_estado=6) THEN
       
        BEGIN
        
        DECLARE v_cantidad_bruta decimal(10,2);
		DECLARE v_stock_actual_producto decimal(10,2);
		DECLARE v_id_producto VARCHAR(30);
		DECLARE fin_consulta INT default 0;
		DECLARE productosPedido CURSOR FOR 
    	SELECT  (pres.cantidad_pmp * posp.cantidad_producto) as cantidad_bruta, prod.stock_producto,prod.id_producto
        FROM productos_ordenes_entregas_presupuestos as posp
        INNER JOIN presentaciones_productos as pp ON posp.id_presentacion_producto = pp.id_presentacion_producto
        INNER JOIN presentaciones as pres ON pp.id_presentacion = pres.id_presentacion
        INNER JOIN productos as prod ON pp.id_producto = prod.id_producto
        WHERE posp.id_orden_entrega_presupuesto = sp_id_pedido;
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET fin_consulta = 1;
		
        OPEN productosPedido;
        mi_ciclo: LOOP
        	FETCH productosPedido INTO v_cantidad_bruta, v_stock_actual_producto, v_id_producto;
        	IF fin_consulta = 1 THEN 
            	LEAVE mi_ciclo;
        	END IF;
        		-- Actualizamos cada stock
            	UPDATE productos SET stock_producto = v_stock_actual_producto+v_cantidad_bruta WHERE id_producto=v_id_producto;
                IF(ROW_COUNT()<=0) THEN
                	ROLLBACK;
                	SIGNAL SQLSTATE '45000'
					SET MESSAGE_TEXT = 'No se logró actualizar el stock de uno de los productos';
                END IF;
		END LOOP mi_ciclo;
        CLOSE productosPedido; 
        END;
        
        -- Borrar los detalles del pago
        UPDATE pagos SET status = 0 WHERE id_orden_entrega_presupuesto = sp_id_pedido;
        IF(ROW_COUNT()<=0) THEN
          	ROLLBACK;
            SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'No se borraron los detalles del pago';
       	END IF;
 	END IF;
    
    -- Actualizamos en status del pedido
    UPDATE pedidos SET status = sp_estado WHERE id_orden_entrega_presupuesto = sp_id_pedido;
    IF(ROW_COUNT()<=0) THEN
    	ROLLBACK;
        SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'No se pudo actualizar el estado del pedido';
    END IF;
    
    COMMIT;
    END$$

DELIMITER ;

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
(932, 1, 3, 622, 1),
(933, 1, 257, 618, 1),
(934, 1, 257, 619, 1),
(935, 1, 257, 620, 1),
(936, 1, 257, 621, 1),
(937, 1, 257, 622, 1),
(938, 1, 258, 618, 1),
(939, 1, 258, 619, 1),
(940, 1, 258, 620, 1),
(941, 1, 258, 621, 1),
(942, 1, 258, 622, 1),
(943, 1, 29, 623, 1),
(944, 1, 10, 625, 1),
(945, 1, 18, 627, 1),
(946, 1, 3, 629, 1),
(947, 1, 3, 631, 1),
(948, 1, 3, 633, 1),
(949, 1, 11, 635, 1),
(950, 1, 11, 637, 1),
(951, 1, 9, 639, 1),
(952, 1, 9, 641, 1),
(953, 1, 253, 643, 1),
(954, 1, 13, 645, 1),
(955, 1, 15, 644, 1),
(956, 1, 9, 642, 1),
(957, 1, 9, 640, 1),
(958, 1, 9, 638, 1),
(959, 1, 11, 636, 1),
(960, 1, 11, 634, 1),
(961, 1, 3, 632, 1),
(962, 1, 3, 630, 1),
(963, 1, 3, 628, 1),
(964, 1, 18, 626, 1),
(965, 1, 10, 624, 1),
(966, 1, 259, 618, 1),
(967, 1, 260, 618, 1),
(968, 1, 260, 619, 1),
(969, 1, 259, 619, 1),
(970, 1, 259, 620, 1),
(971, 1, 260, 620, 1),
(972, 1, 260, 621, 1),
(973, 1, 259, 621, 1),
(974, 1, 259, 622, 1),
(975, 1, 260, 622, 1),
(976, 1, 261, 618, 1),
(977, 1, 261, 619, 1),
(978, 1, 261, 620, 1),
(979, 1, 261, 621, 1),
(980, 1, 261, 622, 1),
(981, 1, 262, 618, 1),
(982, 1, 262, 619, 1),
(983, 1, 262, 620, 1),
(984, 1, 262, 621, 1),
(985, 1, 262, 622, 1),
(986, 1, 29, 618, 1),
(987, 1, 29, 619, 1),
(988, 1, 29, 621, 1),
(989, 1, 29, 620, 1),
(990, 1, 29, 622, 1),
(991, 1, 248, 647, 1),
(992, 1, 248, 648, 1),
(993, 1, 248, 649, 1),
(994, 1, 263, 618, 1),
(995, 1, 263, 619, 1),
(996, 1, 263, 620, 1),
(997, 1, 263, 621, 1),
(998, 1, 263, 622, 1),
(999, 1, 248, 650, 1),
(1000, 1, 264, 618, 1),
(1001, 1, 264, 619, 1),
(1002, 1, 264, 620, 1),
(1003, 1, 264, 621, 1),
(1004, 1, 264, 622, 1),
(1005, 1, 248, 651, 1),
(1006, 1, 248, 652, 1),
(1007, 2, 18, 618, 1),
(1008, 3, 18, 618, 0),
(1009, 3, 9, 618, 0),
(1010, 3, 8, 618, 0),
(1011, 3, 1, 618, 0),
(1012, 3, 249, 618, 0),
(1013, 3, 264, 618, 0),
(1014, 3, 251, 618, 0),
(1015, 3, 261, 618, 0),
(1016, 3, 261, 619, 1),
(1017, 3, 251, 619, 1),
(1018, 3, 18, 619, 1),
(1019, 3, 264, 619, 1),
(1020, 3, 249, 619, 1),
(1021, 3, 1, 619, 1),
(1022, 3, 8, 619, 1),
(1023, 3, 29, 618, 0),
(1024, 3, 250, 618, 0),
(1025, 3, 250, 619, 1),
(1026, 1, 248, 653, 1),
(1027, 3, 9, 622, 0);

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
(501, 18, 'V30485684', 'actDT', 1),
(658, 248, 'V30485680', 'borrarDataModuloSS', 1),
(660, 248, 'V30485680', 'actDT', 1),
(693, 248, 'V30485694', 'borrarDataModuloSS', 1),
(695, 248, 'V30485694', 'actDT', 1),
(708, 18, 'V12345666', 'borrarDataModuloSS', 1),
(709, 18, 'V30485680', 'borrarDataModuloSS', 1),
(710, 18, 'V30485683', 'borrarDataModuloSS', 1),
(712, 18, 'V30485686', 'borrarDataModuloSS', 1),
(713, 18, 'V30485694', 'borrarDataModuloSS', 1),
(714, 18, 'V12345666', 'actDT', 1),
(715, 18, 'V30485680', 'actDT', 1),
(716, 18, 'V30485683', 'actDT', 1),
(717, 18, 'V30485686', 'actDT', 1),
(718, 18, 'V30485694', 'actDT', 1);

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
  `ip_dispositivo` varchar(25) NOT NULL,
  `cambios_efectuados` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `fecha_bitacora` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bitacora`
--

INSERT INTO `bitacora` (`id_bitacora`, `cedula_usuario`, `id_modulo`, `resultado_bitacora`, `accion`, `ip_dispositivo`, `cambios_efectuados`, `fecha_bitacora`, `status`) VALUES
(1645, 'V30485684', 18, 'éxito', 'registrarIva', '', NULL, '2026-06-13 16:47:54', 1),
(1646, 'V30485684', 18, 'éxito', 'registrarIva', '', NULL, '2026-06-13 16:48:00', 1),
(1647, 'V30485684', 18, 'éxito', 'registrarIva', '', NULL, '2026-06-13 16:48:18', 1),
(1648, 'V30485684', 248, 'Fallido', 'registrar', '', NULL, '2026-06-13 18:40:36', 1),
(1651, 'V30485684', 248, 'Éxito', 'registrar', '', NULL, '2026-06-13 19:14:15', 1),
(1652, 'V30485684', 248, 'Éxito', 'Asignar Repartidor', '', NULL, '2026-06-13 19:26:40', 1),
(1653, 'V30485684', 18, 'éxito', 'registrarIva', '', NULL, '2026-06-13 19:30:38', 1),
(1654, 'V30485684', 248, 'Éxito', 'registrar', '', NULL, '2026-06-13 19:31:54', 1),
(1655, 'V30485684', 248, 'Éxito', 'actualizar', '', NULL, '2026-06-14 14:02:03', 1),
(1656, 'V30485684', 248, 'Fallido', 'registrar', '', NULL, '2026-06-14 14:05:47', 1),
(1657, 'V30485684', 248, 'Fallido', 'registrar', '', NULL, '2026-06-14 14:06:59', 1),
(1658, 'V30485684', 248, 'Éxito', 'registrar', '', NULL, '2026-06-14 14:07:16', 1),
(1659, 'V30485684', 248, 'Éxito', 'Asignar Repartidor', '', NULL, '2026-06-14 14:35:40', 1),
(1660, 'V30485684', 248, 'Éxito', 'actualizar', '', NULL, '2026-06-14 14:36:19', 1),
(1661, 'V30485684', 247, 'éxito', 'registrar', '', NULL, '2026-06-14 14:38:18', 1),
(1662, 'V30485684', 247, 'éxito', 'actualizar', '', NULL, '2026-06-14 14:38:26', 1),
(1663, 'V30485684', 247, 'éxito', 'actualizar', '', NULL, '2026-06-14 14:38:33', 1),
(1664, 'V30485684', 247, 'éxito', 'actualizar', '', NULL, '2026-06-14 14:39:44', 1),
(1665, 'V30485684', 247, 'éxito', 'actualizar', '', NULL, '2026-06-14 14:39:59', 1),
(1666, 'V30485684', 247, 'éxito', 'actualizar', '', NULL, '2026-06-14 14:41:56', 1),
(1667, 'V30485684', 247, 'éxito', 'actualizar', '', NULL, '2026-06-14 14:47:51', 1),
(1668, 'V30485684', 247, 'fallido', 'actualizar', '', NULL, '2026-06-14 14:50:38', 1),
(1669, 'V30485684', 247, 'fallido', 'actualizar', '', NULL, '2026-06-14 14:50:53', 1),
(1670, 'V30485684', 247, 'éxito', 'actualizar', '', NULL, '2026-06-14 14:51:00', 1),
(1671, 'V30485684', 247, 'éxito', 'actualizar', '', NULL, '2026-06-14 14:52:57', 1),
(1672, 'V30485684', 247, 'éxito', 'registrar', '', NULL, '2026-06-14 14:54:22', 1),
(1673, 'V30485684', 247, 'éxito', 'eliminar', '', NULL, '2026-06-14 14:54:26', 1),
(1674, 'V30485684', 247, 'éxito', 'eliminar', '', NULL, '2026-06-14 14:54:29', 1),
(1675, 'V30485684', 17, 'Error', 'actualizar Empresa de envíos con id: 6', '', NULL, '2026-06-14 14:55:53', 1),
(1676, 'V30485684', 17, 'Éxito', 'actualizar Empresa de envíos con id: 6', '', NULL, '2026-06-14 14:55:57', 1),
(1677, 'V30485684', 250, 'Éxito', 'eliminar Empresa de envíos con id: 7', '', NULL, '2026-06-14 14:56:02', 1),
(1678, 'V30485684', 17, 'Éxito', 'actualizar Empresa de envíos con id: 6', '', NULL, '2026-06-14 14:56:07', 1),
(1679, 'V30485684', 248, 'Éxito', 'Asignar Repartidor', '', NULL, '2026-06-14 17:39:25', 1),
(1680, 'V30485684', 248, 'Éxito', 'Asignar Repartidor', '', NULL, '2026-06-14 17:39:36', 1),
(1681, 'V30485684', 248, 'Éxito', 'actualizar', '', NULL, '2026-06-14 17:39:40', 1),
(1682, 'V30485684', 248, 'Éxito', 'registrar', '', NULL, '2026-06-14 17:47:00', 1),
(1683, 'V30485684', 248, 'Éxito', 'Asignar Repartidor', '', NULL, '2026-06-14 17:57:14', 1),
(1684, 'V30485684', 248, 'Fallido', 'registrar', '', NULL, '2026-06-14 17:57:24', 1),
(1685, 'V30485684', 248, 'Fallido', 'registrar', '', NULL, '2026-06-14 17:57:33', 1),
(1686, 'V30485684', 248, 'Fallido', 'registrar', '', NULL, '2026-06-14 17:57:47', 1),
(1687, 'V30485684', 248, 'Fallido', 'registrar', '', NULL, '2026-06-14 18:00:36', 1),
(1688, 'V30485684', 248, 'Fallido', 'registrar', '', NULL, '2026-06-14 18:01:00', 1),
(1689, 'V30485684', 248, 'Fallido', 'registrar', '', NULL, '2026-06-14 18:01:24', 1),
(1690, 'V30485684', 248, 'Éxito', 'registrar', '', NULL, '2026-06-14 18:04:01', 1),
(1691, 'V30485684', 248, 'Éxito', 'registrar', '', NULL, '2026-06-14 18:04:40', 1),
(1692, 'V30485684', 248, 'Éxito', 'registrar', '', NULL, '2026-06-14 18:06:39', 1),
(1693, 'V30485684', 248, 'Éxito', 'registrar', '', NULL, '2026-06-14 19:10:02', 1),
(1694, 'V30485684', 248, 'Éxito', 'registrar', '', NULL, '2026-06-19 21:13:35', 1),
(1695, 'V30485684', 248, 'Éxito', 'registrar', '', NULL, '2026-06-19 21:16:54', 1),
(1696, 'V30485684', 248, 'Fallido', 'registrar', '', NULL, '2026-06-19 21:17:26', 1),
(1697, 'V30485684', 9, 'éxito', 'registrar', '190.97.229.53', '{\"cedula_usuario\":{\"registrado\":\"V1234567\"},\"nombre_usuario\":{\"registrado\":\"Anderson\"},\"apellido_usuario\":{\"registrado\":\"Freitez\"},\"correo_usuario\":{\"registrado\":\"andersonfreitez96@gmail.com\"},\"telefono_usuario\":{\"registrado\":\"04161234567\"},\"id_rol\":{\"registrado\":2},\"usuario_usuario\":{\"registrado\":\"Ander1239\"},\"contrasena_usuario\":{\"registrado\":\"$2y$10$o4NZdHyA8DC48693PT\\/xcuiYLZEp944yhZUenpn2nFvU2DI4UZ.o.\"},\"foto_usuario\":{\"registrado\":\"usuarios_2026_06_21_13_32_34_11.png\"},\"direccion_usuario\":{\"registrado\":\"SANARE\"}}', '2026-06-21 13:32:34', 1),
(1698, 'V30485684', 9, 'éxito', 'iniciar sesión', '190.97.229.53', NULL, '2026-06-21 17:17:54', 1),
(1699, 'V30485684', 9, 'éxito', 'iniciar sesión', '181.208.252.213', NULL, '2026-06-21 17:18:20', 1),
(1700, 'V30485684', 9, 'éxito', 'actualizar', '190.97.229.53', '{\"telefono_usuario\":{\"modificado\":\"04169484648\"}}', '2026-06-21 18:12:36', 1),
(1701, 'V30485684', 248, 'Fallido', 'registrar', '190.97.229.53', NULL, '2026-06-21 18:24:11', 1),
(1702, 'V30485684', 4, 'éxito', 'actualizar', '190.97.229.53', NULL, '2026-06-21 18:26:52', 1),
(1703, 'V30485684', 4, 'éxito', 'actualizar', '190.97.229.53', NULL, '2026-06-21 18:27:09', 1),
(1704, 'V30485684', 248, 'Fallido', 'registrar', '190.97.229.53', NULL, '2026-06-22 11:05:02', 1),
(1705, 'V30485684', 248, 'Éxito', 'Asignar Repartidor', '190.97.229.53', NULL, '2026-06-22 11:35:11', 1),
(1706, 'V30485684', 248, 'Éxito', 'Asignar Repartidor al pedido con id: FACT-26169-00001-13', '190.97.229.53', '{\"cedula_repartidor\":{\"registrado\":\"V12344567\"},\"status_pedido\":{\"modificado\":7},\"cedula_usuario\":{\"registrado\":\"V30485684\"}}', '2026-06-22 12:14:10', 1),
(1707, 'V30485684', 248, 'Éxito', 'Asignar Repartidor al pedido con id: FACT-26164-00006-74', '190.97.229.53', '{\"cedula_repartidor\":{\"registrado\":\"V12344567\"},\"status_pedido\":{\"modificado\":7},\"cedula_usuario\":{\"registrado\":\"V30485684\"}}', '2026-06-22 12:16:43', 1),
(1708, 'V30485684', 248, 'Éxito', 'Asignar Repartidor al pedido con id: FACT-26164-00005-85', '190.97.229.53', '{\"cedula_repartidor\":{\"registrado\":\"V12344567\"},\"status_pedido\":{\"modificado\":7},\"cedula_usuario\":{\"registrado\":\"V30485684\"}}', '2026-06-22 13:17:56', 1),
(1710, 'V30485684', 248, 'Éxito', 'registrar pedido con id: FACT-26172-00001-48', '190.97.229.53', '{\"productos\":[{\"id_producto\":{\"registrado\":\"PROD-26150-00001-39\"},\"id_unidad_medida\":{\"registrado\":2},\"id_categoria_producto\":{\"registrado\":1},\"nombre_producto\":{\"registrado\":\"CLORO\"},\"precio_producto\":{\"registrado\":\"1.00\"},\"stock_producto\":{\"registrado\":\"1000.00\"},\"stock_minimo_producto\":{\"registrado\":\"2.00\"},\"status\":{\"registrado\":1},\"nombre_unidad_medida\":{\"registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"registrado\":\"L\"},\"equivalencia_ub\":{\"registrado\":\"1000.00\"},\"nombre_categoria_producto\":{\"registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"registrado\":1},\"id_presentacion_producto\":{\"registrado\":\"PRPR-26160-00001-60\"},\"id_presentacion\":{\"registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"registrado\":1},\"foto_presentacion\":{\"registrado\":\"presentaciones_productos_2026-06-10_11_15_34.jpg?v=2026-06-10_17_54_04\"},\"nombre_presentacion\":{\"registrado\":\"POR LITRO\"},\"cantidad_pmp\":{\"registrado\":\"1.00\"},\"precio_bs\":{\"registrado\":\"567.00\"},\"precio_dolar\":{\"registrado\":\"1.00\"},\"tipo_item\":{\"registrado\":\"productos\"},\"cantidad\":{\"registrado\":20}}],\"pagos\":[{\"id_metodo_pago\":{\"registrado\":\"1\"},\"id_moneda\":{\"registrado\":\"2\"},\"id_banco_emisor\":{\"registrado\":\"1\"},\"id_banco_receptor\":{\"registrado\":\"1\"},\"referencia_pago\":{\"registrado\":\"123456\"},\"monto_pago\":{\"registrado\":115060}}],\"delivery\":{\"latitud\":{\"registrado\":9.85951786980876},\"longitud\":{\"registrado\":-69.61194737227689}},\"comprobantes_pago\":[{\"eliminado\":[\"comprobantes_pagos_2026_06_22_13_18_56_36.png\"]}]}', '2026-06-22 13:18:56', 1),
(1711, 'V30485684', 248, 'Éxito', 'Asignar Repartidor al pedido con id: FACT-26164-00004-76', '190.97.229.53', '{\"cedula_repartidor\":{\"registrado\":\"V12344567\"},\"status_pedido\":{\"modificado\":7},\"cedula_usuario\":{\"registrado\":\"V30485684\"}}', '2026-06-22 13:19:42', 1),
(1712, 'V30485684', 248, 'Éxito', 'Asignar Repartidor al pedido con id: FACT-26164-00003-63', '190.97.229.53', '{\"cedula_repartidor\":{\"registrado\":\"V12344567\"},\"status_pedido\":{\"modificado\":7},\"cedula_usuario\":{\"registrado\":\"V30485684\"}}', '2026-06-22 14:57:45', 1),
(1718, 'V30485684', 248, 'Éxito', 'registrar pedido con id: FACT-26172-00001-26', '190.97.229.53', NULL, '2026-06-22 16:38:01', 1),
(1719, 'V30485684', 248, 'Éxito', 'registrar pedido con id: FACT-26172-00002-88', '190.97.229.53', NULL, '2026-06-22 16:39:43', 1),
(1722, 'V30485684', 248, 'Éxito', 'registrar pedido con id: FACT-26172-00003-47', '190.97.229.53', NULL, '2026-06-22 16:59:08', 1),
(1723, 'V30485684', 248, 'Éxito', 'registrar pedido con id: FACT-26172-00001-98', '190.97.229.53', NULL, '2026-06-22 18:23:27', 1),
(1724, 'V30485684', 9, 'éxito', 'registrar', '190.97.229.53', NULL, '2026-06-22 18:27:06', 1),
(1725, 'V30485684', 9, 'éxito', 'actualizar', '190.97.229.53', NULL, '2026-06-22 18:27:18', 1),
(1726, 'V30485684', 9, 'éxito', 'eliminar', '190.97.229.53', NULL, '2026-06-22 18:27:24', 1),
(1727, 'V30485684', 9, 'éxito', 'iniciar sesión', '190.97.229.53', NULL, '2026-06-24 14:05:13', 1),
(1728, 'V30485684', 9, 'éxito', 'registrar usuario con la cedula/rif: V30485694', '0.0.0.0', NULL, '2026-06-24 18:34:59', 1),
(1729, 'V30485684', 9, 'éxito', 'actualizar usuario con la cedula/rif: V1234567', '0.0.0.0', NULL, '2026-06-24 18:35:20', 1),
(1730, 'V30485684', 9, 'éxito', 'actualizar usuario con la cedula/rif: V1234567', '0.0.0.0', NULL, '2026-06-24 18:35:27', 1),
(1731, 'V30485684', 9, 'éxito', 'eliminar usuario con la cedula/rif: V1234567', '0.0.0.0', NULL, '2026-06-24 18:35:31', 1),
(1732, 'V30485684', 247, 'fallido', 'actualizar', '0.0.0.0', NULL, '2026-06-24 18:36:54', 1),
(1733, 'V30485684', 247, 'éxito', 'actualizar', '0.0.0.0', NULL, '2026-06-24 18:36:58', 1),
(1734, 'V30485684', 247, 'éxito', 'actualizar', '0.0.0.0', NULL, '2026-06-24 18:37:03', 1),
(1735, 'V30485684', 247, 'éxito', 'registrar', '0.0.0.0', NULL, '2026-06-24 18:37:18', 1),
(1736, 'V30485684', 247, 'éxito', 'actualizar', '0.0.0.0', NULL, '2026-06-24 18:37:26', 1),
(1737, 'V30485684', 247, 'fallido', 'actualizar', '0.0.0.0', NULL, '2026-06-24 18:37:32', 1),
(1738, 'V30485684', 247, 'fallido', 'actualizar', '0.0.0.0', NULL, '2026-06-24 18:37:34', 1),
(1739, 'V30485684', 247, 'fallido', 'actualizar', '0.0.0.0', NULL, '2026-06-24 18:37:45', 1),
(1740, 'V30485684', 247, 'fallido', 'actualizar', '0.0.0.0', NULL, '2026-06-24 18:37:52', 1),
(1741, 'V30485684', 247, 'éxito', 'actualizar', '0.0.0.0', NULL, '2026-06-24 18:37:56', 1),
(1742, 'V30485684', 247, 'éxito', 'eliminar', '0.0.0.0', NULL, '2026-06-24 18:38:00', 1),
(1743, 'V30485684', 17, 'Éxito', 'actualizar Empresa de envíos con id: 6', '0.0.0.0', NULL, '2026-06-24 18:43:01', 1),
(1744, 'V30485684', 250, 'Éxito', 'Registrar', '0.0.0.0', NULL, '2026-06-24 18:43:13', 1),
(1745, 'V30485684', 250, 'Éxito', 'eliminar Empresa de envíos con id: 34', '0.0.0.0', NULL, '2026-06-24 18:43:16', 1),
(1746, 'V30485684', 17, 'Éxito', 'actualizar Empresa de envíos con id: 6', '0.0.0.0', NULL, '2026-06-24 18:43:21', 1),
(1747, 'V30485684', 303, 'éxito', 'registrar', '0.0.0.0', NULL, '2026-06-24 18:43:33', 1),
(1748, 'V30485684', 303, 'éxito', 'actualizar', '0.0.0.0', NULL, '2026-06-24 18:43:39', 1),
(1749, 'V30485684', 303, 'éxito', 'actualizar', '0.0.0.0', NULL, '2026-06-24 18:43:44', 1),
(1750, 'V30485684', 303, 'éxito', 'eliminar', '0.0.0.0', NULL, '2026-06-24 18:43:49', 1),
(1751, 'V30485684', 9, 'éxito', 'registrar usuario con la cedula/rif: V12345666', '190.97.229.53', NULL, '2026-06-24 23:38:15', 1),
(1752, 'V30485684', 9, 'éxito', 'iniciar sesión', '190.97.229.53', NULL, '2026-06-25 13:27:32', 1),
(1753, 'V30485684', 9, 'éxito', 'iniciar sesión', '181.208.252.213', NULL, '2026-06-25 21:18:50', 1),
(1754, 'V30485684', 9, 'éxito', 'iniciar sesión', '190.97.229.53', NULL, '2026-06-26 11:58:28', 1),
(1755, 'V30485684', 248, 'Éxito', 'Asignar Repartidor al pedido con id: FACT-26172-00001-98', '190.97.229.53', NULL, '2026-06-26 12:06:21', 1),
(1756, 'V30485684', 248, 'Éxito', 'Actualizar pedido con id: FACT-26172-00001-98', '190.97.229.53', NULL, '2026-06-26 12:14:16', 1),
(1759, 'V30485684', 248, 'Éxito', 'registrar pedido con id: FACT-26176-00001-02', '190.97.229.53', NULL, '2026-06-26 12:52:24', 1),
(1760, 'V30485684', 248, 'Éxito', 'Asignar Repartidor al pedido con id: FACT-26176-00001-02', '190.97.229.53', NULL, '2026-06-26 12:54:28', 1),
(1761, 'V30485684', 248, 'Éxito', 'Actualizar pedido con id: FACT-26176-00001-02', '190.97.229.53', NULL, '2026-06-26 12:54:34', 1),
(1763, 'V30485684', 248, 'Éxito', 'registrar pedido con id: FACT-26176-00002-51', '181.208.252.213', NULL, '2026-06-26 12:57:12', 1),
(1764, 'V30485684', 248, 'Éxito', 'Asignar Repartidor al pedido con id: FACT-26176-00002-51', '181.208.252.213', NULL, '2026-06-26 12:57:55', 1),
(1765, 'V30485684', 248, 'Éxito', 'Actualizar pedido con id: FACT-26176-00002-51', '181.208.252.213', NULL, '2026-06-26 12:58:14', 1),
(1766, 'V30485684', 248, 'Éxito', 'registrar pedido con id: FACT-26176-00003-44', '181.208.252.213', NULL, '2026-06-26 13:00:46', 1),
(1767, 'V30485684', 248, 'Éxito', 'Asignar Repartidor al pedido con id: FACT-26176-00003-44', '181.208.252.213', NULL, '2026-06-26 13:01:24', 1),
(1768, 'V30485684', 248, 'Éxito', 'Actualizar pedido con id: FACT-26176-00003-44', '181.208.252.213', NULL, '2026-06-26 13:02:24', 1),
(1774, 'V30485684', 248, 'Éxito', 'registrar pedido con id: FACT-26176-00004-03', '181.208.252.213', NULL, '2026-06-26 19:59:20', 1),
(1776, 'V30485684', 248, 'Éxito', 'registrar pedido con id: FACT-26176-00005-46', '190.97.229.53', NULL, '2026-06-26 20:22:00', 1),
(1777, 'V30485684', 248, 'Éxito', 'registrar pedido con id: FACT-26176-00006-46', '190.97.229.53', NULL, '2026-06-26 20:24:45', 1),
(1778, 'V30485684', 248, 'Éxito', 'registrar pedido con id: FACT-26177-00001-87', '37.203.35.5', NULL, '2026-06-27 13:10:08', 1),
(1779, 'V30485684', 248, 'Éxito', 'Asignar Repartidor al pedido con id: FACT-26177-00001-87', 'false', NULL, '2026-06-27 13:40:34', 1),
(1780, 'V30485684', 248, 'Éxito', 'Actualizar pedido con id: FACT-26177-00001-87', 'false', NULL, '2026-06-27 13:40:44', 1),
(1782, 'V30485684', 18, 'Éxito', 'registrar Cambios de IVA', 'false', '{\"id_cambio_iva\":{\"anterior\":254,\"nuevo\":256},\"monto_cambio_iva\":{\"anterior\":\"78.00\",\"nuevo\":\"16.00\"},\"fecha_cambio_iva\":{\"anterior\":\"2026-06-20 22:10:21\",\"nuevo\":\"2026-06-27 13:41:17\"}}', '2026-06-27 13:41:17', 1),
(1783, 'V30485684', 248, 'Éxito', 'Actualizar pedido con id: FACT-26176-00004-03', 'false', NULL, '2026-06-27 14:16:07', 1),
(1791, 'V30485684', 9, 'éxito', 'iniciar sesión', 'false', NULL, '2026-06-27 17:23:30', 1),
(1792, 'V30485684', 23, 'Éxito', 'actualizar', '37.203.35.5', '{\"valor_moneda\":{\"anterior\":\"567.00\",\"nuevo\":\"623.02\"}}', '2026-06-27 18:38:28', 1),
(1798, 'V30485684', 9, 'éxito', 'iniciar sesión', 'false', NULL, '2026-06-28 13:56:44', 1),
(1799, 'V30485684', 246, 'Fallido', 'Registrar produccion', 'false', NULL, '2026-06-28 14:25:12', 1),
(1986, 'V30485684', 248, 'Éxito', 'registrar pedido con id: FACT-26178-00001-25', 'false', NULL, '2026-06-28 20:40:43', 1),
(1987, 'V30485684', 248, 'Éxito', 'Asignar Repartidor al pedido con id: FACT-26178-00001-25', 'false', NULL, '2026-06-28 20:42:51', 1),
(1988, 'V30485684', 248, 'Éxito', 'Actualizar pedido con id: FACT-26178-00001-25', 'false', NULL, '2026-06-28 20:49:53', 1),
(1989, 'V30485684', 248, 'Éxito', 'registrar pedido con id: FACT-26178-00002-82', 'false', NULL, '2026-06-28 21:08:15', 1),
(1990, 'V30485684', 248, 'Éxito', 'Asignar Repartidor al pedido con id: FACT-26178-00002-82', 'false', NULL, '2026-06-28 21:08:27', 1),
(1991, 'V30485684', 248, 'Éxito', 'registrar pedido con id: FACT-26178-00003-62', 'false', NULL, '2026-06-28 21:49:27', 1),
(1993, 'V30485684', 248, 'Éxito', 'Actualizar pedido con id: FACT-26178-00003-62', 'false', '{\"status_pedido\":{\"modificado\":6},\"productos\":[{\"id_orden_entrega_presupuesto\":{\"eliminado\":\"FACT-26178-00003-62\"},\"cedula_usuario\":{\"eliminado\":\"\"},\"id_cambio_iva\":{\"eliminado\":256},\"rif_cedula_cliente\":{\"eliminado\":\"V30485684\"},\"fecha_orden_entrega_presupuesto\":{\"eliminado\":\"2026-06-28 21:49:25\"},\"status\":{\"eliminado\":1},\"id_producto_factura\":{\"eliminado\":316},\"id_presentacion_producto\":{\"eliminado\":\"PRPR-26159-00002-84\"},\"cantidad_producto\":{\"eliminado\":\"1.00\"},\"id_producto\":{\"eliminado\":\"PROD-26150-00001-39\"},\"id_presentacion\":{\"eliminado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"eliminado\":1},\"foto_presentacion\":{\"eliminado\":\"presentaciones_productos_2026-06-10_18_06_25.png?v=2026-06-14_17_46_10\"},\"id_unidad_medida\":{\"eliminado\":2},\"id_categoria_producto\":{\"eliminado\":1},\"nombre_producto\":{\"eliminado\":\"CLORO\"},\"precio_producto\":{\"eliminado\":\"1.00\"},\"stock_producto\":{\"eliminado\":\"193.00\"},\"stock_minimo_producto\":{\"eliminado\":\"2.00\"},\"nombre_presentacion\":{\"eliminado\":\"PIPA\"},\"cantidad_pmp\":{\"eliminado\":\"200.00\"},\"precio_producto_factura\":{\"eliminado\":\"1.00\"},\"cantidad_bruta\":{\"eliminado\":\"200.00\"},\"precio_presentacion_factura\":{\"eliminado\":\"200.00\"},\"descuento\":{\"eliminado\":18},\"precioSinDescuento\":{\"eliminado\":180},\"subtotal_factura\":{\"eliminado\":180}}]}', '2026-06-28 22:06:22', 1),
(1996, 'V30485684', 248, 'Éxito', 'registrar pedido con id: FACT-26179-00001-18', '181.208.252.213', NULL, '2026-06-29 00:43:15', 1),
(1997, 'V30485684', 248, 'Éxito', 'Asignar Repartidor al pedido con id: FACT-26179-00001-18', '181.208.252.213', NULL, '2026-06-29 00:45:26', 1),
(1998, 'V30485684', 248, 'Éxito', 'Actualizar pedido con id: FACT-26179-00001-18', '181.208.252.213', '{\"status_pedido\":{\"modificado\":8}}', '2026-06-29 00:45:33', 1),
(1999, 'V30485684', 248, 'Éxito', 'Actualizar pedido con id: FACT-26178-00002-82', '181.208.252.213', '{\"status_pedido\":{\"modificado\":8}}', '2026-06-29 00:45:41', 1),
(2021, 'V30485684', 248, 'Fallido', 'registrar', '181.208.252.213', NULL, '2026-06-29 01:55:57', 1),
(2022, 'V30485684', 248, 'Éxito', 'registrar', '190.97.229.53', NULL, '2026-06-29 01:58:21', 1),
(2023, 'V30485684', 248, 'Éxito', 'Asignar Repartidor al pedido (FACT-26179-00002-79)', '190.97.229.53', NULL, '2026-06-29 01:58:52', 1),
(2024, 'V30485684', 248, 'Éxito', 'Actualizar pedido (FACT-26179-00002-79)', '190.97.229.53', NULL, '2026-06-29 01:59:38', 1),
(2025, 'V30485684', 248, 'Fallido', 'registrar', '190.97.229.53', NULL, '2026-06-29 02:01:29', 1),
(2026, 'V30485684', 248, 'Éxito', 'registrar', '190.97.229.53', NULL, '2026-06-29 02:02:05', 1),
(2027, 'V30485684', 248, 'Éxito', 'Asignar Repartidor al pedido (FACT-26179-00003-87)', '190.97.229.53', NULL, '2026-06-29 02:02:40', 1),
(2028, 'V30485684', 248, 'Éxito', 'Actualizar pedido (FACT-26179-00003-87)', '190.97.229.53', NULL, '2026-06-29 02:02:50', 1),
(2034, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"modificado\":\"2026-06-28 13:56:44\"}}', '2026-06-29 16:16:10', 1),
(2035, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"modificado\":\"2026-06-29 16:16:10\"}}', '2026-06-29 16:17:49', 1),
(2037, 'V30485684', 305, 'Éxito', 'Actualizar', '181.208.252.213', '{\"foto_presentacion\":{\"modificado\":\"presentaciones_productos_2026-06-10_11_15_21.jpg?v=2026-06-14_17_46_24\"}}', '2026-06-29 16:22:00', 1),
(2038, 'V30485684', 305, 'Éxito', 'Actualizar', '181.208.252.213', '{\"foto_presentacion\":{\"modificado\":\"presentaciones_productos_2026-06-10_11_15_34.jpg?v=2026-06-10_17_54_04\"}}', '2026-06-29 16:22:08', 1),
(2039, 'V30485684', 305, 'Éxito', 'Actualizar', '181.208.252.213', '{\"foto_presentacion\":{\"modificado\":\"presentaciones_productos_2026-06-10_18_06_25.png?v=2026-06-14_17_46_10\"}}', '2026-06-29 16:22:15', 1),
(2040, 'V30485684', 305, 'Éxito', 'Actualizar', '181.208.252.213', '{\"foto_presentacion\":{\"modificado\":\"presentaciones_productos_2026-06-10_11_27_45.jpg?v=2026-06-14_17_46_16\"}}', '2026-06-29 16:22:25', 1),
(2041, 'V30485684', 9, 'Éxito', 'Eliminar usuario con la cedula/rif: V30485694', '181.208.252.213', NULL, '2026-06-29 16:32:48', 1),
(2042, 'V30485684', 9, 'Éxito', 'Eliminar usuario con la cedula/rif: V30485686', '181.208.252.213', NULL, '2026-06-29 16:32:50', 1),
(2043, 'V30485684', 9, 'Éxito', 'Eliminar usuario con la cedula/rif: V30485683', '181.208.252.213', NULL, '2026-06-29 16:32:53', 1),
(2044, 'V30485684', 9, 'Éxito', 'Eliminar usuario con la cedula/rif: V30485680', '181.208.252.213', NULL, '2026-06-29 16:33:14', 1),
(2045, 'V30485684', 9, 'Éxito', 'Eliminar usuario con la cedula/rif: V12345666', '181.208.252.213', NULL, '2026-06-29 16:33:16', 1),
(2046, 'V30485684', 9, 'Éxito', 'registrar usuario con la cedula/rif: V30485683', '181.208.252.213', '{\"cedula_usuario\":{\"eliminado\":\"V30485683\"},\"nombre_usuario\":{\"eliminado\":\"Anderson\"},\"apellido_usuario\":{\"eliminado\":\"Freitez\"},\"correo_usuario\":{\"eliminado\":\"andersonfreitez68@gmail.com\"},\"telefono_usuario\":{\"eliminado\":\"04169484648\"},\"id_rol\":{\"eliminado\":2},\"usuario_usuario\":{\"eliminado\":\"Ander1234\"},\"contrasena_usuario\":{\"eliminado\":\"$2y$10$z8qg8.gX1U9xp2H2ePDdMerM47eYJQGiroHZdlHZglGHt2Z4ryp9W\"},\"foto_usuario\":{\"eliminado\":\"usuarios_2026_06_29_16_36_54_58.jpg\"},\"direccion_usuario\":{\"eliminado\":\"BARQUISIMETO\"}}', '2026-06-29 16:36:54', 1),
(2047, 'V30485684', 9, 'Éxito', 'Actualizar foto del usuario con la cedula/rif: V30485683', '181.208.252.213', '{\"fofo_usuario\":{\"registrado\":\"usuarios_2026_06_29_16_36_54_58.jpg\"},\"foto_usuario\":{\"eliminado\":{\"name\":\"Imagen de WhatsApp 2025-09-18 a las 10.52.56_6cf5b4d7.jpg\",\"full_path\":\"Imagen de WhatsApp 2025-09-18 a las 10.52.56_6cf5b4d7.jpg\",\"type\":\"image\\/jpeg\",\"tmp_name\":\"C:\\\\xampp\\\\tmp\\\\php5D2B.tmp\",\"error\":0,\"size\":118154}}}', '2026-06-29 16:37:06', 1),
(2049, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26160-00002-49)', '190.97.229.53', '{\"foto_presentacion\":{\"modificado\":\"presentaciones_productos_2026-06-10_11_15_21.jpg?v=2026-06-29_17_00_26\"}}', '2026-06-29 17:06:36', 1),
(2050, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26160-00002-49)', '190.97.229.53', '{\"foto_presentacion\":{\"modificado\":\"presentaciones_productos_2026-06-10_11_15_21.png?v=2026-06-29_17_06_36\"}}', '2026-06-29 17:07:30', 1),
(2051, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26160-00001-60)', '190.97.229.53', '{\"foto_presentacion\":{\"modificado\":\"presentaciones_productos_2026-06-10_11_15_34.jpg?v=2026-06-29_17_01_57\"}}', '2026-06-29 17:08:16', 1),
(2052, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26160-00002-49)', '190.97.229.53', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-06-10_11_15_21.png?v=2026-06-29_17_07_30\"}}', '2026-06-29 17:28:06', 1),
(2053, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26160-00002-49)', '181.208.252.213', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-06-10_11_15_21.jpg?v=2026-06-29_17_28_06\"}}', '2026-06-29 17:45:36', 1),
(2054, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26160-00002-49)', '181.208.252.213', '{\"foto_presentacion\":{\"Modificado\":\"\"}}', '2026-06-29 17:52:34', 1),
(2055, 'V30485684', 9, 'Éxito', 'Eliminar foto del usuario con la cedula/rif: V30485683', '181.208.252.213', NULL, '2026-06-29 17:52:54', 1),
(2056, 'V30485684', 9, 'Éxito', 'Actualizar foto del usuario con la cedula/rif: V30485683', '181.208.252.213', '{\"fofo_usuario\":{\"Registrado\":\"\"},\"foto_usuario\":{\"Eliminado\":{\"name\":\"Imagen de WhatsApp 2025-09-18 a las 10.52.56_6cf5b4d7.jpg\",\"full_path\":\"Imagen de WhatsApp 2025-09-18 a las 10.52.56_6cf5b4d7.jpg\",\"type\":\"image\\/jpeg\",\"tmp_name\":\"C:\\\\xampp\\\\tmp\\\\phpD6F9.tmp\",\"error\":0,\"size\":118154}}}', '2026-06-29 17:52:59', 1),
(2057, 'V30485684', 248, 'Éxito', 'registrar', '181.208.252.213', '{\"productos\":{\"Eliminado\":[{\"id_producto\":\"PROD-26150-00001-39\",\"id_presentacion\":\"PRES-26123-00001-28\",\"id_presentacion_producto\":\"PRPR-26160-00001-60\",\"cantidad\":1}]},\"pagos\":{\"Eliminado\":[{\"id_metodo_pago\":\"3\",\"id_moneda\":\"1\",\"referencia_pago\":\"123456\",\"monto_pago\":113}]},\"delivery\":{\"Eliminado\":{\"latitud\":9.85978348198813,\"longitud\":-69.61196464762112}},\"comprobantes_pago\":{\"Eliminado\":[\"comprobantes_pagos_2026_06_29_18_09_11_18.jpg\"]}}', '2026-06-29 18:09:12', 1),
(2058, 'V30485684', 248, 'Éxito', 'registrar', '181.208.252.213', '{\"productos\":[{\"id_producto\":{\"Registrado\":\"PROD-26150-00001-39\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26160-00001-60\"},\"cantidad\":{\"Registrado\":1}}],\"pagos\":[{\"id_metodo_pago\":{\"Registrado\":\"3\"},\"id_moneda\":{\"Registrado\":\"1\"},\"referencia_pago\":{\"Registrado\":\"123456\"},\"monto_pago\":{\"Registrado\":113}}],\"delivery\":{\"latitud\":{\"Registrado\":9.860149631106573},\"longitud\":{\"Registrado\":-69.6119865232164}},\"comprobantes_pago\":[{\"Registrado\":[\"comprobantes_pagos_2026_06_29_18_13_42_10.jpg\"]}]}', '2026-06-29 18:13:43', 1),
(2059, 'V30485684', 248, 'Éxito', 'Asignar Repartidor al pedido (FACT-26179-00002-67)', '190.97.229.53', '{\"status_pedido\":{\"Modificado\":7},\"cedula_repartidor\":{\"Registrado\":\"V12344567\"},\"cedula_usuario\":{\"Registrado\":\"V30485684\"}}', '2026-06-29 18:16:04', 1),
(2060, 'V30485684', 248, 'Éxito', 'Actualizar pedido (FACT-26179-00002-67)', '190.97.229.53', '{\"status_pedido\":{\"Modificado\":8}}', '2026-06-29 18:17:26', 1),
(2066, 'V30485684', 248, 'Éxito', 'Asignar Repartidor al pedido (FACT-26179-00001-24)', '181.208.252.213', '{\"status_pedido\":{\"Modificado\":7},\"cedula_repartidor\":{\"Registrado\":\"V12344567\"},\"cedula_usuario\":{\"Registrado\":\"V30485684\"}}', '2026-06-29 18:41:58', 1),
(2067, 'V30485684', 4, 'Éxito', 'Actualizar', '190.97.229.53', '{\"precio_producto\":{\"Modificado\":\"100.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26179-00001-42\"},\"id_presentacion\":{\"Modificado\":\"PRES-26123-00001-28\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26179-00002-77\"},\"id_presentacion\":{\"Modificado\":\"PRES-26159-00001-42\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26179-00003-41\"},\"id_presentacion\":{\"Modificado\":\"PRES-26159-00002-78\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26179-00004-94\"},\"foto_presentacion\":{\"Modificado\":\"\"}}],\"materias_primas\":[{\"cantidad_materia_prima\":{\"Modificado\":\"20000000.00\"}}]}}', '2026-06-29 18:43:02', 1),
(2068, 'V30485684', 4, 'Éxito', 'Actualizar', '190.97.229.53', '{\"precio_producto\":{\"Modificado\":\"10000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26179-00001-99\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26179-00002-05\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26179-00003-20\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26179-00004-41\"}}],\"materias_primas\":[{\"cantidad_materia_prima\":{\"Modificado\":\"2000000000.00\"}}]}}', '2026-06-29 18:43:11', 1),
(2069, 'V30485684', 9, 'Éxito', 'Iniciar sesión', 'false', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-06-30 14:21:21\"}}', '2026-06-30 14:21:21', 1),
(2070, 'V30485684', 4, 'Éxito', 'Actualizar', '181.208.252.213', '{\"precio_producto\":{\"Modificado\":\"1.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26180-00001-59\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26180-00002-16\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26180-00003-51\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26180-00004-16\"}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26180-00005-63\"},\"id_producto\":{\"Registrado\":\"PROD-26150-00001-39\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26180-00006-98\"},\"id_producto\":{\"Registrado\":\"PROD-26150-00001-39\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"cantidad_materia_prima\":{\"Modificado\":\"0.10\"}}]}}', '2026-06-30 15:53:05', 1),
(2071, 'V30485684', 4, 'Éxito', 'Actualizar', '181.208.252.213', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26180-00001-33\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26180-00002-37\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26180-00003-79\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26180-00004-33\"},\"mostrar_ecommerce\":{\"Modificado\":1}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26180-00005-68\"},\"mostrar_ecommerce\":{\"Modificado\":1}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26180-00006-46\"},\"mostrar_ecommerce\":{\"Modificado\":1}}],\"materias_primas\":[[]]}}', '2026-06-30 16:04:15', 1),
(2072, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26180-00006-46)', '181.208.252.213', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-06-30_16_04_29.jpg?v=2026-06-30_16_04_29\"}}', '2026-06-30 16:04:29', 1),
(2073, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26180-00005-68)', '181.208.252.213', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-06-30_16_04_36.png?v=2026-06-30_16_04_36\"}}', '2026-06-30 16:04:36', 1),
(2074, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26180-00003-79)', '190.97.229.53', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-06-30_16_10_35.jpg?v=2026-06-30_16_10_35\"}}', '2026-06-30 16:10:35', 1),
(2075, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26180-00006-46)', '190.97.229.53', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-06-30_16_04_29.jpg?v=2026-06-30_16_21_03\"}}', '2026-06-30 16:21:03', 1),
(2076, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26180-00005-68)', '190.97.229.53', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-06-30_16_04_36.jpg?v=2026-06-30_16_24_00\"}}', '2026-06-30 16:24:00', 1),
(2077, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26180-00004-33)', '190.97.229.53', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-06-30_16_24_07.jpg?v=2026-06-30_16_24_07\"}}', '2026-06-30 16:24:07', 1),
(2078, 'V30485684', 247, 'Éxito', 'Actualizar', '190.97.229.53', '{\"minimo_km_ruta\":{\"Modificado\":\"110.00\"}}', '2026-06-30 16:27:49', 1),
(2079, 'V30485684', 247, 'Éxito', 'Registrar', '190.97.229.53', '{\"nombre_ruta\":{\"Registrado\":\"UNO\"},\"precio_ruta\":{\"Registrado\":1},\"minimo_km_ruta\":{\"Registrado\":10},\"maximo_km_ruta\":{\"Registrado\":10}}', '2026-06-30 16:28:05', 1),
(2080, 'V30485684', 247, 'Éxito', 'Actualizar', '190.97.229.53', '{\"maximo_km_ruta\":{\"Modificado\":\"1.00\"}}', '2026-06-30 16:28:30', 1),
(2081, 'V30485684', 247, 'Éxito', 'Eliminar', '190.97.229.53', NULL, '2026-06-30 16:28:33', 1),
(2082, 'V30485684', 250, 'Éxito', 'Registrar', '190.97.229.53', '{\"nombre_empresa\":{\"Registrado\":\"ZOOMM\"}}', '2026-06-30 16:29:15', 1),
(2083, 'V30485684', 250, 'Éxito', 'actualizar Empresa de envíos con id: 35', '190.97.229.53', '{\"nombre_empresa\":{\"Modificado\":\"ZOOMMM\"}}', '2026-06-30 16:29:21', 1),
(2084, 'V30485684', 250, 'Éxito', 'Eliminar Empresa de envíos con id: 35', '190.97.229.53', NULL, '2026-06-30 16:29:23', 1),
(2085, 'V30485684', 303, 'Éxito', 'registtrar', '190.97.229.53', '{\"id_metodo_pago\":{\"Registrado\":0},\"nombre_metodo_pago\":{\"Registrado\":\"ZINLI\"},\"necesita_moneda\":{\"Registrado\":1},\"necesita_banco_emisor\":{\"Registrado\":0},\"necesita_banco_receptor\":{\"Registrado\":0},\"necesita_referencia\":{\"Registrado\":0},\"mostrar_ecommerce\":{\"Registrado\":0}}', '2026-06-30 16:29:50', 1),
(2086, 'V30485684', 303, 'Éxito', 'Eliminar', '190.97.229.53', NULL, '2026-06-30 16:29:57', 1),
(2087, 'V30485684', 4, 'Éxito', 'Actualizar', '190.97.229.53', '{\"stock_producto\":{\"Modificado\":\"2000.00\"},\"stock_minimo_producto\":{\"Modificado\":\"20.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26180-00001-41\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26180-00002-67\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26180-00003-08\"},\"foto_presentacion\":{\"Modificado\":\"\"}},{\"foto_presentacion\":{\"Modificado\":\"\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26180-00005-71\"},\"foto_presentacion\":{\"Modificado\":\"\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26180-00006-67\"},\"foto_presentacion\":{\"Modificado\":\"\"}}],\"materias_primas\":[{\"cantidad_materia_prima\":{\"Modificado\":\"10.00\"}}]}}', '2026-06-30 16:31:59', 1),
(2088, 'V30485684', 247, 'Éxito', 'Actualizar', '190.97.229.53', '{\"minimo_km_ruta\":{\"Modificado\":\"11.00\"}}', '2026-06-30 16:34:48', 1),
(2089, 'V30485684', 248, 'Éxito', 'registrar', '190.97.229.53', '{\"productos\":[{\"id_producto\":{\"Registrado\":\"PROD-26150-00001-39\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26180-00003-08\"},\"cantidad\":{\"Registrado\":2}}],\"pagos\":[{\"id_metodo_pago\":{\"Registrado\":\"3\"},\"id_moneda\":{\"Registrado\":\"1\"},\"referencia_pago\":{\"Registrado\":\"123456\"},\"monto_pago\":{\"Registrado\":529}}],\"delivery\":{\"latitud\":{\"Registrado\":9.861755},\"longitud\":{\"Registrado\":-69.612045}},\"comprobantes_pago\":[{\"Registrado\":[\"comprobantes_pagos_2026_06_30_16_36_36_81.jpg\"]}]}', '2026-06-30 16:36:37', 1),
(2090, 'V30485684', 248, 'Éxito', 'Asignar Repartidor al pedido (FACT-26180-00001-62)', '190.97.229.53', '{\"status_pedido\":{\"Modificado\":7},\"cedula_repartidor\":{\"Registrado\":\"V12344567\"},\"cedula_usuario\":{\"Registrado\":\"V30485684\"}}', '2026-06-30 16:37:11', 1),
(2092, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.53', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-06-30 18:11:22\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-06-30 18:11:22', 1);

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
(1, 'info', 1),
(3, 'success', 1);

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
(3, 'facturacion', 1),
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
(253, 'inventario', 1),
(257, 'sucursalesEmpresasEnvios', 1),
(258, 'Materias Primas', 1),
(259, 'pagos', 1),
(260, 'ordenesServicios', 1),
(261, 'accesos', 1),
(262, 'modulos', 1),
(263, 'ventas', 0),
(264, 'Categorias de Productos', 1),
(266, 'facturas', 1),
(267, 'mensajesWS', 1),
(303, 'metodos-pagos', 1),
(305, 'presentaciones_productos', 1);

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
  `fecha_creacion_notificacion` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id_notificacion`, `cedula_usuario`, `id_icono_notificacion`, `id_tipo_notificacion`, `tiempo_notificacion`, `titulo_notificacion`, `texto_notificacion`, `fecha_creacion_notificacion`, `status`) VALUES
(230, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-13 19:14:14', 0),
(231, 'V30485684', 1, 2, 0, 'IVA actualizado', 'El precio del IVA acaba de ser actualizado', '2026-06-13 19:30:36', 0),
(232, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-13 19:31:53', 0),
(233, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-14 14:07:15', 0),
(234, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-14 17:46:59', 0),
(235, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-14 18:04:00', 0),
(236, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-14 18:04:38', 0),
(237, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-14 18:06:38', 0),
(238, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-14 19:10:01', 0),
(239, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-19 21:13:34', 0),
(240, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-19 21:16:50', 0),
(319, 'V30485680', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-22 16:37:59', 1),
(320, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-22 16:37:59', 0),
(321, 'V30485680', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-22 16:39:42', 1),
(322, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-22 16:39:42', 0),
(333, 'V30485680', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-22 16:59:07', 1),
(334, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-22 16:59:07', 0),
(339, 'V30485680', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-22 18:23:26', 1),
(340, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-22 18:23:26', 0),
(347, 'V30485680', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-26 12:52:23', 1),
(348, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-26 12:52:23', 0),
(349, 'V30485694', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-26 12:52:23', 1),
(350, 'V30485680', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-26 12:57:10', 1),
(351, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-26 12:57:10', 0),
(352, 'V30485694', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-26 12:57:10', 1),
(353, 'V30485680', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-26 13:00:44', 1),
(354, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-26 13:00:44', 0),
(355, 'V30485694', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-26 13:00:44', 1),
(356, 'V30485680', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-26 19:59:18', 1),
(357, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-26 19:59:18', 0),
(358, 'V30485694', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-26 19:59:18', 1),
(359, 'V30485680', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-26 20:21:59', 1),
(360, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-26 20:21:59', 0),
(361, 'V30485694', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-26 20:21:59', 1),
(362, 'V30485680', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-26 20:24:44', 1),
(363, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-26 20:24:44', 0),
(364, 'V30485694', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-26 20:24:44', 1),
(365, 'V30485680', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-27 13:10:07', 1),
(366, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-27 13:10:07', 0),
(367, 'V30485694', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-27 13:10:07', 1),
(368, 'V30485680', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-28 20:40:41', 1),
(369, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-28 20:40:41', 0),
(370, 'V30485694', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-28 20:40:41', 1),
(371, 'V30485680', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-28 21:08:13', 1),
(372, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-28 21:08:13', 0),
(373, 'V30485694', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-28 21:08:13', 1),
(374, 'V30485680', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-28 21:49:26', 1),
(375, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-28 21:49:26', 0),
(376, 'V30485694', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-28 21:49:26', 1),
(377, 'V30485680', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-29 00:43:14', 1),
(378, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-29 00:43:14', 0),
(379, 'V30485694', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-29 00:43:14', 1),
(380, 'V30485680', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-29 01:58:20', 1),
(381, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-29 01:58:20', 0),
(382, 'V30485694', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-29 01:58:20', 1),
(383, 'V30485680', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-29 02:02:04', 1),
(384, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-29 02:02:04', 0),
(385, 'V30485694', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-29 02:02:04', 1),
(386, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-29 18:09:11', 0),
(387, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-29 18:13:42', 0),
(388, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-06-30 16:36:36', 1);

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
(645, 'ver bitácora', 1),
(646, 'PERMISOSN', 0),
(647, 'ver detalles de pedidos propios', 1),
(648, 'cambiar estado de los pedidos', 1),
(649, 'cancelar pedidos', 1),
(650, 'despachar pedidos', 1),
(651, 'ver pedidos propios', 1),
(652, 'ver pedidos de los clientes', 1),
(653, 'imprimir pedidos', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prompts_usuarios`
--

CREATE TABLE `prompts_usuarios` (
  `id_prompt_usuario` int(11) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `prompt` text NOT NULL,
  `respuesta_bot` text NOT NULL,
  `fecha_prompt` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `prompts_usuarios`
--

INSERT INTO `prompts_usuarios` (`id_prompt_usuario`, `cedula_usuario`, `prompt`, `respuesta_bot`, `fecha_prompt`, `status`) VALUES
(1, 'V30485684', 'Soporte técnico', '', '2026-06-27 14:16:42', 1);

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
(4, 'DJJ', 0),
(5, 'SONO', 0);

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
  `direccion_usuario` varchar(255) NOT NULL,
  `ultimo_acceso_usuario` datetime NOT NULL DEFAULT current_timestamp(),
  `intentos_inicio_sesion_fallidos_usuario` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`cedula_usuario`, `id_rol`, `nombre_usuario`, `apellido_usuario`, `usuario_usuario`, `contrasena_usuario`, `telefono_usuario`, `correo_usuario`, `foto_usuario`, `direccion_usuario`, `ultimo_acceso_usuario`, `intentos_inicio_sesion_fallidos_usuario`, `status`) VALUES
('V12345666', 2, 'Anderson', 'Freitez', 'Ander123499', '$2y$10$/Xy1pVC6e1kjmNJgdutBCeJCT2fUHBR3nCxyDZJCFWVFTcXJbDNm6', '04169484999', 'andersonfreitez776@gmail.com', 'usuarios_2026_06_24_23_38_15_25.jpg', 'BARQUISIMETO', '2026-06-24 23:38:15', 0, 0),
('V1234567', 2, 'Anderson', 'Freitez', 'Ander1239', '$2y$10$o4NZdHyA8DC48693PT/xcuiYLZEp944yhZUenpn2nFvU2DI4UZ.o.', '04161234567', 'andersonfreitez96@gmail.com', 'usuarios_2026_06_21_13_32_34_11.jpg?v=2026-06-24_18_22_02', 'SANARE', '2026-06-21 13:32:34', 0, 0),
('V304856111', 1, 'Anderson', 'Freitei', 'Ander1230', '$2y$10$EA7X1VnuaVraVhT.HX0/ouoP9nHpAWsrEYhhxjhm7y549N0KHcYTK', '04169484647', 'andersonfreitez68@gmail.com', 'usuarios_2026_06_22_18_27_06_43.jpg', 'BARQUISIMETO', '2026-06-22 18:27:06', 0, 0),
('V30485680', 1, 'Anderson', 'Freitez', 'Ander12398', '$2y$10$2uO1vyvosR8NTOHpUbCHwuE0Tvpbg9YqE3bTHZRYe.VToDZMkfADe', '04169484655', 'andersonfreitez76@gmail.com', '', 'SANARE', '2026-06-21 13:16:46', 0, 0),
('V30485683', 2, 'Anderson', 'Freitez', 'Ander1234', '$2y$10$z8qg8.gX1U9xp2H2ePDdMerM47eYJQGiroHZdlHZglGHt2Z4ryp9W', '04169484648', 'andersonfreitez68@gmail.com', 'usuarios_2026-06-29_17_53_00.jpg?v=2026-06-29_17_53_00', 'BARQUISIMETO', '2026-06-13 17:37:36', 0, 1),
('V30485684', 1, 'Anderson', 'Freitez', 'Ander123', '$2y$10$TQpxZt7LRgNR0ir01QuGMOhu1/1ptER5gKVNMOAl3SWZLOzVQtvy2', '04169484649', 'andersonfreitez6@gmail.com', '', 'SANARE', '2026-06-30 18:11:22', 0, 1),
('V30485686', 3, 'Anderson', 'Freitez', 'Ander123999', '$2y$10$MX64wBFxnxLXQgHyiUCtNe.ZzOcIUqyXXLoo5NiomfLaAx5d0DJ02', '04169999999', 'andersonfreitez6999@gmail.com', '', 'SANARE', '2026-06-25 00:32:28', 0, 0),
('V30485694', 1, 'Anderson', 'Freitez', 'Ander1230', '$2y$10$FWE4RUlbWzDqd3CDTPfqXeRz9JpYRibZ.gQ4wxDgjuWJQctAkDZiC', '04169484699', 'andersonfreitez996@gmail.com', 'usuarios_2026_06_24_18_34_59_39.jpg', 'BARQUISIMETO', '2026-06-24 18:34:59', 0, 0);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_usuarios_todos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_usuarios_todos` (
`direccion_usuario` varchar(255)
,`cedula_usuario` varchar(20)
,`id_rol` int(11)
,`nombre_rol` varchar(50)
,`nombre_usuario` varchar(50)
,`apellido_usuario` varchar(50)
,`telefono_usuario` varchar(11)
,`correo_usuario` varchar(150)
,`usuario_usuario` varchar(50)
,`foto_usuario` varchar(255)
,`status` tinyint(1)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `v_usuarios_todos`
--
DROP TABLE IF EXISTS `v_usuarios_todos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_usuarios_todos`  AS SELECT `u`.`direccion_usuario` AS `direccion_usuario`, `u`.`cedula_usuario` AS `cedula_usuario`, `u`.`id_rol` AS `id_rol`, `ro`.`nombre_rol` AS `nombre_rol`, `u`.`nombre_usuario` AS `nombre_usuario`, `u`.`apellido_usuario` AS `apellido_usuario`, `u`.`telefono_usuario` AS `telefono_usuario`, `u`.`correo_usuario` AS `correo_usuario`, `u`.`usuario_usuario` AS `usuario_usuario`, `u`.`foto_usuario` AS `foto_usuario`, `u`.`status` AS `status` FROM (`usuarios` `u` join `roles` `ro` on(`ro`.`id_rol` = `u`.`id_rol`)) WHERE `u`.`cedula_usuario` <> 30485684 ;

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
  ADD PRIMARY KEY (`id_modulo`),
  ADD KEY `nombre_modulo_indice` (`nombre_modulo`);

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
  ADD PRIMARY KEY (`id_permiso`),
  ADD KEY `nombre_permiso_indice` (`nombre_permiso`);

--
-- Indices de la tabla `prompts_usuarios`
--
ALTER TABLE `prompts_usuarios`
  ADD PRIMARY KEY (`id_prompt_usuario`),
  ADD KEY `cedula_usuario_prompts_usuarios_fk` (`cedula_usuario`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`),
  ADD KEY `nombre_rol_indice` (`nombre_rol`) USING BTREE;

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
  MODIFY `id_acceso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1028;

--
-- AUTO_INCREMENT de la tabla `acciones_resagadas_usuarios`
--
ALTER TABLE `acciones_resagadas_usuarios`
  MODIFY `id_accion_resagada_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=749;

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id_bitacora` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2093;

--
-- AUTO_INCREMENT de la tabla `iconos_notificaciones`
--
ALTER TABLE `iconos_notificaciones`
  MODIFY `id_icono_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=306;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=389;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=654;

--
-- AUTO_INCREMENT de la tabla `prompts_usuarios`
--
ALTER TABLE `prompts_usuarios`
  MODIFY `id_prompt_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

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
-- Filtros para la tabla `prompts_usuarios`
--
ALTER TABLE `prompts_usuarios`
  ADD CONSTRAINT `cedula_usuario_prompts_usuarios_fk` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `id_rol_usuarios_fk` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
