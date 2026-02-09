<?php
$pageTitle = 'Clientes - Sistema RIAAC';
include '../layouts/header.php';

$clienteModel = new Cliente($pdo);
$clientes = $clienteModel->obtenerTodos();
?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold"><i class="bi bi-people-fill"></i> Gestión de Clientes</h2>
                <p class="text-muted">Administra la base de datos de clientes</p>
            </div>
            <div>
                <a href="form.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nuevo Cliente
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
                                <th>Tipo</th>
                                <th>Nombre/Razón Social</th>
                                <th>DNI/RUC</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>WhatsApp</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clientes as $cliente): ?>
                                <tr>
                                    <td><?php echo $cliente['id']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $cliente['tipo'] === 'empresa' ? 'info' : 'secondary'; ?>">
                                            <?php echo ucfirst($cliente['tipo']); ?>
                                        </span>
                                    </td>
                                    <td><strong><?php echo $cliente['nombre']; ?></strong></td>
                                    <td><?php echo $cliente['dni_ruc'] ?? '-'; ?></td>
                                    <td><?php echo $cliente['email'] ?? '-'; ?></td>
                                    <td><?php echo $cliente['telefono'] ?? '-'; ?></td>
                                    <td>
                                        <?php if ($cliente['whatsapp']): ?>
                                            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $cliente['whatsapp']); ?>"
                                                target="_blank" class="text-success">
                                                <i class="bi bi-whatsapp"></i> <?php echo $cliente['whatsapp']; ?>
                                            </a>
                                        <?php else: echo '-';
                                        endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $cliente['estado'] === 'activo' ? 'success' : 'danger'; ?>">
                                            <?php echo ucfirst($cliente['estado']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="form.php?id=<?php echo $cliente['id']; ?>"
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