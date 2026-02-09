<?php
$pageTitle = 'Reparaciones - Sistema RIAAC';
include '../layouts/header.php';

$reparacionModel = new Reparacion($pdo);
$reparaciones = $reparacionModel->obtenerTodos();
?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold"><i class="bi bi-tools"></i> Reparaciones</h2>
                <p class="text-muted">Gestión de servicios técnicos</p>
            </div>
            <div>
                <a href="form.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nueva Reparación
                </a>
            </div>
        </div>
    </div>
</div>

<?php mostrarMensaje(); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Equipo</th>
                                <th>Marca/Modelo</th>
                                <th>Fecha Ingreso</th>
                                <th>Fecha Entrega</th>
                                <th>Estado</th>
                                <th>Garantía</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reparaciones as $rep): ?>
                                <tr>
                                    <td><?php echo $rep['id']; ?></td>
                                    <td><?php echo $rep['cliente_nombre']; ?></td>
                                    <td><?php echo $rep['tipo_equipo']; ?></td>
                                    <td><?php echo ($rep['marca'] ?? '') . ' ' . ($rep['modelo'] ?? ''); ?></td>
                                    <td><?php echo formatearFecha($rep['fecha_ingreso']); ?></td>
                                    <td><?php echo formatearFecha($rep['fecha_entrega']); ?></td>
                                    <td>
                                        <?php
                                        $badges = [
                                            'pendiente' => 'warning',
                                            'en_proceso' => 'info',
                                            'entregado' => 'success',
                                            'cancelado' => 'danger'
                                        ];
                                        $color = $badges[$rep['estado']] ?? 'secondary';
                                        echo '<span class="badge bg-' . $color . '">' . ucfirst(str_replace('_', ' ', $rep['estado'])) . '</span>';
                                        ?>
                                    </td>
                                    <td><?php echo getBadgeGarantia($rep['estado_garantia']); ?></td>
                                    <td>
                                        <a href="form.php?id=<?php echo $rep['id']; ?>"
                                            class="btn btn-sm btn-warning" title="Editar">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                    </td>
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