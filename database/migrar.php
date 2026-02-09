<?php

/**
 * Script para migrar archivos del sistema antiguo al nuevo
 * Ejecuta este archivo UNA VEZ desde el navegador: http://localhost/inventario_riaac/database/migrar.php
 */

require_once '../config/config.php';
require_once '../includes/autoload.php';

echo "<h1>Migración de Archivos Antiguos</h1>";
echo "<hr>";

try {
    // Contar archivos a migrar
    $stmt = $pdo->query("SELECT COUNT(*) FROM hosting_dominios WHERE archivo_presupuesto IS NOT NULL AND archivo_presupuesto != ''");
    $total = $stmt->fetchColumn();

    echo "<p><strong>Archivos antiguos encontrados:</strong> $total</p>";

    if ($total == 0) {
        echo "<p class='alert alert-info'>✓ No hay archivos antiguos para migrar.</p>";
        exit;
    }

    // Migrar
    $sql = "INSERT INTO documentos_servicios 
        (tipo_servicio, servicio_id, tipo_documento, nombre_original, ruta_archivo, descripcion, fecha_subida)
    SELECT 
        'hosting' as tipo_servicio,
        id as servicio_id,
        'presupuesto' as tipo_documento,
        SUBSTRING_INDEX(archivo_presupuesto, '/', -1) as nombre_original,
        archivo_presupuesto as ruta_archivo,
        'Migrado desde sistema antiguo' as descripcion,
        fecha_inicio as fecha_subida
    FROM hosting_dominios
    WHERE archivo_presupuesto IS NOT NULL 
      AND archivo_presupuesto != ''
      AND id NOT IN (
          SELECT servicio_id FROM documentos_servicios 
          WHERE tipo_servicio = 'hosting' AND descripcion = 'Migrado desde sistema antiguo'
      )";

    $result = $pdo->exec($sql);

    echo "<p class='alert alert-success'>✓ <strong>Migración completada:</strong> $result archivo(s) migrado(s)</p>";

    // Mostrar resumen
    echo "<h3>Resumen por servicio:</h3>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Dominio</th><th>Cliente</th><th>Documentos Migrados</th></tr>";

    $stmt = $pdo->query("
        SELECT 
            h.id,
            h.dominio,
            c.nombre as cliente,
            COUNT(d.id) as documentos_migrados
        FROM hosting_dominios h
        LEFT JOIN clientes c ON h.cliente_id = c.id
        LEFT JOIN documentos_servicios d ON d.servicio_id = h.id AND d.tipo_servicio = 'hosting'
        WHERE h.archivo_presupuesto IS NOT NULL
        GROUP BY h.id
        ORDER BY h.id
    ");

    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['dominio']}</td>";
        echo "<td>{$row['cliente']}</td>";
        echo "<td>{$row['documentos_migrados']}</td>";
        echo "</tr>";
    }

    echo "</table>";

    echo "<br><p><a href='../views/hosting/index.php'>← Volver al listado de hosting</a></p>";
} catch (Exception $e) {
    echo "<p class='alert alert-danger'>✗ <strong>Error:</strong> " . $e->getMessage() . "</p>";
}
