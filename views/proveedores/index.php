<?php
$pageTitle = 'Proveedores - Sistema RIAAC';
include '../layouts/header.php';

$proveedorModel = new Proveedor($pdo);
$proveedores = $proveedorModel->obtenerTodos();
?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold"><i class="bi bi-truck"></i> Proveedores</h2>
                <p class="text-muted">Gestión de proveedores y contactos</p>
            </div>
            <div>
                <a href="form.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nuevo Proveedor
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
                                <th>Nombre</th>
                                <th>Contacto</th>
                                <th>WhatsApp</th>
                                <th>Email</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($proveedores as $prov): ?>
                                <tr>
                                    <td><?php echo $prov['id']; ?></td>
                                    <td><strong><?php echo $prov['nombre']; ?></strong></td>
                                    <td><?php echo $prov['contacto'] ?? '-'; ?></td>
                                    <td>
                                        <?php if ($prov['whatsapp']): ?>
                                            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $prov['whatsapp']); ?>"
                                                target="_blank" class="text-success">
                                                <i class="bi bi-whatsapp"></i> <?php echo $prov['whatsapp']; ?>
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($prov['email']): ?>
                                            <a href="mailto:<?php echo $prov['email']; ?>">
                                                <?php echo $prov['email']; ?>
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $prov['estado'] === 'activo' ? 'success' : 'danger'; ?>">
                                            <?php echo ucfirst($prov['estado']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="form.php?id=<?php echo $prov['id']; ?>"
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