-- ============================================================
-- SISTEMA DE GESTIÓN ADMINISTRATIVA - RIAAC
-- Base de datos para gestión de servicios tecnológicos
-- Versión corregida para MySQL (sin CURDATE() en columnas generadas)
-- ============================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS inventario_riaac CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventario_riaac;

-- ============================================================
-- TABLA: usuarios
-- Gestión de usuarios del sistema con roles
-- ============================================================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'Hash de la contraseña',
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    rol ENUM('administrador', 'tecnico') NOT NULL DEFAULT 'tecnico',
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usuario (usuario),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Usuarios del sistema';

-- ============================================================
-- TABLA: clientes
-- Registro de clientes del negocio (personas o empresas)
-- ============================================================
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('persona', 'empresa') NOT NULL DEFAULT 'persona',
    nombre VARCHAR(150) NOT NULL COMMENT 'Nombre o Razón Social',
    dni_ruc VARCHAR(20) NULL,
    email VARCHAR(100) NULL,
    telefono VARCHAR(20) NULL,
    whatsapp VARCHAR(20) NULL,
    direccion TEXT NULL,
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre),
    INDEX idx_dni_ruc (dni_ruc),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Clientes del negocio';

-- ============================================================
-- TABLA: hosting_dominios
-- Servicios de hosting y dominios vendidos a clientes
-- NOTA: dias_para_vencer se calcula en PHP, no en SQL
-- ============================================================
CREATE TABLE hosting_dominios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    dominio VARCHAR(255) NOT NULL,
    proveedor VARCHAR(100) NULL COMMENT 'Proveedor del hosting',
    fecha_inicio DATE NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    precio_compra DECIMAL(10,2) NULL,
    precio_venta DECIMAL(10,2) NULL,
    observaciones TEXT NULL,
    estado ENUM('activo', 'vencido', 'cancelado') NOT NULL DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT,
    INDEX idx_cliente (cliente_id),
    INDEX idx_dominio (dominio),
    INDEX idx_vencimiento (fecha_vencimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Hosting y dominios';

-- ============================================================
-- TABLA: reparaciones
-- Servicios técnicos de reparación de equipos
-- NOTA: fecha_fin_garantia y estado_garantia se calculan en PHP
-- ============================================================
CREATE TABLE reparaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    tipo_equipo ENUM('PC', 'Laptop', 'Otro') NOT NULL DEFAULT 'PC',
    marca VARCHAR(50) NULL,
    modelo VARCHAR(100) NULL,
    falla_reportada TEXT NOT NULL,
    trabajo_realizado TEXT NULL,
    fecha_ingreso DATE NOT NULL,
    fecha_entrega DATE NULL,
    precio DECIMAL(10,2) NULL,
    dias_garantia INT DEFAULT 0 COMMENT 'Días de garantía del servicio',
    estado ENUM('pendiente', 'en_proceso', 'entregado', 'cancelado') NOT NULL DEFAULT 'pendiente',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT,
    INDEX idx_cliente (cliente_id),
    INDEX idx_fecha_ingreso (fecha_ingreso),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Servicios de reparación';

-- ============================================================
-- TABLA: categorias_productos
-- Categorías para clasificar productos
-- ============================================================
CREATE TABLE categorias_productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Categorías de productos';

-- ============================================================
-- TABLA: proveedores
-- Proveedores de productos
-- ============================================================
CREATE TABLE proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    contacto VARCHAR(100) NULL,
    whatsapp VARCHAR(20) NULL,
    email VARCHAR(100) NULL,
    direccion TEXT NULL,
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Proveedores';

-- ============================================================
-- TABLA: productos
-- Inventario de productos de cómputo
-- ============================================================
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    codigo VARCHAR(50) NULL UNIQUE,
    categoria_id INT NULL,
    proveedor_id INT NULL,
    stock INT NOT NULL DEFAULT 0,
    precio_compra DECIMAL(10,2) NULL,
    descripcion TEXT NULL,
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias_productos(id) ON DELETE SET NULL,
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE SET NULL,
    INDEX idx_nombre (nombre),
    INDEX idx_codigo (codigo),
    INDEX idx_categoria (categoria_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Productos';

-- ============================================================
-- TABLA: ventas_productos
-- Registro de ventas de productos con garantías
-- NOTA: fecha_fin_garantia y estado_garantia se calculan en PHP
-- ============================================================
CREATE TABLE ventas_productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    cliente_id INT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    fecha_venta DATE NOT NULL,
    precio_venta DECIMAL(10,2) NOT NULL,
    precio_compra DECIMAL(10,2) NULL COMMENT 'Para calcular utilidad',
    dias_garantia INT DEFAULT 0,
    comprobante_url VARCHAR(255) NULL COMMENT 'Ruta del comprobante PDF o imagen',
    estado ENUM('activo', 'anulado') NOT NULL DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL,
    INDEX idx_producto (producto_id),
    INDEX idx_cliente (cliente_id),
    INDEX idx_fecha_venta (fecha_venta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Ventas de productos';

-- ============================================================
-- TABLA: alertas_vencimiento
-- Log de alertas generadas por vencimientos próximos
-- ============================================================
CREATE TABLE alertas_vencimiento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('hosting', 'dominio') NOT NULL,
    referencia_id INT NOT NULL COMMENT 'ID del hosting_dominio',
    dias_restantes INT NOT NULL,
    estado ENUM('pendiente', 'enviada', 'ignorada') NOT NULL DEFAULT 'pendiente',
    fecha_generacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo),
    INDEX idx_estado (estado),
    INDEX idx_referencia (referencia_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Alertas de vencimiento';

-- ============================================================
-- DATOS INICIALES
-- ============================================================

-- Usuario administrador por defecto
-- Usuario: admin | Password: admin123
INSERT INTO usuarios (usuario, password, nombre, email, rol, estado) VALUES 
('admin', '$2y$10$E3Uva8wqYH6YPMbD9w9uN.kGqLsVLkkPy.qXzHNb6L0rjuNQZHqxO', 'Administrador del Sistema', 'admin@riaac.com', 'administrador', 'activo');

-- Categorías de productos iniciales
INSERT INTO categorias_productos (nombre, descripcion) VALUES
('Accesorios', 'Accesorios de computadora'),
('Repuestos', 'Repuestos y componentes'),
('Estabilizadores', 'Estabilizadores de voltaje'),
('Cables y Conectores', 'Cables HDMI, VGA, USB, etc'),
('Periféricos', 'Mouse, teclados, webcams');

-- ============================================================
-- TRIGGER: Actualizar stock al vender producto
-- ============================================================
DELIMITER $$
CREATE TRIGGER tr_actualizar_stock_venta 
AFTER INSERT ON ventas_productos
FOR EACH ROW
BEGIN
    UPDATE productos 
    SET stock = stock - NEW.cantidad 
    WHERE id = NEW.producto_id;
END$$
DELIMITER ;

-- ============================================================
-- FIN DEL SCRIPT - Base de datos creada exitosamente
-- ============================================================
