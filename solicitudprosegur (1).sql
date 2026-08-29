-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-08-2026 a las 17:42:39
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `solicitudprosegur`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alertas_solicitudes`
--

CREATE TABLE `alertas_solicitudes` (
  `alerta_id` int(11) NOT NULL,
  `solicitud_id` int(11) NOT NULL,
  `tipo_alerta` varchar(50) NOT NULL DEFAULT 'vencimiento',
  `fecha_envio` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(20) NOT NULL DEFAULT 'enviada',
  `mensaje_error` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=sjis COLLATE=sjis_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `areas`
--

CREATE TABLE `areas` (
  `id_area` int(11) NOT NULL,
  `nombre_area` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `areas`
--

INSERT INTO `areas` (`id_area`, `nombre_area`, `correo`, `estado`, `fecha_creacion`) VALUES
(1, 'Administrativo', 'sistemas@prosegur.com', 'activo', '2026-08-12 13:26:58'),
(2, 'Almacen Inees', 'mantenimiento@prosegur.com', 'activo', '2026-08-12 13:26:58'),
(3, 'Laboratorio Inees', 'operaciones@prosegur.com', 'activo', '2026-08-12 13:26:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_solicitud`
--

CREATE TABLE `detalle_solicitud` (
  `detalle_id` int(11) NOT NULL,
  `solicitud_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad_solicitada` int(11) NOT NULL DEFAULT 1,
  `cantidad_aprobada` int(11) DEFAULT NULL,
  `cantidad_entregada` int(11) NOT NULL DEFAULT 0,
  `observaciones` mediumtext DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `detalle_solicitud`
--

INSERT INTO `detalle_solicitud` (`detalle_id`, `solicitud_id`, `producto_id`, `cantidad_solicitada`, `cantidad_aprobada`, `cantidad_entregada`, `observaciones`, `fecha_creacion`) VALUES
(11, 29, 453, 4, NULL, 0, '', '2026-08-26 11:33:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `inventario_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad_disponible` int(11) NOT NULL DEFAULT 0,
  `ubicacion` varchar(150) DEFAULT NULL,
  `estado` enum('disponible','stock_bajo','agotado','inactivo') NOT NULL DEFAULT 'disponible',
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`inventario_id`, `producto_id`, `cantidad_disponible`, `ubicacion`, `estado`, `fecha_actualizacion`) VALUES
(1, 1, 20, 'Bodega principal', 'disponible', '2026-08-12 13:28:30'),
(2, 2, 2, 'Bodega principal', 'disponible', '2026-08-19 12:36:52'),
(3, 3, 2, 'Bodega principal', 'stock_bajo', '2026-08-15 09:20:37'),
(4, 4, 50, 'Bodega principal', 'stock_bajo', '2026-08-15 09:31:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login`
--

CREATE TABLE `login` (
  `id_login` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `ip` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `login`
--

INSERT INTO `login` (`id_login`, `usuario_id`, `fecha`, `hora`, `usuario`, `ip`) VALUES
(1, 1, '2026-08-12', '11:14:31', 'Administrador', '::1'),
(2, 1, '2026-08-12', '11:26:25', 'Administrador', '::1'),
(3, 1, '2026-08-14', '10:53:15', 'Administrador', '::1'),
(4, 1, '2026-08-14', '11:02:01', 'Administrador', '::1'),
(5, 1, '2026-08-14', '11:35:13', 'Administrador', '::1'),
(6, 6, '2026-08-14', '11:51:01', 'Cliente Prosegur', '::1'),
(7, 1, '2026-08-14', '15:16:12', 'Administrador', '::1'),
(8, 1, '2026-08-15', '09:19:50', 'Administrador', '::1'),
(9, 1, '2026-08-15', '09:52:39', 'Administrador', '::1'),
(10, 6, '2026-08-15', '10:09:08', 'Cliente Prosegur', '::1'),
(11, 1, '2026-08-18', '10:11:50', 'Administrador', '::1'),
(12, 7, '2026-08-19', '15:13:03', 'Técnico de Prueba', '::1'),
(13, 7, '2026-08-19', '15:42:53', 'Técnico de Prueba', '::1'),
(14, 7, '2026-08-20', '08:23:55', 'Técnico de Prueba', '::1'),
(15, 6, '2026-08-20', '08:27:12', 'Cliente Prosegur', '::1'),
(16, 1, '2026-08-20', '08:29:31', 'Administrador', '::1'),
(17, 7, '2026-08-20', '08:34:23', 'Técnico de Prueba', '::1'),
(18, 7, '2026-08-20', '12:03:39', 'Técnico de Prueba', '::1'),
(19, 7, '2026-08-20', '12:32:42', 'Técnico de Prueba', '::1'),
(20, 6, '2026-08-20', '12:48:28', 'Cliente Prosegur', '::1'),
(21, 1, '2026-08-20', '12:48:58', 'Administrador', '::1'),
(22, 1, '2026-08-21', '09:12:01', 'Administrador', '::1'),
(23, 6, '2026-08-21', '09:12:46', 'Cliente Prosegur', '::1'),
(24, 1, '2026-08-21', '11:30:19', 'Administrador', '::1'),
(25, 8, '2026-08-21', '14:37:19', 'Juan Esteban', '::1'),
(26, 1, '2026-08-21', '15:00:49', 'Administrador', '::1'),
(27, 9, '2026-08-22', '07:07:29', 'Yenni Vega', '::1'),
(28, 1, '2026-08-22', '07:12:06', 'Administrador', '::1'),
(29, 1, '2026-08-22', '07:28:39', 'Administrador', '::1'),
(30, 6, '2026-08-22', '07:30:03', 'Cliente Prosegur', '::1'),
(31, 9, '2026-08-22', '07:39:15', 'Yenni Vega', '::1'),
(32, 1, '2026-08-22', '07:39:28', 'Administrador', '::1'),
(33, 1, '2026-08-22', '09:06:15', 'Administrador', '::1'),
(34, 6, '2026-08-24', '08:17:45', 'Cliente Prosegur', '::1'),
(35, 1, '2026-08-24', '08:27:27', 'Administrador', '::1'),
(36, 10, '2026-08-24', '12:18:25', 'Samuel bedoya', '::1'),
(37, 6, '2026-08-24', '12:27:51', 'Cliente Prosegur', '::1'),
(38, 1, '2026-08-24', '12:44:23', 'Administrador', '::1'),
(39, 1, '2026-08-24', '13:25:51', 'Administrador', '::1'),
(40, 1, '2026-08-25', '08:01:59', 'Administrador', '::1'),
(41, 16, '2026-08-25', '08:02:33', 'Daniel Osorio', '::1'),
(42, 1, '2026-08-25', '08:04:03', 'Administrador', '::1'),
(43, 1, '2026-08-25', '09:28:41', 'Administrador', '::1'),
(44, 1, '2026-08-26', '12:00:38', 'Administrador', '::1'),
(45, 1, '2026-08-26', '12:40:59', 'Administrador', '::1'),
(46, 1, '2026-08-27', '08:18:31', 'Administrador', '::1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_inventario`
--

CREATE TABLE `movimientos_inventario` (
  `movimiento_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo_movimiento` enum('entrada','salida','ajuste') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `fecha_movimiento` datetime NOT NULL DEFAULT current_timestamp(),
  `observaciones` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `notificacion_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `mensaje` mediumtext NOT NULL,
  `referencia_id` int(11) DEFAULT NULL,
  `referencia_tipo` varchar(50) DEFAULT NULL,
  `leida` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_lectura` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`notificacion_id`, `usuario_id`, `tipo`, `titulo`, `mensaje`, `referencia_id`, `referencia_tipo`, `leida`, `fecha_creacion`, `fecha_lectura`) VALUES
(1, 1, 'alerta', '', '⚠️ Stock crítico: El producto \'Repuesto de Prueba - Teclado Matricial\' tiene solo 3 unidades disponibles.', NULL, NULL, 1, '2026-08-13 12:38:16', '2026-08-18 15:25:13'),
(2, 1, 'alerta', '', '⚠️ Stock crítico: El producto \'Repuesto de Prueba - Teclado Matricial\' tiene solo 3 unidades disponibles.', NULL, NULL, 1, '2026-08-13 13:20:30', '2026-08-18 15:25:12'),
(3, 1, '', 'Prueba de Alerta', 'Esta es una prueba de la campanita y el correo al tiempo.', NULL, NULL, 1, '2026-08-13 14:43:22', '2026-08-18 15:35:59'),
(4, 1, '', 'Prueba de Alerta', 'Esta es una prueba de la campanita y el correo al tiempo.', NULL, NULL, 1, '2026-08-13 14:54:19', '2026-08-18 15:35:57'),
(5, 1, '', 'Prueba de Alerta', 'Esta es una prueba de la campanita y el correo al tiempo.', NULL, NULL, 1, '2026-08-13 14:54:22', '2026-08-18 15:35:59'),
(6, 1, '', 'Prueba de Alerta', 'Esta es una prueba de la campanita y el correo al tiempo.', NULL, NULL, 1, '2026-08-13 15:03:00', '2026-08-18 15:35:55'),
(7, 1, 'prueba', 'Prueba de Alerta', 'Esta es una prueba de la campanita y el correo al tiempo.', NULL, NULL, 1, '2026-08-14 08:27:18', '2026-08-18 15:25:11'),
(8, 1, 'alerta', 'Notificación del sistema', '⚠️ Stock crítico: El producto \'Repuesto de Prueba - Teclado Matricial\' tiene solo 3 unidades disponibles.', NULL, NULL, 1, '2026-08-14 08:54:19', '2026-08-18 15:35:58'),
(9, 6, 'prueba', 'Prueba de Alerta', 'Esta es una prueba de la campanita y el correo al tiempo.', NULL, NULL, 1, '2026-08-14 15:07:15', '2026-08-20 08:28:22'),
(10, 1, 'alerta', 'Notificación del sistema', '⚠️ Stock crítico: El producto \'Repuesto de Prueba - Teclado Matricial\' tiene solo 3 unidades disponibles.', NULL, NULL, 1, '2026-08-15 09:06:35', '2026-08-18 15:25:10'),
(11, 6, 'prueba', 'Prueba de Alerta', 'Esta es una prueba de la campanita y el correo al tiempo.', NULL, NULL, 1, '2026-08-15 09:06:35', '2026-08-20 08:28:21'),
(12, 1, 'stock_bajo', 'Stock bajo: Monitor', '⚠️ Stock crítico: El producto \'Monitor\' tiene solo 2 unidades disponibles.', NULL, NULL, 1, '2026-08-15 09:20:39', '2026-08-18 15:35:54'),
(13, 1, 'stock_bajo', 'Stock bajo: Monitor', '⚠️ Stock crítico: El producto \'Monitor\' tiene solo 2 unidades disponibles.', NULL, NULL, 1, '2026-08-15 09:20:41', '2026-08-18 15:25:08'),
(14, 6, 'prueba', 'Prueba de Alerta', 'Esta es una prueba de la campanita y el correo al tiempo.', NULL, NULL, 1, '2026-08-15 09:51:04', '2026-08-18 14:47:17'),
(15, 1, 'stock_bajo', 'Stock bajo: Mouse', '⚠️ Stock crítico: El producto \'Mouse\' tiene solo 2 unidades disponibles.', NULL, NULL, 1, '2026-08-19 12:36:54', '2026-08-19 15:18:47'),
(16, 1, 'stock_bajo', 'Stock bajo: Mouse', '⚠️ Stock crítico: El producto \'Mouse\' tiene solo 2 unidades disponibles.', NULL, NULL, 1, '2026-08-19 12:36:55', '2026-08-19 15:18:46'),
(17, 1, 'prueba', 'Prueba de Notificación Creada', 'Esta es una prueba para verificar la campanita y el envío de correo al mismo tiempo.', NULL, NULL, 1, '2026-08-26 09:00:48', '2026-08-26 11:40:04'),
(18, 1, 'prueba', 'Prueba de Notificación Creada', 'Esta es una prueba para verificar la campanita y el envío de correo al mismo tiempo.', NULL, NULL, 1, '2026-08-26 09:23:24', '2026-08-26 11:55:43'),
(19, 1, 'prueba', 'Prueba de Notificación Creada', 'Esta es una prueba para verificar la campanita y el envío de correo al mismo tiempo.', NULL, NULL, 1, '2026-08-26 09:24:36', '2026-08-26 11:56:50'),
(20, 1, 'solicitud_almacen', 'Solicitud a Almacén: Solicitud de cmaras para validadores', 'Enviada a almacen@inees.com por Administrador. Detalle: Correo de Prueba hacer caso omiso', NULL, NULL, 1, '2026-08-26 12:04:24', '2026-08-26 12:44:00'),
(21, 1, 'solicitud_almacen', 'Solicitud a Almacén: Arreglo impresoras', 'Enviada a juanes01vargas@gmail.com por Administrador. Detalle: Detallando ando', NULL, NULL, 1, '2026-08-26 12:13:04', '2026-08-26 12:43:59'),
(22, 1, 'solicitud_almacen', 'Solicitud a Almacén: Actualizacion de Stock', 'Enviada a juanes01vargas@gmail.com por Administrador. Detalle: solicitud para revissar el stock de manera inmediata', NULL, NULL, 1, '2026-08-26 12:16:10', '2026-08-26 12:43:59'),
(23, 1, 'solicitud_almacen', 'Solicitud a Almacén: Actualizacion de Stock', 'Enviada a juanes01vargas@gmail.com por Administrador. Detalle: no poner cuidado', NULL, NULL, 1, '2026-08-26 12:25:54', '2026-08-26 12:43:58'),
(24, 1, 'solicitud_almacen', 'Solicitud a Almacén: sdasdasdsa', 'Enviada a juanes01vargas@gmail.com por Administrador. Detalle: sdasdsasa', NULL, NULL, 1, '2026-08-26 12:27:23', '2026-08-26 12:43:58'),
(25, 1, 'solicitud_almacen', 'Solicitud a Almacén: no se no poner cuidado', 'Enviada a juanes01vargas@gmail.com por Administrador. Detalle: nos e', NULL, NULL, 1, '2026-08-26 12:30:43', '2026-08-26 12:43:58'),
(26, 1, 'solicitud_almacen', 'Solicitud a Almacén: no se no poner cuidado', 'Enviada a juanes01vargas@gmail.com por Administrador. Detalle: adadsasa', NULL, NULL, 1, '2026-08-26 12:41:13', '2026-08-26 12:43:57'),
(27, 1, 'solicitud_almacen', 'Solicitud a Almacén: sadadsa', 'Enviada a almacen@inees.co por Administrador. Detalle: sadadsa', NULL, NULL, 1, '2026-08-26 12:43:23', '2026-08-26 12:43:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset`
--

CREATE TABLE `password_reset` (
  `id` int(11) NOT NULL,
  `usuario_email` varchar(100) NOT NULL,
  `codigo_hash` varchar(255) NOT NULL,
  `expira_en` datetime NOT NULL,
  `usado` tinyint(1) DEFAULT 0,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `password_reset`
--

INSERT INTO `password_reset` (`id`, `usuario_email`, `codigo_hash`, `expira_en`, `usado`, `fecha_creacion`) VALUES
(1, 'tecnico.prueba@inees.co', '$2y$10$j21n6o85/KN0YRfm.aCj0uAAMnMaYJIgtfhquMMiKNhtdiXiHQPda', '2026-08-19 15:46:51', 1, '2026-08-19 20:31:51'),
(2, 'Juanes01vargas@gmail.com', '$2y$10$prbMaKM/pY8e2JRw0YAH4.HIfU3nm9TnyAvN8qTIorNT5fMwWSHqu', '2026-08-19 15:52:21', 1, '2026-08-19 20:37:21'),
(3, 'Juanes01vargas@gmail.com', '$2y$10$EOJsYXGfskaOO2JOK6aSHOPVnGbqWlkM3uSwduy.DPPo/H3q3v25S', '2026-08-19 15:53:40', 1, '2026-08-19 20:38:40'),
(4, 'Juanes01vargas@gmail.com', '$2y$10$pVYBPzO70EwK8321xLuvY.ycbKmoh8fyZSJHXwuSnJ/ENYr6g/Puu', '2026-08-19 15:56:02', 1, '2026-08-19 20:41:02'),
(5, 'Juanes01vargas@gmail.com', '$2y$10$5yOnUoC0TQ/bCCPs7W/U3OQ34lMxk1Lb8GNa7934cAVuFbRE.6lVm', '2026-08-19 15:56:13', 1, '2026-08-19 20:41:13'),
(6, 'juanes01vargas@gmail.com', '$2y$10$mE1oYFVIrG0W1IIY3tvucum2F0wt1uR69ySEGMYsrnl/1fyuHb8My', '2026-08-20 08:36:39', 1, '2026-08-20 13:21:39'),
(7, 'juanes01vargas@gmail.com', '$2y$10$eHKQTekcEEkuMqpH.HnOM.8B.pXTNG9mJZ7pxECb/Z1PkZZeIVGyy', '2026-08-20 08:48:20', 1, '2026-08-20 13:33:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `producto_id` int(11) NOT NULL,
  `nombre_producto` varchar(150) NOT NULL,
  `descripcion` mediumtext DEFAULT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `unidad_medida` varchar(50) NOT NULL DEFAULT 'unidad',
  `stock_minimo` int(11) NOT NULL DEFAULT 0,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`producto_id`, `nombre_producto`, `descripcion`, `categoria`, `unidad_medida`, `stock_minimo`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Teclado', 'Teclado USB para equipo de escritorio', 'Informática', 'unidad', 5, 'activo', '2026-08-12 13:27:27', NULL),
(2, 'Mouse', 'Mouse USB para equipo de escritorio', 'Informática', 'unidad', 5, 'activo', '2026-08-12 13:27:27', NULL),
(3, 'Monitor', 'Monitor para estación de trabajo', 'Informática', 'unidad', 5, 'activo', '2026-08-12 13:27:27', NULL),
(4, 'Cable UTP', 'Cable de red categoría 6', 'Redes', 'metro', 20, 'activo', '2026-08-12 13:27:27', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_permisos`
--

CREATE TABLE `rol_permisos` (
  `rol_id` int(11) NOT NULL,
  `ruta_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `rol_permisos`
--

INSERT INTO `rol_permisos` (`rol_id`, `ruta_id`) VALUES
(1, 8),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 8),
(2, 9),
(3, 2),
(3, 3),
(3, 5),
(3, 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rutas`
--

CREATE TABLE `rutas` (
  `id_ruta` int(11) NOT NULL,
  `nombre_ruta` varchar(50) NOT NULL,
  `controlador_ruta` varchar(255) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `rutas`
--

INSERT INTO `rutas` (`id_ruta`, `nombre_ruta`, `controlador_ruta`, `estado`) VALUES
(1, 'login', 'app/controllers/login/loginControlador.php', 'activo'),
(2, 'inicio', 'app/controllers/inicio/inicioControlador.php', 'activo'),
(3, 'solicitudes', 'app/controllers/solicitudes/solicitudesControlador.php', 'activo'),
(4, 'inventario', 'app/controllers/inventario/inventarioControlador.php', 'activo'),
(5, 'historial', 'app/controllers/solicitudes/solicitudesControlador.php', 'activo'),
(6, 'dashboard', 'app/controllers/dashboard/dashboardControlador.php', 'activo'),
(8, 'notificacion', 'app/controllers/notificacion/NotificacionController.php', 'activo'),
(9, 'inventarioVer', 'app/controllers/inventario/inventarioControlador.php', 'activo'),
(10, 'solicitarCodigo', 'app/controllers/login/solicitarCodigoControlador.php', 'activo'),
(11, 'resetPassword', 'app/controllers/login/resetPasswordControlador.php', 'activo'),
(12, 'enviarCodigo', 'app/controllers/login/solicitarCodigoControlador.php', 'activo'),
(13, 'mensajeEnviado', 'app/controllers/login/solicitarCodigoControlador.php', 'activo'),
(14, 'cambiarPassword', 'app/controllers/login/cambiarPasswordControlador.php', 'activo'),
(15, 'registro', 'app/controllers/login/registroControlador.php', 'activo'),
(16, 'solicitudesRegistro', 'app/controllers/usuario/solicitudesRegistroControlador.php', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `solicitud_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `area_id` int(11) DEFAULT NULL,
  `fecha_solicitud` datetime NOT NULL DEFAULT current_timestamp(),
  `asunto` varchar(200) NOT NULL,
  `descripcion` mediumtext NOT NULL,
  `prioridad` enum('sin_asignar','baja','media','alta','urgente') NOT NULL DEFAULT 'sin_asignar',
  `fecha_limite` datetime DEFAULT NULL,
  `estado` enum('pendiente','en_revision','aprobada','rechazada','en_proceso','atendida','cancelada') NOT NULL DEFAULT 'pendiente',
  `observaciones` mediumtext DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  `atendido_por` int(11) DEFAULT NULL,
  `fecha_atencion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `solicitudes`
--

INSERT INTO `solicitudes` (`solicitud_id`, `usuario_id`, `area_id`, `fecha_solicitud`, `asunto`, `descripcion`, `prioridad`, `fecha_limite`, `estado`, `observaciones`, `foto`, `fecha_actualizacion`, `atendido_por`, `fecha_atencion`) VALUES
(1, 1, 1, '2026-08-13 07:38:41', 'Solicitud de teclados nuevos', 'Se requieren 5 teclados para el área de Sistemas por daño en equipos actuales.', 'media', NULL, 'aprobada', NULL, NULL, '2026-08-13 09:41:04', 1, '2026-08-13 09:41:04'),
(2, 1, 3, '2026-08-13 07:38:41', 'Reposición de cables UTP', 'Se necesitan cables UTP para cableado de red en el área de Operaciones.', 'urgente', NULL, 'atendida', NULL, NULL, '2026-08-19 15:23:59', 7, '2026-08-19 15:23:59'),
(7, 1, 2, '2026-08-13 11:03:52', 'Impresora SDM500', 'impresora para cambio', 'urgente', NULL, 'atendida', NULL, NULL, '2026-08-19 15:24:06', 1, '2026-08-19 15:24:06'),
(8, 6, 1, '2026-08-14 12:53:47', 'Impresora SDM500 -ETAPA PRTUEBA', 'escaner de medidas', 'alta', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL),
(9, 6, 2, '2026-08-14 12:57:28', 'Impresora SDM500 -ETAPA PRTUEBA 2', 'sdadsdad', 'alta', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL),
(10, 6, 1, '2026-08-14 15:07:34', 'PRUEBA PRIORIDAD', 'Prueba para validar prioridad', 'urgente', NULL, 'atendida', NULL, NULL, '2026-08-20 08:31:36', 1, '2026-08-20 08:31:36'),
(11, 6, 3, '2026-08-14 15:07:51', 'PRUEBA PRIORIDAD', 'Prueba para validar prioridad', 'urgente', NULL, 'atendida', NULL, NULL, '2026-08-22 09:18:33', 1, '2026-08-22 09:18:33'),
(12, 6, 3, '2026-08-14 15:08:05', 'PRUEBA PRIORIDAD', 'Prueba para validar prioridad', 'baja', NULL, 'atendida', NULL, NULL, '2026-08-20 08:34:57', 7, '2026-08-20 08:34:57'),
(13, 6, 3, '2026-08-14 15:08:21', 'PRUEBA PRIORIDAD', 'Prueba para validar prioridad', 'media', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL),
(14, 6, NULL, '2026-08-14 15:08:32', 'Prueba para validar prioridad', 'Prueba para validar prioridad', 'alta', NULL, 'rechazada', 'No se aprueba puesto que no se encuentra la cantidad solicitada', NULL, '2026-08-14 15:16:45', 1, '2026-08-14 15:16:45'),
(15, 6, 3, '2026-08-14 15:09:36', 'Impresora SDM500 -ETAPA PRTUEBA', '\'urgente\'', 'alta', NULL, 'atendida', NULL, NULL, '2026-08-15 09:28:39', 1, '2026-08-15 09:28:39'),
(16, 6, NULL, '2026-08-14 15:18:32', 'PRUEBA PRIORIDAD', 'admin', 'urgente', NULL, 'aprobada', NULL, NULL, '2026-08-14 15:19:42', 1, '2026-08-14 15:19:42'),
(17, 6, 3, '2026-08-15 07:27:58', 'tarjetas sdm 500', 'se necesita por cambio de repuesto', 'urgente', NULL, 'atendida', NULL, NULL, '2026-08-15 09:25:38', 1, '2026-08-15 09:25:38'),
(18, 1, NULL, '2026-08-15 09:28:52', 'PRUEBA PRIORIDAD', 'sdadasdsasassa', 'alta', NULL, 'aprobada', NULL, NULL, '2026-08-15 09:29:04', 1, '2026-08-15 09:29:04'),
(19, 1, NULL, '2026-08-15 09:29:44', 'PRUEBA PRIORIDAD-2', 'priodridad', 'alta', NULL, 'atendida', NULL, NULL, '2026-08-19 15:23:42', 7, '2026-08-19 15:23:42'),
(20, 7, 2, '2026-08-20 14:56:19', 'Impresora SDM500 -ETAPA PRTUEBA', 'prueba', 'media', NULL, 'atendida', NULL, NULL, '2026-08-20 14:56:37', 7, '2026-08-20 14:56:37'),
(23, 6, 2, '2026-08-21 09:33:06', 'solicitud para maquinas', 'solicitud', 'media', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL),
(24, 6, 2, '2026-08-21 11:29:50', 'Impresora SDM500 -ETAPA PRTUEBA 2', 'nose', 'alta', NULL, 'atendida', NULL, NULL, '2026-08-21 11:31:03', 1, '2026-08-21 11:31:03'),
(25, 9, 2, '2026-08-22 07:11:42', 'Maquiuna averiada pantalla', 'se necesitan repuestos', 'alta', NULL, 'atendida', NULL, NULL, '2026-08-22 09:14:23', 1, '2026-08-22 07:26:58'),
(26, 6, 2, '2026-08-24 12:39:15', 'solicitud Destornilladores', 'nononononono', 'urgente', '2026-08-26 23:13:47', 'atendida', NULL, NULL, '2026-08-26 11:13:47', 1, '2026-08-26 09:28:40'),
(29, 1, 2, '2026-08-26 11:33:40', 'cascada pum', 'dsadsasdasa', 'alta', '2026-08-27 11:33:53', 'atendida', NULL, NULL, '2026-08-26 11:34:16', 1, '2026-08-26 11:34:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipousuario`
--

CREATE TABLE `tipousuario` (
  `idTipoUsuario` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `tipousuario`
--

INSERT INTO `tipousuario` (`idTipoUsuario`, `nombre_rol`) VALUES
(1, 'superUsuario'),
(2, 'tecnico'),
(3, 'clienteProsegur');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `usuario_id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `cedula` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nivel_acceso` int(11) NOT NULL,
  `cargo` varchar(50) DEFAULT 'Jugador',
  `empresa` varchar(100) DEFAULT NULL,
  `tipo_solicitud` varchar(150) DEFAULT NULL,
  `estado` enum('activo','inactivo','pendiente') DEFAULT 'pendiente',
  `forzar_cambio_pwd` tinyint(1) DEFAULT 0,
  `pwd_ultimo_cambio` datetime DEFAULT NULL,
  `ultimo_acceso` datetime DEFAULT NULL,
  `idTipoUsuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`usuario_id`, `usuario`, `nombre`, `cedula`, `email`, `celular`, `password_hash`, `nivel_acceso`, `cargo`, `empresa`, `tipo_solicitud`, `estado`, `forzar_cambio_pwd`, `pwd_ultimo_cambio`, `ultimo_acceso`, `idTipoUsuario`) VALUES
(1, 'admin', 'Administrador', NULL, 'desarrollo@inees.co', NULL, '$2y$12$2fS4O2Oo2P4lqOJyP9BtEOSwp0EO2SR8johP2AmMTn5MhZVABm/Cu', 1, 'Super Usuario', NULL, NULL, 'activo', 0, NULL, '2026-08-27 08:18:31', 1),
(2, 'tecnicoProsegur', 'TecnicoProsegur', NULL, 'tecnico@inees.co', NULL, '$2y$12$2fS4O2Oo2P4lqOJyP9BtEOSwp0EO2SR8johP2AmMTn5MhZVABm/Cu', 1, 'clienteProsegur', NULL, NULL, 'activo', 0, NULL, NULL, NULL),
(3, 'tecnicoprueba', 'tecnicoprueba', NULL, 'tecnicoprueba@inees.co', NULL, '$2y$10$UqUA5C2A.sni9MtzIqxyNufxYSRmsHjf4D1pd9.I5zJKJa0NvAixe', 0, 'clienteProsegur', NULL, NULL, 'activo', 0, NULL, NULL, NULL),
(6, 'cliente_prosegur', 'Cliente Prosegur', '1098765432', 'cliente@prosegur.com', '3118795919', '$2y$10$w03q0BzaMcX5.Amb2aC3/OY43FBESlwXjPNibdI0JQ0FI/a0fD2Ne', 3, 'Cliente Principal', NULL, NULL, 'activo', 0, '2026-08-14 11:48:37', '2026-08-24 12:27:51', 3),
(7, 'tecnico.prueba', 'Técnico de Prueba', '1098765432', 'Juanes01vargas@gmail.com', '3118795919', '$2y$10$AwsWjDfJ8veBsH3OyXJKzuHmI5IwCBldygxhyUD13kpsrzNOWquYS', 2, 'Técnico Especialista', NULL, NULL, 'activo', 0, '2026-08-20 08:34:09', '2026-08-20 12:32:43', 2),
(8, 'EstebanVargas', 'Juan Esteban', '1034656278', 'juanes012vargas@gmail.com', '3118795919', '$2y$10$qZ.V9eUOwufD9wbfa9qI2Ot6iUX3CYC.IOOpHz8HcXBIDFzQaM66u', 2, 'Técnico', NULL, NULL, 'activo', 0, NULL, '2026-08-21 14:37:19', 2),
(9, 'yenniV', 'Yenni Vega', '1032484329', 'vyennilorena@gmail.com', '3012508286', '$2y$10$KKtKGMPxEW6pNSKKih4QVOzX82TScXy1EFFE15HMtBwIz9ahRxnV6', 2, 'Técnico', NULL, NULL, 'activo', 0, NULL, '2026-08-22 07:39:15', 2),
(10, 'samuelB', 'Samuel bedoya', '1031422232', 'desarrolloejemplo@inees.co', '3166347898', '$2y$10$dwEma9i7hBCGIvAzNtqRNOzNux3YhGbzw/lprVYTVrN9fa8ffmLnW', 2, 'Técnico', NULL, NULL, 'activo', 0, NULL, '2026-08-24 12:18:25', 2),
(11, 'test_cliente_3488', 'Juan Cliente Prueba', '1098765432', 'test_cliente_3488@empresa.com', '3009998877', '$2y$10$QZXmMy3fRBSqyj0hWJDl6euVXbFPvx.ucy27on03yyc2ehQzdMnfy', 3, 'Cliente', 'Empresa Test S.A.', NULL, 'pendiente', 0, NULL, NULL, 3),
(13, 'pepitoP', 'pepito perez', '1034656278', 'pepitoperez10@gmail.com', '3118795919', '$2y$10$GPgzf6pFkSBe.zgKVuAdF.Mjb81KTEjZ4rD9QmzMMXNbjPrpJXabK', 3, 'Cliente', 'Claro Colombia', NULL, 'inactivo', 0, NULL, NULL, 3),
(14, 'cliente_credencial_660', 'Empresa Cliente SAS', '900123456', 'contacto@empresacliente.com', '3112223344', '$2y$10$NlZrAI1gSQh.TJeUefQHlut9CX26j3x60L0zfkqxbcddQ8CraruiO', 3, 'Cliente', 'Empresa Cliente SAS', NULL, 'activo', 0, NULL, NULL, 3),
(16, 'danielO', 'Daniel Osorio', '10325456', 'juguito12@gmail.com', '3118795919', '$2y$10$1tGgK5LMrv79Rf58AKDIdOoeCBtCe.kKkdEBvCcLTBa6bc5HUQcO6', 3, 'Cliente', 'Servientrega', NULL, 'activo', 0, NULL, '2026-08-25 08:02:33', 3);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alertas_solicitudes`
--
ALTER TABLE `alertas_solicitudes`
  ADD PRIMARY KEY (`alerta_id`),
  ADD UNIQUE KEY `unique_alerta` (`solicitud_id`,`tipo_alerta`);

--
-- Indices de la tabla `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`id_area`);

--
-- Indices de la tabla `detalle_solicitud`
--
ALTER TABLE `detalle_solicitud`
  ADD PRIMARY KEY (`detalle_id`),
  ADD KEY `fk_detalle_solicitud` (`solicitud_id`),
  ADD KEY `fk_detalle_producto` (`producto_id`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`inventario_id`),
  ADD KEY `fk_inventario_producto` (`producto_id`);

--
-- Indices de la tabla `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id_login`);

--
-- Indices de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD PRIMARY KEY (`movimiento_id`),
  ADD KEY `fk_movimiento_producto` (`producto_id`),
  ADD KEY `fk_movimiento_usuario` (`usuario_id`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`notificacion_id`),
  ADD KEY `fk_notificacion_usuario` (`usuario_id`);

--
-- Indices de la tabla `password_reset`
--
ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`producto_id`);

--
-- Indices de la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD PRIMARY KEY (`rol_id`,`ruta_id`),
  ADD KEY `ruta_id` (`ruta_id`);

--
-- Indices de la tabla `rutas`
--
ALTER TABLE `rutas`
  ADD PRIMARY KEY (`id_ruta`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`solicitud_id`),
  ADD KEY `fk_solicitud_usuario` (`usuario_id`),
  ADD KEY `fk_solicitud_area` (`area_id`),
  ADD KEY `fk_solicitud_atendido` (`atendido_por`);

--
-- Indices de la tabla `tipousuario`
--
ALTER TABLE `tipousuario`
  ADD PRIMARY KEY (`idTipoUsuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`usuario_id`),
  ADD KEY `nivel_acceso` (`nivel_acceso`),
  ADD KEY `fk_usuarios_tipousuario` (`idTipoUsuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alertas_solicitudes`
--
ALTER TABLE `alertas_solicitudes`
  MODIFY `alerta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `areas`
--
ALTER TABLE `areas`
  MODIFY `id_area` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `detalle_solicitud`
--
ALTER TABLE `detalle_solicitud`
  MODIFY `detalle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `inventario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `login`
--
ALTER TABLE `login`
  MODIFY `id_login` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  MODIFY `movimiento_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `notificacion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `password_reset`
--
ALTER TABLE `password_reset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `producto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `rutas`
--
ALTER TABLE `rutas`
  MODIFY `id_ruta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `solicitud_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `tipousuario`
--
ALTER TABLE `tipousuario`
  MODIFY `idTipoUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alertas_solicitudes`
--
ALTER TABLE `alertas_solicitudes`
  ADD CONSTRAINT `fk_alerta_solicitud` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`solicitud_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_solicitud`
--
ALTER TABLE `detalle_solicitud`
  ADD CONSTRAINT `fk_detalle_solicitud` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`solicitud_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `fk_inventario_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`producto_id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD CONSTRAINT `fk_movimiento_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`producto_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_movimiento_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `fk_notificacion_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD CONSTRAINT `rol_permisos_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `tipousuario` (`idTipoUsuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `rol_permisos_ibfk_2` FOREIGN KEY (`ruta_id`) REFERENCES `rutas` (`id_ruta`) ON DELETE CASCADE;

--
-- Filtros para la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `fk_solicitud_area` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id_area`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_solicitud_atendido` FOREIGN KEY (`atendido_por`) REFERENCES `usuarios` (`usuario_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_solicitud_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_tipousuario` FOREIGN KEY (`idTipoUsuario`) REFERENCES `tipousuario` (`idTipoUsuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
