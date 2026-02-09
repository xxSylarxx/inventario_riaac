<?php
$pageTitle = 'Dashboard - Sistema RIAAC';
include '../layouts/header.php';

// Obtener estadísticas
$clienteModel = new Cliente($pdo);
$hostingModel = new HostingDominio($pdo);
$reparacionModel = new Reparacion($pdo);
$ventaModel = new Venta($pdo);

$totalClientes = $clienteModel->contarActivos();
$totalHosting = $hostingModel->contarActivos();
$reparacionesPendientes = $reparacionModel->contarPorEstado('pendiente');
$garantiasVigentes = $reparacionModel->contarGarantiasVigentes() + $ventaModel->contarGarantiasVigentes();

// Obtener alertas de vencimiento
$proximosVencer = $hostingModel->obtenerProximosVencer();

// Obtener últimas reparaciones y ventas
$ultimasReparaciones = $reparacionModel->obtenerRecientes(5);
$ultimasVentas = $ventaModel->obtenerRecientes(5);
?>

<div class="row fade-in">
    <div class="col-12 mb-4">
        <h2 class="fw-bold"><i class="bi bi-speedometer2"></i> Dashboard</h2>
        <p class="text-muted">Resumen general del sistema</p>
    </div>
</div>

<!-- Tarjetas de estadísticas -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Clientes Activos</h6>
                        <h2 class="stat-number mb-0"><?php echo $totalClientes; ?></h2>
                    </div>
                    <div>
                        <i class="bi bi-people-fill" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Hosting Activos</h6>
                        <h2 class="stat-number mb-0"><?php echo $totalHosting; ?></h2>
                    </div>
                    <div>
                        <i class="bi bi-globe" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card stat-card warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Reparaciones Pendientes</h6>
                        <h2 class="stat-number mb-0"><?php echo $reparacionesPendientes; ?></h2>
                    </div>
                    <div>
                        <i class="bi bi-tools" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Garantías Vigentes</h6>
                        <h2 class="stat-number mb-0"><?php echo $garantiasVigentes; ?></h2>
                    </div>
                    <div>
                        <i class="bi bi-shield-check" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alertas de Vencimiento -->
<?php if (count($proximosVencer) > 0): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle-fill"></i> Alertas de Vencimiento</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Dominio</th>
                                    <th>Cliente</th>
                                    <th>Fecha Vencimiento</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($proximosVencer as $hosting): ?>
                                    <tr>
                                        <td><strong><?php echo $hosting['dominio']; ?></strong></td>
                                        <td><?php echo $hosting['cliente_nombre']; ?></td>
                                        <td><?php echo formatearFecha($hosting['fecha_vencimiento']); ?></td>
                                        <td><?php echo getBadgeAlerta($hosting['dias_para_vencer']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Actividad Reciente -->
<div class="row">
    <!-- Últimas Reparaciones -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-tools"></i> Últimas Reparaciones</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Equipo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ultimasReparaciones as $rep): ?>
                                <tr>
                                    <td><?php echo $rep['cliente_nombre']; ?></td>
                                    <td><?php echo $rep['tipo_equipo'] . ' ' . $rep['marca']; ?></td>
                                    <td>
                                        <?php
                                        $badges = [
                                            'pendiente' => 'warning',
                                            'en_proceso' => 'info',
                                            'entregado' => 'success',
                                            'cancelado' => 'danger'
                                        ];
                                        $color = $badges[$rep['estado']] ?? 'secondary';
                                        echo '<span class="badge bg-' . $color . '">' . ucfirst($rep['estado']) . '</span>';
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimas Ventas -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-cart-fill"></i> Últimas Ventas</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cliente</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ultimasVentas as $venta): ?>
                                <tr>
                                    <td><?php echo $venta['producto_nombre']; ?></td>
                                    <td><?php echo $venta['cliente_nombre'] ?? 'Sin cliente'; ?></td>
                                    <td><strong><?php echo formatearMoneda($venta['precio_venta']); ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>