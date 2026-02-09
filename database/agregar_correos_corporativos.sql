-- Agregar campo de correos corporativos a hosting_dominios
ALTER TABLE hosting_dominios 
ADD COLUMN correos_corporativos TEXT NULL COMMENT 'Lista de correos corporativos creados para el cliente' 
AFTER observaciones;
