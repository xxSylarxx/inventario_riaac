<?php
$pageTitle = 'Garantías - Sistema RIAAC';
include '../layouts/header.php';

$garantiaModel = new Garantia($pdo);

// Obtener filtro
$filtro = $_GET['filtro'] ?? 'todas';

// Obtener garantías según filtro
$garantiasProductos = $garantiaModel->obtenerGarantiasProductos($filtro);
$garantiasServicios = $garantiaModel->obtenerGarantiasServicios($filtro);

// Obtener contadores
$contadores = $garantiaModel->contarPorEstado();
?>

<div class="row">
    <div class="col-12 mb-4">
        <h2 class="fw-bold"><i class="bi bi-shield-check"></i> Gestión de Garantías</h2>
        <p class="text-muted">Vista unificada de garantías de productos y servicios</p>
    </div>
</div>

<!-- Filtros -->
<div class="row mb-4">
    <div class="col-12">
        <div class="btn-group" role="group">
            <a href="?filtro=todas" class="btn btn-<?php echo $filtro === 'todas' ? 'primary' : 'outline-primary'; ?>">
                Todas
            </a>
            <a href="?filtro=vigentes" class="btn btn-<?php echo $filtro === 'vigentes' ? 'success' : 'outline-success'; ?>">
                Vigentes (<?php echo $contadores['productos_vigentes'] + $contadores['servicios_vigentes']; ?>)
            </a>
            <a href="?filtro=por_vencer" class="btn btn-<?php echo $filtro === 'por_vencer' ? 'warning' : 'outline-warning'; ?>">
                Por Vencer (&lt; 30 días)
            </a>
            <a href="?filtro=vencidas" class="btn btn-<?php echo $filtro === 'vencidas' ? 'danger' : 'outline-danger'; ?>">
                Vencidas (<?php echo $contadores['productos_vencidas'] + $contadores['servicios_vencidas']; ?>)
            </a>
        </div>
    </div>
</div>

<!-- Tabs para separar productos y servicios -->
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#productos" type="button">
            <i class="bi bi-box-seam"></i> Productos (<?php echo count($garantiasProductos); ?>)
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#servicios" type="button">
            <i class="bi bi-tools"></i> Servicios (<?php echo count($garantiasServicios); ?>)
        </button>
    </li>
</ul>

<div class="tab-content">
    <!-- Tab Productos -->
    <div class="tab-pane fade show active" id="productos">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Producto</th>
                                <th>Cliente</th>
                                <th>Fecha Venta</th>
                                <th>Fecha Fin Garantía</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($garantiasProductos as $g): ?>
                                <tr>
                                    <td><?php echo $g['id']; ?></td>
                                    <td><strong><?php echo $g['descripcion']; ?></strong></td>
                                    <td><?php echo $g['cliente_nombre'] ?? 'Sin cliente'; ?></td>
                                    <td><?php echo formatearFecha($g['fecha']); ?></td>
                                    <td><?php echo formatearFecha($g['fecha_fin_garantia']); ?></td>
                                    <td><?php echo getBadgeGarantia($g['estado_garantia']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Servicios -->
    <div class="tab-pane fade" id="servicios">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Equipo</th>
                                <th>Cliente</th>
                                <th>Fecha Entrega</th>
                                <th>Fecha Fin Garantía</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($garantiasServicios as $g): ?>
                                <tr>
                                    <td><?php echo $g['id']; ?></td>
                                    <td><strong><?php echo $g['descripcion']; ?></strong></td>
                                    <td><?php echo $g['cliente_nombre']; ?></td>
                                    <td><?php echo formatearFecha($g['fecha']); ?></td>
                                    <td><?php echo formatearFecha($g['fecha_fin_garantia']); ?></td>
                                    <td><?php echo getBadgeGarantia($g['estado_garantia']); ?></td>
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