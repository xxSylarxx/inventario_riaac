-- Crear tabla para almacenar múltiples documentos por servicio
CREATE TABLE documentos_servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_servicio ENUM('hosting', 'reparacion', 'venta') NOT NULL COMMENT 'Tipo de servicio asociado',
    servicio_id INT NOT NULL COMMENT 'ID del servicio (hosting_dominios, reparaciones o ventas_productos)',
    tipo_documento VARCHAR(50) NOT NULL COMMENT 'Tipo: presupuesto, contrato, factura, comprobante, otro',
    nombre_original VARCHAR(255) NOT NULL COMMENT 'Nombre original del archivo',
    ruta_archivo VARCHAR(255) NOT NULL COMMENT 'Ruta del archivo en el servidor',
    descripcion TEXT NULL COMMENT 'Descripción opcional del documento',
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de subida',
    
    INDEX idx_servicio (tipo_servicio, servicio_id),
    INDEX idx_tipo_documento (tipo_documento),
    INDEX idx_fecha (fecha_subida)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Documentos adjuntos a servicios (múltiples por servicio)';

-- Crear carpetas para organizar documentos por tipo
-- uploads/documentos/presupuestos/
-- uploads/documentos/contratos/
-- uploads/documentos/facturas/
-- uploads/documentos/comprobantes/
-- uploads/documentos/otros/
