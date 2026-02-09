-- ============================================================
-- ALTER TABLE para agregar campo de observaciones a ventas
-- ============================================================
-- Este script agrega el campo 'observaciones' a la tabla ventas_productos
-- para permitir notas adicionales sobre cada venta.
-- 
-- NOTA: La funcionalidad de documentos ya está soportada a través de
-- la tabla 'documentos_servicios' con tipo_servicio='venta'
-- ============================================================

USE inventario_riaac;

-- Agregar campo de observaciones
ALTER TABLE ventas_productos 
ADD COLUMN observaciones TEXT NULL COMMENT 'Observaciones o notas adicionales sobre la venta'
AFTER comprobante_url;

-- ============================================================
-- Verificar cambios
-- ============================================================
-- Para verificar que el campo se agregó correctamente, ejecutar:
-- DESCRIBE ventas_productos;
-- ============================================================
