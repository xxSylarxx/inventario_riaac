<?php

/**
 * Modelo Garantia
 * Vista unificada de garantías de productos y servicios
 */

class Garantia
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtener todas las garantías de productos
     */
    public function obtenerGarantiasProductos($filtro = 'todas')
    {
        $sql = "
SELECT v.id, v.fecha_venta as fecha, v.fecha_fin_garantia, v.estado_garantia,
p.nombre as descripcion, c.nombre as cliente_nombre, 'Producto' as tipo
FROM ventas_productos v
LEFT JOIN productos p ON v.producto_id = p.id
LEFT JOIN clientes c ON v.cliente_id = c.id
WHERE v.estado = 'activo' AND v.dias_garantia > 0
";

        if ($filtro === 'vigentes') {
            $sql .= " AND v.estado_garantia = 'vigente'";
        } elseif ($filtro === 'vencidas') {
            $sql .= " AND v.estado_garantia = 'vencida'";
        } elseif ($filtro === 'por_vencer') {
            $sql .= " AND v.estado_garantia = 'vigente' AND DATEDIFF(v.fecha_fin_garantia, CURDATE()) <= 30";
        }

        $sql .= " ORDER BY v.fecha_fin_garantia ASC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Obtener todas las garantías de servicios
     */
    public function obtenerGarantiasServicios($filtro = 'todas')
    {
        $sql = "
    SELECT r.id, r.fecha_entrega as fecha, r.fecha_fin_garantia, r.estado_garantia,
    CONCAT(r.tipo_equipo, ' - ', r.marca, ' ', IFNULL(r.modelo, '')) as descripcion,
    c.nombre as cliente_nombre, 'Servicio' as tipo
    FROM reparaciones r
    LEFT JOIN clientes c ON r.cliente_id = c.id
    WHERE r.estado = 'entregado' AND r.dias_garantia > 0
    ";

        if ($filtro === 'vigentes') {
            $sql .= " AND r.estado_garantia = 'vigente'";
        } elseif ($filtro === 'vencidas') {
            $sql .= " AND r.estado_garantia = 'vencida'";
        } elseif ($filtro === 'por_vencer') {
            $sql .= " AND r.estado_garantia = 'vigente' AND DATEDIFF(r.fecha_fin_garantia, CURDATE()) <= 30";
        }

        $sql .= " ORDER BY r.fecha_fin_garantia ASC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Obtener garantías unificadas (productos + servicios)
     */
    public function obtenerTodasGarantias($filtro = 'todas')
    {
        $productos = $this->obtenerGarantiasProductos($filtro);
        $servicios = $this->obtenerGarantiasServicios($filtro);

        return array_merge($productos, $servicios);
    }

    /**
     * Contar garantías por estado
     */
    public function contarPorEstado()
    {
        $resultado = [
            'productos_vigentes' => 0,
            'productos_vencidas' => 0,
            'servicios_vigentes' => 0,
            'servicios_vencidas' => 0,
        ];

        // Contar productos
        $stmt = $this->pdo->query("
        SELECT COUNT(*) FROM ventas_productos
        WHERE estado = 'activo' AND estado_garantia = 'vigente'
        ");
        $resultado['productos_vigentes'] = $stmt->fetchColumn();

        $stmt = $this->pdo->query("
        SELECT COUNT(*) FROM ventas_productos
        WHERE estado = 'activo' AND estado_garantia = 'vencida'
        ");
        $resultado['productos_vencidas'] = $stmt->fetchColumn();

        // Contar servicios
        $stmt = $this->pdo->query("
        SELECT COUNT(*) FROM reparaciones
        WHERE estado = 'entregado' AND estado_garantia = 'vigente'
        ");
        $resultado['servicios_vigentes'] = $stmt->fetchColumn();

        $stmt = $this->pdo->query("
        SELECT COUNT(*) FROM reparaciones
        WHERE estado = 'entregado' AND estado_garantia = 'vencida'
        ");
        $resultado['servicios_vencidas'] = $stmt->fetchColumn();

        return $resultado;
    }
}
