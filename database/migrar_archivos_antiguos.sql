-- Migrar archivos de presupuesto antiguos al nuevo sistema de documentos
-- Este script copia los archivos del campo 'archivo_presupuesto' a la tabla 'documentos_servicios'

INSERT INTO documentos_servicios 
    (tipo_servicio, servicio_id, tipo_documento, nombre_original, ruta_archivo, descripcion, fecha_subida)
SELECT 
    'hosting' as tipo_servicio,
    id as servicio_id,
    'presupuesto' as tipo_documento,
    SUBSTRING_INDEX(archivo_presupuesto, '/', -1) as nombre_original, -- Extraer nombre del archivo
    archivo_presupuesto as ruta_archivo,
    'Migrado desde sistema antiguo' as descripcion,
    fecha_inicio as fecha_subida -- Usar fecha de inicio como referencia
FROM hosting_dominios
WHERE archivo_presupuesto IS NOT NULL 
  AND archivo_presupuesto != ''
  AND id NOT IN (
      -- Evitar duplicados: no migrar si ya existe
      SELECT servicio_id FROM documentos_servicios 
      WHERE tipo_servicio = 'hosting'
  );

-- Verificar migración
SELECT 
    h.id,
    c.nombre as cliente,
    h.dominio,
    COUNT(d.id) as documentos_migrados
FROM hosting_dominios h
LEFT JOIN clientes c ON h.cliente_id = c.id
LEFT JOIN documentos_servicios d ON d.servicio_id = h.id AND d.tipo_servicio = 'hosting'
WHERE h.archivo_presupuesto IS NOT NULL
GROUP BY h.id, c.nombre, h.dominio;
