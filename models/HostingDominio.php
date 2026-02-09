<?php

/**
 * Modelo HostingDominio
 * Gestión de servicios de hosting y dominios
 */

class HostingDominio
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtener hosting/dominio por ID
     */
    public function obtenerPorId($id)
    {
        $stmt = $this->pdo->prepare("
SELECT h.*, c.nombre as cliente_nombre
FROM hosting_dominios h
LEFT JOIN clientes c ON h.cliente_id = c.id
WHERE h.id = ?
");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Obtener todos los hosting/dominios
     */
    public function obtenerTodos()
    {
        $sql = "SELECT h.*, c.nombre as cliente_nombre
                FROM hosting_dominios h
                LEFT JOIN clientes c ON h.cliente_id = c.id
                ORDER BY h.fecha_vencimiento ASC";
        $stmt = $this->pdo->query($sql);
        $hostings = $stmt->fetchAll();

        // Agregar días para vencer calculados
        foreach ($hostings as &$hosting) {
            $hosting['dias_para_vencer'] = $this->calcularDiasParaVencer($hosting['fecha_vencimiento']);
        }

        return $hostings;
    }

    /**
     * Obtener servicios próximos a vencer (30 días o menos)
     */
    public function obtenerProximosVencer()
    {
        $sql = "SELECT h.*, c.nombre as cliente_nombre
                FROM hosting_dominios h
                LEFT JOIN clientes c ON h.cliente_id = c.id
                WHERE h.estado = 'activo'
                ORDER BY h.fecha_vencimiento ASC";
        $stmt = $this->pdo->query($sql);
        $todos = $stmt->fetchAll();

        // Filtrar en PHP solo los que vencen en 30 días o menos
        $proximos = [];
        foreach ($todos as $hosting) {
            $diasParaVencer = $this->calcularDiasParaVencer($hosting['fecha_vencimiento']);
            if ($diasParaVencer !== null && $diasParaVencer <= 30 && $diasParaVencer >= 0) {
                $hosting['dias_para_vencer'] = $diasParaVencer;
                $proximos[] = $hosting;
            }
        }

        return $proximos;
    }

    /**
     * Calcula los días restantes para el vencimiento de una fecha.
     * @param string $fechaVencimiento La fecha de vencimiento en formato 'YYYY-MM-DD'.
     * @return int|null El número de días restantes, o null si la fecha es inválida.
     */
    private function calcularDiasParaVencer($fechaVencimiento)
    {
        try {
            $hoy = new DateTime();
            $vencimiento = new DateTime($fechaVencimiento);
            $intervalo = $hoy->diff($vencimiento);
            return (int)$intervalo->format('%R%a'); // %R para el signo, %a para el número total de días
        } catch (Exception $e) {
            // Manejar error si la fecha es inválida
            return null;
        }
    }

    /**
     * Crear hosting/dominio
     */
    public function crear($datos)
    {
        $stmt = $this->pdo->prepare("
    INSERT INTO hosting_dominios
    (cliente_id, dominio, proveedor, fecha_inicio, fecha_vencimiento,
    precio_compra, precio_venta, observaciones, correos_corporativos, archivo_presupuesto, estado)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

        return $stmt->execute([
            $datos['cliente_id'],
            $datos['dominio'],
            $datos['proveedor'] ?? null,
            $datos['fecha_inicio'],
            $datos['fecha_vencimiento'],
            $datos['precio_compra'] ?? null,
            $datos['precio_venta'] ?? null,
            $datos['observaciones'] ?? null,
            $datos['correos_corporativos'] ?? null,
            $datos['archivo_presupuesto'] ?? null,
            $datos['estado'] ?? 'activo'
        ]);
    }

    /**
     * Actualizar hosting/dominio
     */
    public function actualizar($id, $datos)
    {
        $stmt = $this->pdo->prepare("
    UPDATE hosting_dominios
    SET cliente_id = ?, dominio = ?, proveedor = ?, fecha_inicio = ?,
    fecha_vencimiento = ?, precio_compra = ?, precio_venta = ?,
    observaciones = ?, correos_corporativos = ?, archivo_presupuesto = ?, estado = ?
    WHERE id = ?
    ");

        return $stmt->execute([
            $datos['cliente_id'],
            $datos['dominio'],
            $datos['proveedor'] ?? null,
            $datos['fecha_inicio'],
            $datos['fecha_vencimiento'],
            $datos['precio_compra'] ?? null,
            $datos['precio_venta'] ?? null,
            $datos['observaciones'] ?? null,
            $datos['correos_corporativos'] ?? null,
            $datos['archivo_presupuesto'] ?? null,
            $datos['estado'],
            $id
        ]);
    }

    /**
     * Eliminar hosting/dominio
     */
    public function eliminar($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM hosting_dominios WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Contar servicios activos
     */
    public function contarActivos()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM hosting_dominios WHERE estado = 'activo'");
        return $stmt->fetchColumn();
    }

    /**
     * Generar alertas de vencimiento
     */
    public function generarAlertas()
    {
        // Obtener todos los servicios activos
        $sql = "SELECT id, fecha_vencimiento
                FROM hosting_dominios
                WHERE estado = 'activo'";
        $stmt = $this->pdo->query($sql);
        $servicios = $stmt->fetchAll();

        foreach ($servicios as $servicio) {
            $diasParaVencer = $this->calcularDiasParaVencer($servicio['fecha_vencimiento']);

            // Solo generar alerta si quedan exactamente 30, 15 o 5 días
            if (in_array($diasParaVencer, [30, 15, 5])) {
                // Verificar si ya existe una alerta para este servicio y días
                $stmt = $this->pdo->prepare("
                    SELECT COUNT(*) FROM alertas_vencimiento
                    WHERE tipo = 'hosting' AND referencia_id = ? AND dias_restantes = ?
                ");
                $stmt->execute([$servicio['id'], $diasParaVencer]);

                if ($stmt->fetchColumn() == 0) {
                    // Crear nueva alerta
                    $stmt = $this->pdo->prepare("
                        INSERT INTO alertas_vencimiento (tipo, referencia_id, dias_restantes, estado)
                        VALUES ('hosting', ?, ?, 'pendiente')
                    ");
                    $stmt->execute([$servicio['id'], $diasParaVencer]);
                }
            }
        }
    }
}
