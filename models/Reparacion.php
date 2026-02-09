<?php

/**
 * Modelo Reparacion
 * Gestión de servicios técnicos de reparación
 */

class Reparacion
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtener todos los registros con información del cliente
     */
    public function obtenerTodos()
    {
        $sql = "SELECT r.*, c.nombre as cliente_nombre
                FROM reparaciones r
                LEFT JOIN clientes c ON r.cliente_id = c.id
                ORDER BY r.fecha_ingreso DESC";
        $stmt = $this->pdo->query($sql);
        $reparaciones = $stmt->fetchAll();

        // Calcular campos de garantía en PHP
        foreach ($reparaciones as &$rep) {
            $rep['fecha_fin_garantia'] = $this->calcularFechaFinGarantia($rep);
            $rep['estado_garantia'] = $this->calcularEstadoGarantia($rep);
        }

        return $reparaciones;
    }

    /**
     * Obtener reparación por ID
     */
    public function obtenerPorId($id)
    {
        $stmt = $this->pdo->prepare("
SELECT r.*, c.nombre as cliente_nombre
FROM reparaciones r
LEFT JOIN clientes c ON r.cliente_id = c.id
WHERE r.id = ?
");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Obtener reparaciones recientes (últimas 10)
     */
    public function obtenerRecientes($limite = 5)
    {
        $sql = "SELECT r.*, c.nombre as cliente_nombre 
                FROM reparaciones r
                LEFT JOIN clientes c ON r.cliente_id = c.id
                WHERE r.estado = 'entregado'
                ORDER BY r.fecha_entrega DESC
                LIMIT :limite";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        $reparaciones = $stmt->fetchAll();

        // Calcular campos de garantía
        foreach ($reparaciones as &$rep) {
            $rep['fecha_fin_garantia'] = $this->calcularFechaFinGarantia($rep);
            $rep['estado_garantia'] = $this->calcularEstadoGarantia($rep);
        }

        return $reparaciones;
    }

    /**
     * Obtener reparaciones con garantía vigente
     */
    public function obtenerConGarantiaVigente()
    {
        $sql = "SELECT r.*, c.nombre as cliente_nombre 
                FROM reparaciones r
                LEFT JOIN clientes c ON r.cliente_id = c.id
                WHERE r.fecha_entrega IS NOT NULL
                ORDER BY r.fecha_entrega DESC";
        $stmt = $this->pdo->query($sql);
        $reparaciones = $stmt->fetchAll();

        // Filtrar solo las vigentes
        $vigentes = [];
        foreach ($reparaciones as $rep) {
            $rep['fecha_fin_garantia'] = $this->calcularFechaFinGarantia($rep);
            $rep['estado_garantia'] = $this->calcularEstadoGarantia($rep);
            if ($rep['estado_garantia'] === 'vigente') {
                $vigentes[] = $rep;
            }
        }

        return $vigentes;
    }

    /**
     * Crear reparación
     */
    public function crear($datos)
    {
        $stmt = $this->pdo->prepare("
INSERT INTO reparaciones
(cliente_id, tipo_equipo, marca, modelo, falla_reportada, trabajo_realizado,
fecha_ingreso, fecha_entrega, precio, dias_garantia, estado)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

        return $stmt->execute([
            $datos['cliente_id'],
            $datos['tipo_equipo'],
            $datos['marca'] ?? null,
            $datos['modelo'] ?? null,
            $datos['falla_reportada'],
            $datos['trabajo_realizado'] ?? null,
            $datos['fecha_ingreso'],
            $datos['fecha_entrega'] ?? null,
            $datos['precio'] ?? null,
            $datos['dias_garantia'] ?? 0,
            $datos['estado'] ?? 'pendiente'
        ]);
    }

    /**
     * Actualizar reparación
     */
    public function actualizar($id, $datos)
    {
        $stmt = $this->pdo->prepare("
UPDATE reparaciones
SET cliente_id = ?, tipo_equipo = ?, marca = ?, modelo = ?,
falla_reportada = ?, trabajo_realizado = ?, fecha_ingreso = ?,
fecha_entrega = ?, precio = ?, dias_garantia = ?, estado = ?
WHERE id = ?
");

        return $stmt->execute([
            $datos['cliente_id'],
            $datos['tipo_equipo'],
            $datos['marca'] ?? null,
            $datos['modelo'] ?? null,
            $datos['falla_reportada'],
            $datos['trabajo_realizado'] ?? null,
            $datos['fecha_ingreso'],
            $datos['fecha_entrega'] ?? null,
            $datos['precio'] ?? null,
            $datos['dias_garantia'] ?? 0,
            $datos['estado'],
            $id
        ]);
    }

    /**
     * Eliminar reparación
     */
    public function eliminar($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM reparaciones WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Contar reparaciones por estado
     */
    public function contarPorEstado($estado)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM reparaciones WHERE estado = ?");
        $stmt->execute([$estado]);
        return $stmt->fetchColumn();
    }

    /**
     * Contar garantías vigentes
     */
    public function contarGarantiasVigentes()
    {
        $sql = "SELECT COUNT(*) as total 
                FROM reparaciones 
                WHERE fecha_entrega IS NOT NULL";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch();

        // Obtener todas y filtrar en PHP
        $todas = $this->obtenerConGarantiaVigente();
        return count($todas);
    }

    /**
     * Calcular fecha fin de garantía
     */
    private function calcularFechaFinGarantia($reparacion)
    {
        if (empty($reparacion['fecha_entrega'])) {
            return null;
        }

        $fecha = new DateTime($reparacion['fecha_entrega']);
        $fecha->modify('+' . $reparacion['dias_garantia'] . ' days');
        return $fecha->format('Y-m-d');
    }

    /**
     * Calcular estado de garantía
     */
    private function calcularEstadoGarantia($reparacion)
    {
        if (empty($reparacion['fecha_entrega'])) {
            return 'sin_entrega';
        }

        $fechaFin = $this->calcularFechaFinGarantia($reparacion);
        $hoy = date('Y-m-d');

        return ($hoy <= $fechaFin) ? 'vigente' : 'vencida';
    }
}
