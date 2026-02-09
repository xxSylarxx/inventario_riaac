-- Agregar campos para adjuntar presupuestos/archivos a los servicios

-- Hosting y Dominios
ALTER TABLE hosting_dominios 
ADD COLUMN archivo_presupuesto VARCHAR(255) NULL COMMENT 'Ruta del archivo de presupuesto aprobado' 
AFTER correos_corporativos;

-- Reparaciones
ALTER TABLE reparaciones 
ADD COLUMN archivo_presupuesto VARCHAR(255) NULL COMMENT 'Ruta del archivo de presupuesto aprobado' 
AFTER trabajo_realizado;

-- Ventas de Productos (ya tiene comprobante_url, agregar presupuesto)
ALTER TABLE ventas_productos 
ADD COLUMN archivo_presupuesto VARCHAR(255) NULL COMMENT 'Ruta del archivo de presupuesto aprobado' 
AFTER comprobante_url;
