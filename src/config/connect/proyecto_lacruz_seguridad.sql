-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-08-2026 a las 16:48:23
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
(1008, 3, 18, 618, 1),
(1009, 3, 9, 618, 1),
(1010, 3, 8, 618, 1),
(1011, 3, 1, 618, 1),
(1012, 3, 249, 618, 1),
(1013, 3, 264, 618, 1),
(1014, 3, 251, 618, 1),
(1015, 3, 261, 618, 1),
(1016, 3, 261, 619, 1),
(1017, 3, 251, 619, 1),
(1018, 3, 18, 619, 1),
(1019, 3, 264, 619, 1),
(1020, 3, 249, 619, 1),
(1021, 3, 1, 619, 1),
(1022, 3, 8, 619, 1),
(1023, 3, 29, 618, 1),
(1024, 3, 250, 618, 1),
(1025, 3, 250, 619, 1),
(1026, 1, 248, 653, 1),
(1027, 3, 9, 622, 1),
(1028, 1, 267, 618, 1),
(1029, 1, 267, 619, 1),
(1030, 1, 267, 620, 1),
(1031, 1, 267, 621, 1),
(1032, 1, 303, 618, 1),
(1033, 1, 266, 619, 1),
(1034, 1, 266, 618, 1),
(1035, 1, 266, 620, 1),
(1036, 1, 266, 621, 1),
(1037, 1, 253, 655, 1),
(1038, 1, 253, 657, 1),
(1039, 1, 253, 656, 1),
(1040, 1, 253, 654, 1),
(1041, 1, 261, 658, 1),
(1042, 1, 261, 659, 1),
(1043, 1, 261, 660, 1),
(1047, 1, 248, 662, 1),
(1048, 1, 4, 663, 1),
(1049, 1, 23, 664, 1),
(1050, 1, 261, 665, 1),
(1051, 1, 308, 618, 1),
(1052, 1, 308, 620, 1),
(1053, 1, 308, 619, 1),
(1054, 1, 308, 621, 1),
(1055, 1, 308, 622, 1),
(1061, 1, 307, 618, 1),
(1062, 1, 307, 619, 1),
(1063, 1, 307, 620, 1),
(1064, 1, 307, 621, 1),
(1065, 1, 307, 622, 1),
(1066, 1, 303, 619, 1),
(1067, 1, 303, 620, 1),
(1068, 1, 303, 621, 1),
(1069, 1, 303, 622, 1);

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
(1139, 9, 'V30485688', 'borrarDataModuloSS', 1),
(1140, 9, 'V30485688', 'actDT', 1),
(1141, 9, 'V30485681', 'borrarDataModuloSS', 1),
(1143, 9, 'V30485681', 'actDT', 1),
(1151, 248, 'V30485681', 'borrarDataModuloSS', 1),
(1153, 248, 'V30485681', 'actDT', 1),
(1159, 247, 'V30485681', 'borrarDataModuloSS', 1),
(1161, 247, 'V30485681', 'actDT', 1),
(1167, 250, 'V30485681', 'borrarDataModuloSS', 1),
(1169, 250, 'V30485681', 'actDT', 1),
(1191, 19, 'V30485681', 'borrarDataModuloSS', 1),
(1193, 19, 'V30485681', 'actDT', 1),
(1227, 4, 'V30485681', 'borrarDataModuloSS', 1),
(1229, 4, 'V30485681', 'actDT', 1);

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
(2247, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-02 19:16:47\"}}', '2026-08-02 19:16:47', 1),
(2248, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-03 15:20:56\"}}', '2026-08-03 15:20:56', 1),
(2249, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-03 15:37:44\"}}', '2026-08-03 15:37:44', 1),
(2250, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-03 16:26:26\"}}', '2026-08-03 16:26:26', 1),
(2251, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-03 16:34:09\"}}', '2026-08-03 16:34:09', 1),
(2252, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-03 16:44:58\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-03 16:44:58', 1),
(2253, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-06 14:34:11\"}}', '2026-08-06 14:34:11', 1),
(2254, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 11:27:54\"}}', '2026-08-07 11:27:54', 1),
(2255, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 11:45:45\"}}', '2026-08-07 11:45:45', 1),
(2256, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 11:48:07\"}}', '2026-08-07 11:48:07', 1),
(2257, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 11:50:21\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-07 11:50:21', 1),
(2258, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 11:52:24\"}}', '2026-08-07 11:52:24', 1),
(2259, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 11:59:48\"}}', '2026-08-07 11:59:48', 1),
(2260, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 12:02:20\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-07 12:02:20', 1),
(2261, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 12:04:32\"}}', '2026-08-07 12:04:32', 1),
(2262, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 12:11:47\"}}', '2026-08-07 12:11:47', 1),
(2263, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 12:29:25\"}}', '2026-08-07 12:29:25', 1),
(2264, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 12:57:18\"}}', '2026-08-07 12:57:19', 1),
(2265, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 13:17:54\"}}', '2026-08-07 13:17:54', 1),
(2266, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 13:20:00\"}}', '2026-08-07 13:20:00', 1),
(2267, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:24:42\"}}', '2026-08-07 14:24:42', 1),
(2268, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:31:05\"}}', '2026-08-07 14:31:05', 1),
(2269, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:34:53\"}}', '2026-08-07 14:34:53', 1),
(2270, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:36:19\"}}', '2026-08-07 14:36:19', 1),
(2271, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:39:01\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-07 14:39:01', 1),
(2272, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:40:56\"}}', '2026-08-07 14:40:56', 1),
(2273, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:44:05\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-07 14:44:05', 1),
(2274, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:53:46\"}}', '2026-08-07 14:48:46', 1),
(2275, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 14:57:49\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-07 14:52:49', 1),
(2276, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 15:01:59\"}}', '2026-08-07 14:56:59', 1),
(2277, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 15:07:59\"}}', '2026-08-07 15:02:59', 1),
(2278, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 15:24:04\"}}', '2026-08-07 15:19:04', 1),
(2279, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-07 15:29:35\"}}', '2026-08-07 15:24:35', 1),
(2280, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 11:44:29\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-08 11:39:29', 1),
(2281, 'V30485684', 28, 'Sin cambios', 'Actualizar con id672', '190.97.229.57', NULL, '2026-08-08 11:40:13', 1),
(2282, 'V30485684', 28, 'Sin cambios', 'Actualizar con id672', '190.97.229.57', NULL, '2026-08-08 11:40:55', 1),
(2283, 'V30485684', 28, 'Sin cambios', 'Actualizar con id672', '190.97.229.57', NULL, '2026-08-08 11:40:59', 1),
(2284, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 12:13:55\"}}', '2026-08-08 12:08:55', 1),
(2285, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 12:30:09\"}}', '2026-08-08 12:25:09', 1),
(2286, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 13:31:23\"}}', '2026-08-08 13:26:23', 1),
(2287, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 13:42:01\"}}', '2026-08-08 13:37:01', 1),
(2288, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 13:53:41\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-08 13:48:41', 1),
(2289, 'V30485684', 28, 'Éxito', 'Actualizar con id 672', '190.97.229.57', '{\"nombre_permiso\":{\"Modificado\":\"agregar pagos\"}}', '2026-08-08 13:49:57', 1),
(2290, 'V30485684', 28, 'Éxito', 'Actualizar con id 672', '190.97.229.57', '{\"nombre_permiso\":{\"Modificado\":\"agregar pago\"}}', '2026-08-08 13:50:09', 1),
(2291, 'V30485684', 28, 'Éxito', 'registrar', '190.97.229.57', '{\"id_permiso\":{\"Registrado\":673},\"nombre_permiso\":{\"Registrado\":\"permisox\"},\"status\":{\"Registrado\":1}}', '2026-08-08 13:50:22', 1),
(2292, 'V30485684', 28, 'Éxito', 'Eliminar con id 673', '190.97.229.57', NULL, '2026-08-08 13:50:26', 1),
(2294, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 16:41:32\"}}', '2026-08-08 16:36:32', 1),
(2295, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-08 17:30:34\"}}', '2026-08-08 17:25:34', 1),
(2296, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 14:25:37\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-10 14:20:37', 1),
(2297, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 14:34:26\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-10 14:29:26', 1),
(2298, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 14:35:09\"}}', '2026-08-10 14:30:09', 1),
(2299, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 14:47:03\"}}', '2026-08-10 14:42:03', 1),
(2300, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 15:04:51\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-10 14:59:51', 1),
(2301, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 16:17:40\"}}', '2026-08-10 16:12:40', 1),
(2302, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 16:29:33\"}}', '2026-08-10 16:24:33', 1),
(2303, 'V30485684', 9, 'Éxito', 'registrar usuario con la cédula/rif: V30485688', '190.97.229.57', '{\"cedula_usuario\":{\"Registrado\":\"V30485688\"},\"nombre_usuario\":{\"Registrado\":\"Anderson\"},\"apellido_usuario\":{\"Registrado\":\"Freitez\"},\"correo_usuario\":{\"Registrado\":\"andersonfremitez6@gmail.com\"},\"telefono_usuario\":{\"Registrado\":\"04169484640\"},\"id_rol\":{\"Registrado\":1},\"usuario_usuario\":{\"Registrado\":\"Ander1239\"},\"contrasena_usuario\":{\"Registrado\":\"$2y$10$petVY5x\\/7OGMh1gk\\/PuBt.R2SxsI20QXItsUrfqIG.XrEk4aScQ5m\"},\"foto_usuario\":{\"Registrado\":\"usuarios_2026_08_10_16_45_15_60.jpg\"},\"direccion_usuario\":{\"Registrado\":\"BARQUISIMETO\"}}', '2026-08-10 16:45:17', 1),
(2304, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 17:01:59\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-10 16:56:59', 1),
(2305, 'V30485684', 9, 'Éxito', 'registrar usuario con la cédula/rif: V30485681', '181.208.252.213', '{\"cedula_usuario\":{\"Registrado\":\"V30485681\"},\"nombre_usuario\":{\"Registrado\":\"Anderson\"},\"apellido_usuario\":{\"Registrado\":\"Freitez\"},\"correo_usuario\":{\"Registrado\":\"andersonfreitez66@gmail.com\"},\"telefono_usuario\":{\"Registrado\":\"04169484643\"},\"id_rol\":{\"Registrado\":1},\"usuario_usuario\":{\"Registrado\":\"Ander1236\"},\"contrasena_usuario\":{\"Registrado\":\"$2y$10$6ljN.VrGlB6mn4wLkHfDaOXke8z9135c.OYp5R.mHKk\\/y0bWnjNde\"},\"foto_usuario\":{\"Registrado\":\"usuarios_2026_08_10_16_58_55_14.jpg\"},\"direccion_usuario\":{\"Registrado\":\"BARQUISIMETO\"}}', '2026-08-10 16:58:57', 1),
(2306, 'V30485684', 9, 'Éxito', 'Actualizar usuario con la cedula/rif: V30485688', '181.208.252.213', '{\"nombre_usuario\":{\"Modificado\":\"Andersoa\"},\"cedula_usuario\":{\"Eliminado\":\"V30485688\"},\"foto_usuario\":{\"Eliminado\":\"usuarios_2026_08_10_16_45_15_60.jpg\"}}', '2026-08-10 17:00:16', 1),
(2307, 'V30485684', 9, 'Éxito', 'Actualizar foto del usuario con la cedula/rif: V30485688', '181.208.252.213', '{\"foto_usuario\":{\"name\":{\"Registrado\":\"Imagen2.jpg\"},\"full_path\":{\"Registrado\":\"Imagen2.jpg\"},\"type\":{\"Registrado\":\"image\\/jpeg\"},\"tmp_name\":{\"Registrado\":\"C:\\\\xampp\\\\tmp\\\\phpCA63.tmp\"},\"error\":{\"Registrado\":0},\"size\":{\"Registrado\":134384}},\"fofo_usuario\":{\"Eliminado\":\"usuarios_2026_08_10_16_45_15_60.jpg\"}}', '2026-08-10 17:00:26', 1),
(2308, 'V30485684', 9, 'Éxito', 'Eliminar usuario con la cedula/rif: V30485688', '181.208.252.213', NULL, '2026-08-10 17:00:36', 1),
(2309, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 17:21:15\"}}', '2026-08-10 17:16:15', 1),
(2310, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 17:27:17\"}}', '2026-08-10 17:22:17', 1),
(2311, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 17:36:12\"}}', '2026-08-10 17:31:12', 1),
(2312, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 17:45:38\"}}', '2026-08-10 17:40:38', 1),
(2313, 'V30485684', 248, 'Éxito', 'registrar', '181.208.252.213', '{\"productos\":[{\"id_producto\":{\"Registrado\":\"PROD-26150-00001-39\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26183-00015-86\"},\"cantidad\":{\"Registrado\":1}}],\"pagos\":[{\"id_metodo_pago\":{\"Registrado\":\"3\"},\"id_moneda\":{\"Registrado\":\"1\"},\"referencia_pago\":{\"Registrado\":\"123456\"},\"monto_pago\":{\"Registrado\":304}}],\"delivery\":{\"latitud\":{\"Registrado\":9.93103},\"longitud\":{\"Registrado\":-69.621948}},\"comprobantes_pago\":[{\"Registrado\":[\"comprobantes_pagos_2026_08_10_17_44_30_56.jpg\"]}]}', '2026-08-10 17:44:32', 1),
(2314, 'V30485684', 248, 'Éxito', 'Asignar Repartidor al pedido (FACT-26221-00001-60)', '181.208.252.213', '{\"status_pedido\":{\"Modificado\":7},\"cedula_repartidor\":{\"Registrado\":\"V12344567\"},\"cedula_usuario\":{\"Registrado\":\"V30485684\"}}', '2026-08-10 17:47:34', 1),
(2315, 'V30485684', 248, 'Éxito', 'Actualizar pedido (FACT-26221-00001-60)', '181.208.252.213', '{\"status_pedido\":{\"Modificado\":8}}', '2026-08-10 17:48:13', 1),
(2317, 'V30485684', 247, 'Éxito', 'Registrar', '190.97.229.57', '{\"nombre_ruta\":{\"Registrado\":\"OTRA MAS\"},\"precio_ruta\":{\"Registrado\":\"2\"},\"minimo_km_ruta\":{\"Registrado\":\"100\"},\"maximo_km_ruta\":{\"Registrado\":\"100\"}}', '2026-08-10 17:55:28', 1),
(2318, 'V30485684', 247, 'Éxito', 'Actualizar', '190.97.229.57', '{\"precio_ruta\":{\"Modificado\":\"0.20\"}}', '2026-08-10 17:55:38', 1),
(2319, 'V30485684', 247, 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-08-10 17:55:45', 1),
(2320, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 18:09:37\"}}', '2026-08-10 18:04:37', 1),
(2321, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 18:22:58\"}}', '2026-08-10 18:17:58', 1),
(2322, 'V30485684', 250, 'Éxito', 'Registrar', '181.208.252.213', '{\"nombre_empresa\":{\"Registrado\":\"ZOOMM\"}}', '2026-08-10 18:25:23', 1),
(2323, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-10 18:30:58\"}}', '2026-08-10 18:25:58', 1),
(2324, 'V30485684', 250, 'Éxito', 'Registrar', '190.97.229.57', '{\"nombre_empresa\":{\"Registrado\":\"ZOOMmm\"}}', '2026-08-10 18:26:39', 1),
(2325, 'V30485684', 250, 'Éxito', 'actualizar Empresa de envíos con id: 38', '190.97.229.57', '{\"nombre_empresa\":{\"Modificado\":\"ZOOMmm.\"}}', '2026-08-10 18:27:01', 1),
(2326, 'V30485684', 250, 'Éxito', 'Registrar', '190.97.229.57', '{\"nombre_empresa\":{\"Registrado\":\"ZOOMmm\"}}', '2026-08-10 18:31:06', 1),
(2327, 'V30485684', 250, 'Éxito', 'Eliminar Empresa de envíos con id: 39', '190.97.229.57', NULL, '2026-08-10 18:31:10', 1),
(2328, 'V30485684', 250, 'Éxito', 'Eliminar Empresa de envíos con id: 38', '190.97.229.57', NULL, '2026-08-10 18:31:13', 1),
(2329, 'V30485684', 250, 'Éxito', 'Eliminar Empresa de envíos con id: 37', '190.97.229.57', NULL, '2026-08-10 18:31:17', 1),
(2330, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 11:19:22\"}}', '2026-08-11 11:14:22', 1),
(2331, 'V30485684', 247, 'Éxito', 'Registrar', '190.97.229.57', '{\"nombre_ruta\":{\"Registrado\":\"OTRA MAS\"},\"precio_ruta\":{\"Registrado\":\"2\"},\"minimo_km_ruta\":{\"Registrado\":\"11\"},\"maximo_km_ruta\":{\"Registrado\":\"100\"}}', '2026-08-11 11:15:25', 1),
(2332, 'V30485684', 247, 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-08-11 11:15:41', 1),
(2333, 'V30485684', 250, 'Éxito', 'Registrar', '190.97.229.57', '{\"nombre_empresa\":{\"Registrado\":\"ZOOMs\"}}', '2026-08-11 11:15:59', 1),
(2334, 'V30485684', 250, 'Éxito', 'Eliminar Empresa de envíos con id: 40', '190.97.229.57', NULL, '2026-08-11 11:16:03', 1),
(2335, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 11:45:30\"}}', '2026-08-11 11:40:30', 1),
(2336, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 11:53:02\"}}', '2026-08-11 11:48:02', 1),
(2337, 'V30485684', 19, 'Éxito', 'registtrar', '190.97.229.57', '{\"id_metodo_pago\":{\"Registrado\":0},\"nombre_metodo_pago\":{\"Registrado\":\"EFECTIVOm\"},\"necesita_moneda\":{\"Registrado\":1},\"necesita_banco_emisor\":{\"Registrado\":1},\"necesita_banco_receptor\":{\"Registrado\":1},\"necesita_referencia\":{\"Registrado\":1},\"mostrar_ecommerce\":{\"Registrado\":1}}', '2026-08-11 11:50:12', 1),
(2338, 'V30485684', 19, 'Éxito', 'Actualizar', '181.208.252.213', '{\"nombre_metodo_pago\":{\"Modificado\":\"EFECTIVOmm\"}}', '2026-08-11 11:54:44', 1),
(2339, 'V30485684', 19, 'Fallido', 'Actualizar', '181.208.252.213', NULL, '2026-08-11 11:54:59', 1),
(2340, 'V30485684', 19, 'Fallido', 'Actualizar', '181.208.252.213', NULL, '2026-08-11 11:55:45', 1),
(2341, 'V30485684', 19, 'Fallido', 'Actualizar', '181.208.252.213', NULL, '2026-08-11 11:55:57', 1),
(2342, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 12:09:33\"}}', '2026-08-11 12:04:33', 1),
(2343, 'V30485684', 19, 'Fallido', 'Actualizar', '181.208.252.213', NULL, '2026-08-11 12:04:51', 1),
(2344, 'V30485684', 19, 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_moneda\":{\"Modificado\":0},\"necesita_banco_emisor\":{\"Modificado\":0}}', '2026-08-11 12:07:35', 1),
(2345, 'V30485684', 19, 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_banco_receptor\":{\"Modificado\":0}}', '2026-08-11 12:07:43', 1),
(2346, 'V30485684', 19, 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_referencia\":{\"Modificado\":0},\"mostrar_ecommerce\":{\"Modificado\":0}}', '2026-08-11 12:07:49', 1),
(2347, 'V30485684', 19, 'Éxito', 'registtrar', '181.208.252.213', '{\"id_metodo_pago\":{\"Registrado\":0},\"nombre_metodo_pago\":{\"Registrado\":\"EFECTIVOs\"},\"necesita_moneda\":{\"Registrado\":1},\"necesita_banco_emisor\":{\"Registrado\":1},\"necesita_banco_receptor\":{\"Registrado\":1},\"necesita_referencia\":{\"Registrado\":0},\"mostrar_ecommerce\":{\"Registrado\":1}}', '2026-08-11 12:08:10', 1),
(2348, 'V30485684', 19, 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_referencia\":{\"Modificado\":1}}', '2026-08-11 12:09:39', 1),
(2349, 'V30485684', 19, 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_banco_emisor\":{\"Modificado\":0}}', '2026-08-11 12:09:52', 1),
(2350, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 12:18:14\"}}', '2026-08-11 12:13:14', 1),
(2351, 'V30485684', 19, 'Éxito', 'registtrar', '190.97.229.57', '{\"id_metodo_pago\":{\"Registrado\":0},\"nombre_metodo_pago\":{\"Registrado\":\"EFECTIVOb\"},\"necesita_moneda\":{\"Registrado\":1},\"necesita_banco_emisor\":{\"Registrado\":0},\"necesita_banco_receptor\":{\"Registrado\":0},\"necesita_referencia\":{\"Registrado\":0},\"mostrar_ecommerce\":{\"Registrado\":0}}', '2026-08-11 12:13:43', 1),
(2352, 'V30485684', 19, 'Éxito', 'Actualizar', '190.97.229.57', '{\"necesita_banco_emisor\":{\"Modificado\":1},\"necesita_banco_receptor\":{\"Modificado\":1},\"necesita_referencia\":{\"Modificado\":1}}', '2026-08-11 12:15:24', 1),
(2353, 'V30485684', 19, 'Éxito', 'Actualizar', '190.97.229.57', '{\"mostrar_ecommerce\":{\"Modificado\":1}}', '2026-08-11 12:15:46', 1),
(2354, 'V30485684', 19, 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_banco_emisor\":{\"Modificado\":1}}', '2026-08-11 12:17:52', 1),
(2355, 'V30485684', 19, 'Éxito', 'Eliminar', '181.208.252.213', NULL, '2026-08-11 12:17:56', 1),
(2356, 'V30485684', 19, 'Éxito', 'Eliminar', '181.208.252.213', NULL, '2026-08-11 12:18:02', 1),
(2357, 'V30485684', 19, 'Éxito', 'Eliminar', '181.208.252.213', NULL, '2026-08-11 12:18:06', 1),
(2358, 'V30485684', 19, 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_banco_receptor\":{\"Modificado\":0},\"mostrar_ecommerce\":{\"Modificado\":0}}', '2026-08-11 12:18:27', 1),
(2359, 'V30485684', 19, 'Éxito', 'Actualizar', '181.208.252.213', '{\"necesita_referencia\":{\"Modificado\":0}}', '2026-08-11 12:18:54', 1),
(2360, 'V30485684', 4, 'Éxito', 'Registrar', '181.208.252.213', '{\"id_producto\":{\"Registrado\":\"PROD-26222-00001-83\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"CLORO2\"},\"precio_producto\":{\"Registrado\":\"1.00\"},\"stock_producto\":{\"Registrado\":\"100.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00001-86\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00001-83\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00002-79\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00001-83\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00003-67\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00001-83\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00004-38\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00001-83\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00005-00\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00001-83\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00006-27\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00001-83\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima\":{\"Registrado\":\"MATE-26123-00001-66\"},\"cantidad_materia_prima\":{\"Registrado\":\"1.00\"}}]}}', '2026-08-11 12:22:42', 1),
(2361, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 12:32:46\"}}', '2026-08-11 12:27:46', 1),
(2362, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26222-00006-27)', '190.97.229.57', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-08-11_12_33_26.jpg?v=2026-08-11_12_33_26\"}}', '2026-08-11 12:33:26', 1),
(2363, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26222-00005-00)', '190.97.229.57', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-08-11_12_33_44.jpg?v=2026-08-11_12_33_44\"}}', '2026-08-11 12:33:44', 1),
(2364, 'V30485684', 4, 'Éxito', 'Actualizar', '190.97.229.57', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00001-40\"}},[],{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00003-78\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00004-81\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00005-06\"},\"foto_presentacion\":{\"Modificado\":\"\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00006-01\"},\"foto_presentacion\":{\"Modificado\":\"\"}}],\"materias_primas\":[{\"cantidad_materia_prima\":{\"Modificado\":\"2.00\"}}]}}', '2026-08-11 12:34:12', 1),
(2365, 'V30485684', 4, 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-08-11 12:34:38', 1),
(2366, 'V30485684', 4, 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-08-11 12:36:17', 1),
(2367, 'V30485684', 4, 'Éxito', 'Registrar', '190.97.229.57', '{\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"CLORO\"},\"precio_producto\":{\"Registrado\":\"1.00\"},\"stock_producto\":{\"Registrado\":\"100.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00007-32\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00008-22\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00009-69\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00010-25\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00011-11\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00012-62\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00002-19\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima\":{\"Registrado\":\"MATE-26123-00001-66\"},\"cantidad_materia_prima\":{\"Registrado\":\"10.00\"}}]}}', '2026-08-11 12:37:05', 1),
(2368, 'V30485684', 4, 'Éxito', 'Registrar', '190.97.229.57', '{\"id_producto\":{\"Registrado\":\"PROD-26222-00003-10\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"CLORO2\"},\"precio_producto\":{\"Registrado\":\"1.00\"},\"stock_producto\":{\"Registrado\":\"1.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00013-45\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00003-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00014-75\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00003-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00015-48\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00003-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00016-38\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00003-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00017-82\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00003-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00018-55\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00003-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima\":{\"Registrado\":\"MATE-26123-00001-66\"},\"cantidad_materia_prima\":{\"Registrado\":\"10.00\"}}]}}', '2026-08-11 12:37:59', 1),
(2369, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 12:44:05\"}}', '2026-08-11 12:39:05', 1),
(2370, 'V30485684', 4, 'Éxito', 'Registrar', '181.208.252.213', '{\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"CLOROf\"},\"precio_producto\":{\"Registrado\":\"1.00\"},\"stock_producto\":{\"Registrado\":\"1.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00019-24\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_20_25.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00020-14\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_20_25.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00021-24\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_20_25.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00022-79\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_20_25.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00023-38\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_20_25.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00024-75\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_20_25.png\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima\":{\"Registrado\":\"MATE-26123-00001-66\"},\"cantidad_materia_prima\":{\"Registrado\":\"1.00\"}}]}}', '2026-08-11 12:48:20', 1),
(2371, 'V30485684', 4, 'Éxito', 'Registrar', '181.208.252.213', '{\"id_producto\":{\"Registrado\":\"PROD-26222-00005-98\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":2},\"nombre_producto\":{\"Registrado\":\"CLOROm\"},\"precio_producto\":{\"Registrado\":\"1.00\"},\"stock_producto\":{\"Registrado\":\"1.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"NO FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":0},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00025-15\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00005-98\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_53_82.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00026-98\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00005-98\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_53_82.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00027-61\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00005-98\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_53_82.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00028-84\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00005-98\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_53_82.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00029-56\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00005-98\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":1},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_53_82.png\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00030-45\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00005-98\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_48_53_82.png\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[]}}', '2026-08-11 12:48:53', 1),
(2372, 'V30485684', 4, 'Éxito', 'Registrar', '181.208.252.213', '{\"id_producto\":{\"Registrado\":\"PROD-26222-00006-10\"},\"id_unidad_medida\":{\"Registrado\":2},\"id_categoria_producto\":{\"Registrado\":1},\"nombre_producto\":{\"Registrado\":\"CLOROs\"},\"precio_producto\":{\"Registrado\":\"10.00\"},\"stock_producto\":{\"Registrado\":\"1.00\"},\"stock_minimo_producto\":{\"Registrado\":\"5.00\"},\"status\":{\"Registrado\":1},\"nombre_categoria_producto\":{\"Registrado\":\"FABRICADOS\"},\"necesitan_materias_primas\":{\"Registrado\":1},\"nombre_unidad_medida\":{\"Registrado\":\"LITRO(S)\"},\"simbolo_unidad_medida\":{\"Registrado\":\"L\"},\"equivalencia_ub\":{\"Registrado\":\"1000.00\"},\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00031-33\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00006-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26123-00001-28\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"presentaciones_productos_2026_08_11_12_50_58_55.jpg\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00032-91\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00006-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00001-42\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00033-85\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00006-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00034-70\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00006-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26159-00003-24\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00035-71\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00006-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00036-95\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00006-10\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima\":{\"Registrado\":\"MATE-26123-00001-66\"},\"cantidad_materia_prima\":{\"Registrado\":\"0.01\"}}]}}', '2026-08-11 12:50:58', 1),
(2373, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26222-00032-91)', '181.208.252.213', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-08-11_12_51_14.png?v=2026-08-11_12_51_14\"}}', '2026-08-11 12:51:14', 1),
(2374, 'V30485684', 4, 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-08-11 12:52:04', 1),
(2375, 'V30485684', 4, 'Éxito', 'Eliminar', '190.97.229.57', NULL, '2026-08-11 12:53:54', 1),
(2376, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 13:10:17\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-11 13:05:17', 1),
(2377, 'V30485684', 4, 'Éxito', 'Actualizar', '181.208.252.213', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-23\"},\"foto_presentacion\":{\"Modificado\":\"\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-74\"},\"foto_presentacion\":{\"Modificado\":\"\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-91\"},\"mostrar_ecommerce\":{\"Modificado\":1},\"foto_presentacion\":{\"Modificado\":\"\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00040-84\"},\"mostrar_ecommerce\":{\"Modificado\":1},\"foto_presentacion\":{\"Modificado\":\"\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00041-30\"},\"foto_presentacion\":{\"Modificado\":\"\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00042-33\"},\"foto_presentacion\":{\"Modificado\":\"\"}}],\"materias_primas\":[[]]}}', '2026-08-11 13:05:38', 1),
(2378, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26222-00042-33)', '181.208.252.213', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-08-11_13_05_54.png?v=2026-08-11_13_05_54\"}}', '2026-08-11 13:05:54', 1),
(2379, 'V30485684', 4, 'Éxito', 'Actualizar', '181.208.252.213', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-98\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-09\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-77\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00040-19\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00041-44\"},\"mostrar_ecommerce\":{\"Modificado\":1}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00042-60\"},\"foto_presentacion\":{\"Modificado\":\"\"}}],\"materias_primas\":[[]]}}', '2026-08-11 13:06:02', 1),
(2380, 'V30485684', 4, 'Éxito', 'Actualizar', '181.208.252.213', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-85\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-61\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-26\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00040-64\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00041-74\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00042-30\"}}],\"materias_primas\":[[]]}}', '2026-08-11 13:11:43', 1),
(2381, 'V30485684', 4, 'Éxito', 'Actualizar', '181.208.252.213', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-27\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-10\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-14\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00040-16\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00041-48\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00042-85\"}}],\"materias_primas\":[[]]}}', '2026-08-11 13:11:58', 1),
(2382, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 13:18:52\"}}', '2026-08-11 13:13:52', 1),
(2383, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 13:30:45\"}}', '2026-08-11 13:25:46', 1),
(2384, 'V30485684', 4, 'Éxito', 'Actualizar', '190.97.229.57', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-67\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-54\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-27\"}},[],{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00041-30\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00042-32\"}}],\"materias_primas\":[[]]}}', '2026-08-11 13:26:08', 1),
(2385, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 13:45:19\"}}', '2026-08-11 13:40:19', 1),
(2386, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 13:55:26\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-11 13:50:26', 1),
(2387, 'V30485684', 4, 'Éxito', 'Actualizar', '181.208.252.213', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-12\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-22\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-89\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00040-98\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00041-07\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00042-17\"}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Modificado\":212}}]}}', '2026-08-11 13:56:49', 1),
(2388, 'V30485684', 4, 'Éxito', 'Actualizar', '181.208.252.213', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-68\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-34\"},\"id_presentacion\":{\"Modificado\":\"PRES-26159-00003-24\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-45\"},\"id_presentacion\":{\"Modificado\":\"PRES-26177-00001-19\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00040-23\"},\"id_presentacion\":{\"Modificado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Modificado\":0}},{\"id_presentacion_producto\":{\"Eliminado\":\"PRPR-26222-00041-07\"},\"id_producto\":{\"Eliminado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Eliminado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Eliminado\":1},\"foto_presentacion\":{\"Eliminado\":\"\"},\"status\":{\"Eliminado\":1}},{\"id_presentacion_producto\":{\"Eliminado\":\"PRPR-26222-00042-17\"},\"id_producto\":{\"Eliminado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Eliminado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Eliminado\":0},\"foto_presentacion\":{\"Eliminado\":\"\"},\"status\":{\"Eliminado\":1}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Modificado\":213}}]}}', '2026-08-11 13:56:57', 1),
(2389, 'V30485684', 4, 'Éxito', 'Actualizar', '181.208.252.213', '{\"detallesExtra\":{\"presentaciones\":[{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00037-00\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00038-12\"},\"id_presentacion\":{\"Modificado\":\"PRES-26159-00002-78\"},\"mostrar_ecommerce\":{\"Modificado\":0}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00039-70\"},\"id_presentacion\":{\"Modificado\":\"PRES-26159-00003-24\"}},{\"id_presentacion_producto\":{\"Modificado\":\"PRPR-26222-00040-06\"},\"id_presentacion\":{\"Modificado\":\"PRES-26177-00001-19\"},\"mostrar_ecommerce\":{\"Modificado\":1}},{\"id_presentacion_producto\":{\"Registrado\":\"PRPR-26222-00041-20\"},\"id_producto\":{\"Registrado\":\"PROD-26222-00004-43\"},\"id_presentacion\":{\"Registrado\":\"PRES-26177-00002-40\"},\"mostrar_ecommerce\":{\"Registrado\":0},\"foto_presentacion\":{\"Registrado\":\"\"},\"status\":{\"Registrado\":1}}],\"materias_primas\":[{\"id_materia_prima_producto\":{\"Modificado\":214}}]}}', '2026-08-11 13:57:05', 1),
(2390, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26222-00041-20)', '181.208.252.213', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-08-11_13_57_32.png?v=2026-08-11_13_57_32\"}}', '2026-08-11 13:57:32', 1),
(2391, 'V30485684', 305, 'Éxito', 'Actualizar foto de la presentacion (PRPR-26222-00039-70)', '181.208.252.213', '{\"foto_presentacion\":{\"Modificado\":\"presentaciones_productos_2026-08-11_13_57_40.png?v=2026-08-11_13_57_40\"}}', '2026-08-11 13:57:40', 1),
(2392, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 14:14:01\"}}', '2026-08-11 14:09:01', 1),
(2393, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 14:15:35\"}}', '2026-08-11 14:10:35', 1),
(2394, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-11 15:28:51\"}}', '2026-08-11 15:23:51', 1),
(2395, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-12 11:05:48\"}}', '2026-08-12 11:00:48', 1),
(2396, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 11:03:33\"}}', '2026-08-14 10:58:33', 1),
(2397, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 11:04:40\"}}', '2026-08-14 10:59:40', 1),
(2398, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 11:07:14\"}}', '2026-08-14 11:02:14', 1),
(2399, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 11:18:46\"}}', '2026-08-14 11:13:47', 1),
(2400, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 11:25:05\"}}', '2026-08-14 11:20:05', 1),
(2401, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 12:08:33\"}}', '2026-08-14 12:03:33', 1);
INSERT INTO `bitacora` (`id_bitacora`, `cedula_usuario`, `id_modulo`, `resultado_bitacora`, `accion`, `ip_dispositivo`, `cambios_efectuados`, `fecha_bitacora`, `status`) VALUES
(2402, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 12:23:58\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-14 12:18:58', 1),
(2403, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 12:42:03\"}}', '2026-08-14 12:37:03', 1),
(2404, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 12:59:48\"}}', '2026-08-14 12:54:48', 1),
(2405, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 13:15:12\"}}', '2026-08-14 13:10:12', 1),
(2406, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 13:40:53\"}}', '2026-08-14 13:35:53', 1),
(2407, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '181.208.252.213', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 13:56:25\"},\"intentos_inicio_sesion_fallidos_usuario\":{\"Modificado\":0}}', '2026-08-14 13:51:25', 1),
(2408, 'V30485684', 9, 'Éxito', 'Iniciar sesión', '190.97.229.57', '{\"ultimo_acceso_usuario\":{\"Modificado\":\"2026-08-14 14:04:41\"}}', '2026-08-14 13:59:41', 1);

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
(305, 'presentaciones_productos', 1),
(306, 'mamm', 0),
(307, 'chatbot', 1),
(308, 'preguntas-seguridad', 1),
(309, 'ordenesEntregasPresupuestos', 1);

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
(398, 'V30485684', 1, 2, 0, 'Permisos', 'Un permiso ha sido actualizado.', '2026-08-08 13:49:57', 0),
(399, 'V30485684', 1, 2, 0, 'Permisos', 'Un permiso ha sido actualizado.', '2026-08-08 13:50:09', 0),
(400, 'V30485684', 1, 2, 0, 'Permisos', 'Un permiso ha sido registrado en el sistema.', '2026-08-08 13:50:22', 0),
(401, 'V30485684', 1, 2, 0, 'Permisos', 'Un permiso ha sido eliminado del sistema.', '2026-08-08 13:50:26', 0),
(402, 'V30485681', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-08-10 17:44:31', 1),
(403, 'V30485684', 1, 2, 0, 'Pedido nuevo', 'Acaba de llegar un pedido nuevo', '2026-08-10 17:44:31', 0);

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
(653, 'imprimir pedidos', 1),
(654, 'registrar cargas o descargas de productos', 1),
(655, 'ver historial de e/s de los productos', 1),
(656, 'registrar cargas o descargas de materias primas', 1),
(657, 'ver historial de e/s de las materias primas', 1),
(658, 'ver permisos generales', 1),
(659, 'ver permisos especiales', 1),
(660, 'actualizar permisos', 1),
(662, 'asignar repartidores a pedidos', 1),
(663, 'ver detalles de los productos', 1),
(664, 'ver historial de cambio de las divisas', 1),
(665, 'listar permisos', 1),
(666, 'ver chatbot', 1),
(667, 'enviar mensaje chatbot', 1),
(668, 'imprimir reportes de anomalias de productos', 1),
(669, 'imprimir reportes de anomalias de materias primas', 1),
(670, 'anular', 1),
(671, 'despachar orden', 1),
(672, 'agregar pago', 1),
(673, 'permisox', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preguntas_seguridad`
--

CREATE TABLE `preguntas_seguridad` (
  `id_pregunta` varchar(20) NOT NULL,
  `texto_pregunta` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `preguntas_seguridad`
--

INSERT INTO `preguntas_seguridad` (`id_pregunta`, `texto_pregunta`, `status`) VALUES
('PREG-26210-00001-26', '¿Cuál es tu comida favorita?', 1),
('PREG-26210-00002-21', '¿En qué calle vivías cuando tenías diez años?', 1),
('PREG-26210-00003-77', '¿Cómo se llamaba tu primera mascota?', 1),
('PREG-26210-00004-61', '¿Cuál es el segundo nombre de tu abuelo materno?', 1),
('PREG-26210-00005-63', '¿Cómo se llamaba tu primer maestro de escuela?', 1),
('PREG-26210-00006-60', '¿Cuál era tu apodo favorito durante la infancia?', 1),
('PREG-26210-00007-27', '¿Cuál fue el primer modelo de auto que tuviste?', 1),
('PREG-26210-00008-61', '¿A qué ciudad viajaste en tu primer vuelo en avión?', 1),
('PREG-26210-00009-75', '¿Cuál es el título de tu libro favorito de la infancia?', 1),
('PREG-26210-00010-46', '¿Cuál fue el primer concierto de música al que asististe?', 1),
('PREG-26210-00011-43', '¿En qué hotel te hospedaste durante tus vacaciones favoritas?', 1),
('PREG-26210-00012-08', '¿Cómo se llamaba la primera empresa donde trabajaste?', 1),
('PREG-26210-00013-26', '¿Cuál era el nombre de la mascota de tu universidad?', 1),
('PREG-26210-00014-96', '¿En qué ciudad se conocieron tus padres?', 1),
('PREG-26210-00015-02', '¿Cuál fue el primer país extranjero que visitaste?', 1),
('PREG-26210-00016-12', '¿Cómo se llamaba el hospital donde naciste?', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preguntas_seguridad_usuarios`
--

CREATE TABLE `preguntas_seguridad_usuarios` (
  `id_pregunta_usuario` varchar(20) NOT NULL,
  `id_pregunta` varchar(20) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `respuesta_pregunta` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `preguntas_seguridad_usuarios`
--

INSERT INTO `preguntas_seguridad_usuarios` (`id_pregunta_usuario`, `id_pregunta`, `cedula_usuario`, `respuesta_pregunta`, `status`) VALUES
('PRUS-26213-00001-85', 'PREG-26210-00001-26', 'V30485684', '$2y$10$oSAFQLHBQ.f0yXrP2EHvYOWmXbFQEK6ddsUSES5kZuRfvHTGrvkLO', 1),
('PRUS-26213-00002-33', 'PREG-26210-00003-77', 'V30485684', '$2y$10$ESrPpMPFFnpSTAKCOiAqVOApkQ7In.nwsj4H8oVj1nk5GTb96i7OW', 1),
('PRUS-26213-00003-13', 'PREG-26210-00015-02', 'V30485684', '$2y$10$CJ4N3mC.78fqwCK4eOvaFOK5MqaS5cIeAldIZ4N8NHnnwOrZGunW6', 1),
('PRUS-26213-00004-32', 'PREG-26210-00013-26', 'V30485684', '$2y$10$AAs.exVU1/hT.kK/2wYnRumBj0/81k6la0doYE7P81IEKCENLZ2Yi', 1),
('PRUS-26213-00005-91', 'PREG-26210-00012-08', 'V30485684', '$2y$10$nEuTtqbC4VHg/Fq7MjHDJuo8pAjGVNj.MQ1/DvKaH5lVu6FeminVe', 1),
('PRUS-26213-00006-15', 'PREG-26210-00010-46', 'V30485684', '$2y$10$.pudhcTVmpJqP56lu1guxOCQpi8czdVNALQv04oklr6X3byw/GHly', 1),
('PRUS-26221-00001-41', 'PREG-26210-00001-26', 'V30485688', '$2y$10$kkIvKOeLqnsJjPepiPbPaObftz5zu.Utme0AAci7k9jqbCwNdxb7.', 1),
('PRUS-26221-00002-20', 'PREG-26210-00002-21', 'V30485688', '$2y$10$WXVuBpVC/aD2eJjIjiGOzu5rMVQU9i.LDG77RAib4sAlNRE5hLgDG', 1),
('PRUS-26221-00003-40', 'PREG-26210-00003-77', 'V30485688', '$2y$10$xplVAHINlxboKlbtghJQFOGG/93Wa0ZmgDSIXQe7ymr.mRtfNYl8i', 1),
('PRUS-26221-00004-31', 'PREG-26210-00015-02', 'V30485688', '$2y$10$97U9ZxMj0mPK/rtsBgTM.e6A33jx6DFV./yd6R5aHTD9pVEpI4CEa', 1),
('PRUS-26221-00005-05', 'PREG-26210-00014-96', 'V30485688', '$2y$10$.vWhvgbmkDn0wSvhLpu5p.YRkVpg.Qy1r5sEV4REGFFmhSaPvJJoe', 1),
('PRUS-26221-00006-70', 'PREG-26210-00012-08', 'V30485688', '$2y$10$MSyyqq8/wDJedr3JCR/GeuXLtDX2fE4z/XR1LEAV4rWAdodyVmRYG', 1),
('PRUS-26221-00007-22', 'PREG-26210-00001-26', 'V30485681', '$2y$10$wU8cYibX8NLN27pihZMW4Obl1MtwCq60TaylCpisI7k3graGFoaHu', 1),
('PRUS-26221-00008-99', 'PREG-26210-00002-21', 'V30485681', '$2y$10$oCY9ov16IiozchUkb/jkhOZnuIwVC4HRPtkcjLoGoBQxkUDJW2auy', 1),
('PRUS-26221-00009-61', 'PREG-26210-00003-77', 'V30485681', '$2y$10$EJGejQQ/q8/bHep25huPnO9AKIXVy9q0Pkt/WV/rah7C4rBlUwKoq', 1),
('PRUS-26221-00010-42', 'PREG-26210-00014-96', 'V30485681', '$2y$10$F0WYELHGHtHBGMWGEJvE.ehIVVz5VMtDaa1x38KCl6Eh.Yx84sf1C', 1),
('PRUS-26221-00011-68', 'PREG-26210-00012-08', 'V30485681', '$2y$10$b3u2pHbQ24nWvTq8Tg8DXep2j6CQYU1ICmfpfrMmJXVP80Z2u/r7i', 1),
('PRUS-26221-00012-42', 'PREG-26210-00011-43', 'V30485681', '$2y$10$caw/tqyKBEMy7cZcRrMcMePBXzmAwEgzkbctT.h3Ax9ilGYtgfdv6', 1);

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
(3, 'CLIENTES', 1);

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
-- Estructura de tabla para la tabla `tokens_usuarios`
--

CREATE TABLE `tokens_usuarios` (
  `id_token_usuario` varchar(20) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `tipo_token` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `vencimiento_token` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
('V30485681', 1, 'Anderson', 'Freitez', 'Ander1236', '$2y$10$6ljN.VrGlB6mn4wLkHfDaOXke8z9135c.OYp5R.mHKk/y0bWnjNde', '04169484643', 'andersonfreitez66@gmail.com', 'usuarios_2026_08_10_16_58_55_14.jpg', 'BARQUISIMETO', '2026-08-11 04:58:55', 0, 1),
('V30485684', 1, 'Anderson', 'Freitez', 'Ander123', '$2y$10$c6nJB8cfBNeptJq45jPrMOK.jPOGFOEq7rZAFHyHkg3k7WzQMQXC6', '04169484649', 'andersonfreitez6@gmail.com', '', 'SANARE', '2026-08-14 14:12:56', 0, 1),
('V30485688', 1, 'Andersoa', 'Freitez', 'Ander1239', '$2y$10$petVY5x/7OGMh1gk/PuBt.R2SxsI20QXItsUrfqIG.XrEk4aScQ5m', '04169484640', 'andersonfremitez6@gmail.com', 'usuarios_2026_08_10_16_45_15_60.jpg?v=2026-08-10_17_00_26', 'BARQUISIMETO', '2026-08-11 04:45:15', 0, 0);

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
-- Indices de la tabla `preguntas_seguridad`
--
ALTER TABLE `preguntas_seguridad`
  ADD PRIMARY KEY (`id_pregunta`);

--
-- Indices de la tabla `preguntas_seguridad_usuarios`
--
ALTER TABLE `preguntas_seguridad_usuarios`
  ADD PRIMARY KEY (`id_pregunta_usuario`),
  ADD KEY `id_pregunta_preguntas_seguridad_usuarios_fk` (`id_pregunta`),
  ADD KEY `cedula_usuario_preguntas_seguridad_usuarios_fk` (`cedula_usuario`);

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
-- Indices de la tabla `tokens_usuarios`
--
ALTER TABLE `tokens_usuarios`
  ADD PRIMARY KEY (`id_token_usuario`),
  ADD KEY `cedula_usuario_tokens_usuarios` (`cedula_usuario`);

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
  MODIFY `id_acceso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1070;

--
-- AUTO_INCREMENT de la tabla `acciones_resagadas_usuarios`
--
ALTER TABLE `acciones_resagadas_usuarios`
  MODIFY `id_accion_resagada_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1281;

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id_bitacora` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2409;

--
-- AUTO_INCREMENT de la tabla `iconos_notificaciones`
--
ALTER TABLE `iconos_notificaciones`
  MODIFY `id_icono_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=311;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=404;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=674;

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
-- Filtros para la tabla `preguntas_seguridad_usuarios`
--
ALTER TABLE `preguntas_seguridad_usuarios`
  ADD CONSTRAINT `cedula_usuario_preguntas_seguridad_usuarios_fk` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_pregunta_preguntas_seguridad_usuarios_fk` FOREIGN KEY (`id_pregunta`) REFERENCES `preguntas_seguridad` (`id_pregunta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `prompts_usuarios`
--
ALTER TABLE `prompts_usuarios`
  ADD CONSTRAINT `cedula_usuario_prompts_usuarios_fk` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tokens_usuarios`
--
ALTER TABLE `tokens_usuarios`
  ADD CONSTRAINT `cedula_usuario_tokens_usuarios` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `id_rol_usuarios_fk` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
