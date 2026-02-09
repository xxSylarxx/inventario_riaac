-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 09-02-2026 a las 11:52:02
-- Versión del servidor: 8.0.32
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `inventario_riaac`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alertas_vencimiento`
--

DROP TABLE IF EXISTS `alertas_vencimiento`;
CREATE TABLE IF NOT EXISTS `alertas_vencimiento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipo` enum('hosting','dominio') COLLATE utf8mb4_unicode_ci NOT NULL,
  `referencia_id` int NOT NULL COMMENT 'ID del hosting_dominio',
  `dias_restantes` int NOT NULL,
  `estado` enum('pendiente','enviada','ignorada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `fecha_generacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_estado` (`estado`),
  KEY `idx_referencia` (`referencia_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Alertas de vencimiento';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_productos`
--

DROP TABLE IF EXISTS `categorias_productos`;
CREATE TABLE IF NOT EXISTS `categorias_productos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `idx_nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Categorías de productos';

--
-- Volcado de datos para la tabla `categorias_productos`
--

INSERT INTO `categorias_productos` (`id`, `nombre`, `descripcion`, `fecha_creacion`) VALUES
(1, 'Accesorios', 'Accesorios de computadora', '2026-02-01 23:41:28'),
(2, 'Repuestos', 'Repuestos y componentes', '2026-02-01 23:41:28'),
(3, 'Estabilizadores', 'Estabilizadores de voltaje', '2026-02-01 23:41:28'),
(4, 'Cables y Conectores', 'Cables HDMI, VGA, USB, etc', '2026-02-01 23:41:28'),
(5, 'Periféricos', 'Mouse, teclados, webcams', '2026-02-01 23:41:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipo` enum('persona','empresa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'persona',
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre o Razón Social',
  `dni_ruc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `ubicacion_google_maps` text COLLATE utf8mb4_unicode_ci,
  `estado` enum('activo','inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_nombre` (`nombre`),
  KEY `idx_dni_ruc` (`dni_ruc`),
  KEY `idx_estado` (`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Clientes del negocio';

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `tipo`, `nombre`, `dni_ruc`, `email`, `telefono`, `whatsapp`, `direccion`, `ubicacion_google_maps`, `estado`, `fecha_registro`) VALUES
(1, 'persona', 'Juny Sanchez Pejerrey', '', '', '', '+51924826449', 'Comas', NULL, 'activo', '2026-02-02 00:26:36'),
(2, 'persona', 'Familiar o amigo de Alex', '', '', '', '+51983952957', 'Puente Piedra', NULL, 'activo', '2026-02-02 00:53:17'),
(3, 'persona', 'Julio Conislla', '', '', '', '+51972043280', 'Comas', NULL, 'activo', '2026-02-02 00:54:17'),
(4, 'persona', 'Cila Pan', '', '', '', '+51983952957', 'Puente Piedra, familiar de Alex', 'https://maps.app.goo.gl/RsaP5gf7GsLEgjmv5', 'activo', '2026-02-08 06:00:50'),
(5, 'persona', 'Sr. Segura', '', '', '', '+51966277759', '', '', 'activo', '2026-02-08 19:02:14'),
(6, 'persona', 'Prof. Ramos', '', '', '', '+51955538791', '', '', 'activo', '2026-02-08 21:15:06'),
(7, 'persona', 'Kevin Colonia', '', '', '', '+51960880415', '', '', 'activo', '2026-02-08 21:30:01'),
(8, 'empresa', 'ASOCIACIÓN EDUCATIVA LICEO SANTO DOMINGO', '20547080841', '', '', '+51904751921', '', '', 'activo', '2026-02-08 21:38:15'),
(9, 'empresa', 'ASOCIACION EDUCATIVA SANTO DOMINGO, EL LIDER', '20614679655', '', '', '+51962733155', '', '', 'activo', '2026-02-08 21:40:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos`
--

DROP TABLE IF EXISTS `documentos`;
CREATE TABLE IF NOT EXISTS `documentos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipo_servicio` enum('hosting','reparacion','venta') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `servicio_id` int NOT NULL,
  `tipo_documento` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_original` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruta_archivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_subida` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_servicio` (`tipo_servicio`,`servicio_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `documentos`
--

INSERT INTO `documentos` (`id`, `tipo_servicio`, `servicio_id`, `tipo_documento`, `nombre_original`, `ruta_archivo`, `descripcion`, `fecha_subida`) VALUES
(7, 'venta', 2, 'presupuesto', 'pago_tinta.jpeg', 'uploads/documentos/presupuesto/doc_20260208_6988fe6fac8589.63748391.jpeg', '', '2026-02-08 21:21:51'),
(8, 'hosting', 1, 'presupuesto', 'C029-PROPUESTA CORREOS CORPORATIVOS_INGENIEROS.pdf', 'uploads/documentos/presupuesto/doc_20260208_6989052d7cea91.47945717.pdf', '', '2026-02-08 21:50:37'),
(15, 'hosting', 2, 'contrato', 'CONTRATO DE SERVICIO DE CORREOS CORPORATIVOS.pdf', 'uploads/documentos/contrato/doc_20260208_69890646758c21.09698019.pdf', '', '2026-02-08 21:55:18'),
(16, 'hosting', 5, 'presupuesto', 'C030-PROPUESTA LIDERSD.pdf', 'uploads/documentos/presupuesto/doc_20260208_6989072a85c474.41789909.pdf', '', '2026-02-08 21:59:06'),
(17, 'hosting', 6, 'presupuesto', 'RENOVACION DE HOSTING Y DOMINIO_LSD.pdf', 'uploads/documentos/presupuesto/doc_20260208_69891d6c32c512.21092137.pdf', '', '2026-02-08 23:34:04'),
(18, 'hosting', 6, 'presupuesto', 'RENOVACION DE HOSTING Y DOMINIO_LSD.docx', 'uploads/documentos/presupuesto/doc_20260208_69892111c35168.78671491.docx', '', '2026-02-08 23:49:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hosting_dominios`
--

DROP TABLE IF EXISTS `hosting_dominios`;
CREATE TABLE IF NOT EXISTS `hosting_dominios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `dominio` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `proveedor` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Proveedor del hosting',
  `fecha_inicio` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `precio_compra` decimal(10,2) DEFAULT NULL,
  `precio_venta` decimal(10,2) DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `correos_corporativos` text COLLATE utf8mb4_unicode_ci COMMENT 'Lista de correos corporativos creados para el cliente',
  `archivo_presupuesto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('activo','vencido','cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cliente` (`cliente_id`),
  KEY `idx_dominio` (`dominio`),
  KEY `idx_vencimiento` (`fecha_vencimiento`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Hosting y dominios';

--
-- Volcado de datos para la tabla `hosting_dominios`
--

INSERT INTO `hosting_dominios` (`id`, `cliente_id`, `dominio`, `proveedor`, `fecha_inicio`, `fecha_vencimiento`, `precio_compra`, `precio_venta`, `observaciones`, `correos_corporativos`, `archivo_presupuesto`, `estado`, `fecha_registro`) VALUES
(1, 1, 'siasacperu.com', 'Yachay', '2025-06-18', '2026-06-19', 57.99, 150.00, '<ul><li>Dominio .pe (siasacperu.com)</li><li>Hosting de 3000 MB</li><li>3 cuentas de correo</li><li>Beneficio exclusivo: 1 cuenta adicional con integración Gmail (caso especial para este proyecto)</li><li>Antivirus</li><li>CERTIFICADO SSL https://</li></ul><p><br></p><p>Esta propuesta se encuentra en el acceso directo de delivery de imagenes, ya que hay un error en el correlativo de la propuesta</p>', 'gerencia@siasacperu.com\r\noperaciones@siasacperu.com\r\nventas@siasacperu.com', 'uploads/presupuestos/presupuesto_20260201_697ff5017d38d7.63656992.pdf', 'activo', '2026-02-02 00:33:52'),
(2, 3, 'zurahvac.com.pe', 'puntope', '2025-12-19', '2026-12-19', 110.00, 210.00, '<ul><li>El servicio de hosting para el período 2025–2026 tiene un costo de S/ 100.00 (cien soles). </li><li>El servicio incluye cinco (5) correos corporativos, cada uno con 2 GB de almacenamiento. </li><li>El servicio de hosting y correos estará activo durante la vigencia del presente acuerdo.&nbsp;</li></ul>', 'proyectos@zurahvac.com.pe', 'uploads/presupuestos/doc_20260202_6980899e80ebf2.08808028.pdf', 'activo', '2026-02-02 00:59:02'),
(3, 7, 'soltecsac.com.pe', 'puntope', '2025-03-05', '2026-03-06', 110.00, 110.00, '<p><br></p>', 'kcolonia@soltecsac.com.pe\r\noperaciones@soltecsac.com.pe\r\nventas@soltecsac.com.pe', NULL, 'activo', '2026-02-08 21:33:12'),
(4, 7, 'textilescolonia.com.pe', 'puntope', '2025-08-10', '2026-08-09', 110.00, 110.00, '<p><br></p>', 'administracion@textilescolonia.com.pe\r\ngerencia@textilescolonia.com.pe\r\nventas@textilescolonia.com.pe', NULL, 'activo', '2026-02-08 21:35:42'),
(5, 9, 'lidersd.edu.pe', 'puntope', '2025-11-05', '2026-11-04', 110.00, 110.00, '<p><br></p>', '', NULL, 'activo', '2026-02-08 21:41:45'),
(6, 8, 'liceosd.edu.pe', 'puntope', '2025-09-23', '2026-09-22', 110.00, 110.00, '<p><br></p>', '', NULL, 'activo', '2026-02-08 21:42:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

DROP TABLE IF EXISTS `productos`;
CREATE TABLE IF NOT EXISTS `productos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categoria_id` int DEFAULT NULL,
  `proveedor_id` int DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `precio_compra` decimal(10,2) DEFAULT NULL,
  `fecha_compra` date DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `estado` enum('activo','inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `proveedor_id` (`proveedor_id`),
  KEY `idx_nombre` (`nombre`),
  KEY `idx_codigo` (`codigo`),
  KEY `idx_categoria` (`categoria_id`),
  KEY `idx_fecha_compra` (`fecha_compra`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Productos';

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `codigo`, `categoria_id`, `proveedor_id`, `stock`, `precio_compra`, `fecha_compra`, `descripcion`, `estado`, `fecha_registro`) VALUES
(5, 'ESTABILIZADOR FORZA USB 1200VA/600W 8 TOMAS BLACK', 'FVR-1222 ', 3, 1, 0, 70.00, '2026-01-30', 'ESTABILIZADOR FORZA FVR-1222 USB 1200VA/600W 8 TOMAS BLACK', 'activo', '2026-02-08 05:55:18'),
(6, 'TINTA NEGRA HP SMART TANK 580', 'BK', 2, NULL, 0, 38.00, '2026-02-06', 'TINTA DE COLOR NEGRO', 'activo', '2026-02-08 21:14:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE IF NOT EXISTS `proveedores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contacto` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `estado` enum('activo','inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Proveedores';

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id`, `nombre`, `contacto`, `whatsapp`, `email`, `direccion`, `estado`, `fecha_registro`) VALUES
(1, 'K&M COMPUTER', '', '+51981278881', '', 'Wilson - Galeria Compuplaza tienda 129', 'activo', '2026-02-08 05:54:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reparaciones`
--

DROP TABLE IF EXISTS `reparaciones`;
CREATE TABLE IF NOT EXISTS `reparaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `tipo_equipo` enum('PC','Laptop','Otro') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PC',
  `marca` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modelo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `falla_reportada` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `trabajo_realizado` text COLLATE utf8mb4_unicode_ci,
  `archivo_presupuesto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_ingreso` date NOT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `dias_garantia` int DEFAULT '0' COMMENT 'Días de garantía del servicio',
  `estado` enum('pendiente','en_proceso','entregado','cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cliente` (`cliente_id`),
  KEY `idx_fecha_ingreso` (`fecha_ingreso`),
  KEY `idx_estado` (`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Servicios de reparación';

--
-- Volcado de datos para la tabla `reparaciones`
--

INSERT INTO `reparaciones` (`id`, `cliente_id`, `tipo_equipo`, `marca`, `modelo`, `falla_reportada`, `trabajo_realizado`, `archivo_presupuesto`, `fecha_ingreso`, `fecha_entrega`, `precio`, `dias_garantia`, `estado`, `fecha_registro`) VALUES
(1, 4, 'PC', 'Dell', 'Desktop', 'La maquina no encendía, y la encontré con el ventilador girando, lo apagué y al momento de encender empezó a tener el pitido clásico de que no reconoce la memoria ram', 'Se abrió el equipo , se retiro el polvo que tenia saqué y volvi a colocar la memoria RAM y encendió sin problemas', NULL, '2026-02-07', '2026-02-07', 20.00, 30, 'entregado', '2026-02-08 18:56:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Hash de la contraseña',
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('administrador','tecnico') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tecnico',
  `estado` enum('activo','inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_usuario` (`usuario`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Usuarios del sistema';

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `password`, `nombre`, `email`, `rol`, `estado`, `fecha_creacion`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador del Sistema', 'admin@riaac.com', 'administrador', 'activo', '2026-02-01 23:41:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas_productos`
--

DROP TABLE IF EXISTS `ventas_productos`;
CREATE TABLE IF NOT EXISTS `ventas_productos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `producto_id` int NOT NULL,
  `cliente_id` int DEFAULT NULL,
  `cantidad` int NOT NULL DEFAULT '1',
  `fecha_venta` date NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `precio_compra` decimal(10,2) DEFAULT NULL COMMENT 'Para calcular utilidad',
  `dias_garantia` int DEFAULT '0',
  `comprobante_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ruta del comprobante PDF o imagen',
  `observaciones` text COLLATE utf8mb4_unicode_ci COMMENT 'Observaciones o notas adicionales sobre la venta',
  `archivo_presupuesto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('activo','anulado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_producto` (`producto_id`),
  KEY `idx_cliente` (`cliente_id`),
  KEY `idx_fecha_venta` (`fecha_venta`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Ventas de productos';

--
-- Volcado de datos para la tabla `ventas_productos`
--

INSERT INTO `ventas_productos` (`id`, `producto_id`, `cliente_id`, `cantidad`, `fecha_venta`, `precio_venta`, `precio_compra`, `dias_garantia`, `comprobante_url`, `observaciones`, `archivo_presupuesto`, `estado`, `fecha_registro`) VALUES
(1, 5, 4, 1, '2026-02-08', 120.00, 70.00, 365, NULL, NULL, NULL, 'activo', '2026-02-08 06:02:23'),
(2, 6, 6, 1, '2026-02-08', 60.00, 38.00, 90, NULL, '<p><br></p>', NULL, 'activo', '2026-02-08 21:15:42');

--
-- Disparadores `ventas_productos`
--
DROP TRIGGER IF EXISTS `tr_actualizar_stock_venta`;
DELIMITER $$
CREATE TRIGGER `tr_actualizar_stock_venta` AFTER INSERT ON `ventas_productos` FOR EACH ROW BEGIN
    UPDATE productos 
    SET stock = stock - NEW.cantidad 
    WHERE id = NEW.producto_id;
END
$$
DELIMITER ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `hosting_dominios`
--
ALTER TABLE `hosting_dominios`
  ADD CONSTRAINT `hosting_dominios_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_productos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `reparaciones`
--
ALTER TABLE `reparaciones`
  ADD CONSTRAINT `reparaciones_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT;

--
-- Filtros para la tabla `ventas_productos`
--
ALTER TABLE `ventas_productos`
  ADD CONSTRAINT `ventas_productos_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `ventas_productos_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
