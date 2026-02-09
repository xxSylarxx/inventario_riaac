<?php

/**
 * Modelo Venta
 * Gestión de ventas de productos
 */

class Venta
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtener todas las ventas
     */
    public function obtenerTodos()
    {
        $sql = "SELECT v.*, p.nombre as producto_nombre, c.nombre as cliente_nombre 
                FROM ventas_productos v
                LEFT JOIN productos p ON v.producto_id = p.id
                LEFT JOIN clientes c ON v.cliente_id = c.id
                ORDER BY v.fecha_venta DESC";
        $stmt = $this->pdo->query($sql);
        $ventas = $stmt->fetchAll();

        // Calcular campos de garantía
        foreach ($ventas as &$venta) {
            $venta['fecha_fin_garantia'] = $this->calcularFechaFinGarantia($venta);
            $venta['estado_garantia'] = $this->calcularEstadoGarantia($venta);
        }

        return $ventas;
    }

    /**
     * Obtener venta por ID
     */
    public function obtenerPorId($id)
    {
        $stmt = $this->pdo->prepare("
SELECT v.*, p.nombre as producto_nombre, c.nombre as cliente_nombre
FROM ventas_productos v
LEFT JOIN productos p ON v.producto_id = p.id
LEFT JOIN clientes c ON v.cliente_id = c.id
WHERE v.id = ?
");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Obtener ventas recientes
     */
    public function obtenerRecientes($limite = 5)
    {
        $sql = "SELECT v.*, p.nombre as producto_nombre, c.nombre as cliente_nombre
                FROM ventas_productos v
                LEFT JOIN productos p ON v.producto_id = p.id
                LEFT JOIN clientes c ON v.cliente_id = c.id
                WHERE v.estado = 'activo'
                ORDER BY v.fecha_venta DESC
                LIMIT :limite";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        $ventas = $stmt->fetchAll();

        foreach ($ventas as &$venta) {
            $venta['fecha_fin_garantia'] = $this->calcularFechaFinGarantia($venta);
            $venta['estado_garantia'] = $this->calcularEstadoGarantia($venta);
        }

        return $ventas;
    }

    /**
     * Obtener ventas con garantía vigente
     */
    public function obtenerConGarantiaVigente()
    {
        $sql = "SELECT v.*, p.nombre as producto_nombre, c.nombre as cliente_nombre
                FROM ventas_productos v
                LEFT JOIN productos p ON v.producto_id = p.id
                LEFT JOIN clientes c ON v.cliente_id = c.id
                WHERE v.estado = 'activo'
                ORDER BY v.fecha_venta DESC";
        $stmt = $this->pdo->query($sql);
        $ventas = $stmt->fetchAll();

        $vigentes = [];
        foreach ($ventas as $venta) {
            $venta['fecha_fin_garantia'] = $this->calcularFechaFinGarantia($venta);
            $venta['estado_garantia'] = $this->calcularEstadoGarantia($venta);
            if ($venta['estado_garantia'] === 'vigente') {
                $vigentes[] = $venta;
            }
        }

        return $vigentes;
    }

    /**
     * Crear venta
     */
    public function crear($datos)
    {
        $stmt = $this->pdo->prepare("
INSERT INTO ventas_productos
(producto_id, cliente_id, cantidad, fecha_venta, precio_venta,
precio_compra, dias_garantia, comprobante_url, observaciones, estado)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

        return $stmt->execute([
            $datos['producto_id'],
            $datos['cliente_id'] ?? null,
            $datos['cantidad'],
            $datos['fecha_venta'],
            $datos['precio_venta'],
            $datos['precio_compra'] ?? null,
            $datos['dias_garantia'] ?? 0,
            $datos['comprobante_url'] ?? null,
            $datos['observaciones'] ?? null,
            $datos['estado'] ?? 'activo'
        ]);
    }

    /**
     * Actualizar venta
     */
    public function actualizar($id, $datos)
    {
        $stmt = $this->pdo->prepare("
UPDATE ventas_productos
SET producto_id = ?, cliente_id = ?, cantidad = ?, fecha_venta = ?,
precio_venta = ?, precio_compra = ?, dias_garantia = ?,
comprobante_url = ?, observaciones = ?, estado = ?
WHERE id = ?
");

        return $stmt->execute([
            $datos['producto_id'],
            $datos['cliente_id'] ?? null,
            $datos['cantidad'],
            $datos['fecha_venta'],
            $datos['precio_venta'],
            $datos['precio_compra'] ?? null,
            $datos['dias_garantia'] ?? 0,
            $datos['comprobante_url'] ?? null,
            $datos['observaciones'] ?? null,
            $datos['estado'],
            $id
        ]);
    }

    /**
     * Eliminar venta
     */
    public function eliminar($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM ventas_productos WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Subir comprobante
     */
    public function subirComprobante($archivo)
    {
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        if (!validarExtension($extension)) {
            return ['success' => false, 'message' => 'Extensión de archivo no permitida'];
        }

        if ($archivo['size'] > MAX_FILE_SIZE) {
            return ['success' => false, 'message' => 'El archivo excede el tamaño máximo permitido'];
        }

        $nombreArchivo = generarNombreUnico($extension);
        $rutaDestino = UPLOADS_PATH . $nombreArchivo;

        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            return ['success' => true, 'filename' => $nombreArchivo];
        }

        return ['success' => false, 'message' => 'Error al subir el archivo'];
    }

    /**
     * Contar ventas del día
     */
    public function contarVentasHoy()
    {
        $stmt = $this->pdo->query("
SELECT COUNT(*) FROM ventas_productos
WHERE DATE(fecha_venta) = CURDATE() AND estado = 'activo'
");
        return $stmt->fetchColumn();
    }

    /**
     * Contar garantías vigentes
     */
    public function contarGarantiasVigentes()
    {
        $todas = $this->obtenerConGarantiaVigente();
        return count($todas);
    }

    /**
     * Calcular fecha fin de garantía
     */
    private function calcularFechaFinGarantia($venta)
    {
        if (empty($venta['fecha_venta']) || empty($venta['dias_garantia'])) {
            return null;
        }

        $fecha = new DateTime($venta['fecha_venta']);
        $fecha->modify('+' . $venta['dias_garantia'] . ' days');
        return $fecha->format('Y-m-d');
    }

    /**
     * Calcular estado de garantía
     */
    private function calcularEstadoGarantia($venta)
    {
        if (empty($venta['dias_garantia'])) {
            return 'sin_garantia';
        }

        $fechaFin = $this->calcularFechaFinGarantia($venta);
        if (!$fechaFin) {
            return 'sin_garantia';
        }

        $hoy = date('Y-m-d');
        return ($hoy <= $fechaFin) ? 'vigente' : 'vencida';
    }
}
