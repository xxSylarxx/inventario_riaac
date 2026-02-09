<?php
// Cargar configuración ANTES del header
require_once '../../config/config.php';
require_once '../../includes/autoload.php';
require_once '../../includes/helpers.php';
require_once '../../includes/auth.php';

$reparacionModel = new Reparacion($pdo);
$clienteModel = new Cliente($pdo);
$clientes = $clienteModel->obtenerActivos();

$isEdit = isset($_GET['id']);
$reparacion = null;

if ($isEdit) {
    $reparacion = $reparacionModel->obtenerPorId($_GET['id']);
    if (!$reparacion) {
        setMensaje('Reparación no encontrada', 'danger');
        header('Location: index.php');
        exit;
    }
}

// Procesar formulario ANTES de incluir header
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'cliente_id' => $_POST['cliente_id'],
        'tipo_equipo' => $_POST['tipo_equipo'],
        'marca' => $_POST['marca'] ?? null,
        'modelo' => $_POST['modelo'] ?? null,
        'falla_reportada' => $_POST['falla_reportada'],
        'trabajo_realizado' => $_POST['trabajo_realizado'] ?? null,
        'fecha_ingreso' => $_POST['fecha_ingreso'],
        'fecha_entrega' => $_POST['fecha_entrega'] ?? null,
        'precio' => $_POST['precio'] ?? null,
        'dias_garantia' => $_POST['dias_garantia'] ?? 0,
        'estado' => $_POST['estado']
    ];

    try {
        if ($isEdit) {
            $reparacionModel->actualizar($_GET['id'], $datos);
            setMensaje('Reparación actualizada exitosamente', 'success');
        } else {
            $reparacionModel->crear($datos);
            setMensaje('Reparación registrada exitosamente', 'success');
        }
        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        setMensaje('Error al guardar la reparación: ' . $e->getMessage(), 'danger');
    }
}

// AHORA sí incluir el header
$pageTitle = 'Formulario de Reparación - Sistema RIAAC';
include '../layouts/header.php';
?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold">
                    <i class="bi bi-tools"></i>
                    <?php echo $isEdit ? 'Editar Reparación' : 'Nueva Reparación'; ?>
                </h2>
                <p class="text-muted">Complete los datos del servicio técnico</p>
            </div>
            <div>
                <a href="index.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
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
                <form method="POST" action="">
                    <div class="row">
                        <!-- Cliente -->
                        <div class="col-md-6 mb-3">
                            <label for="cliente_id" class="form-label">Cliente *</label>
                            <select class="form-select" id="cliente_id" name="cliente_id" required>
                                <option value="">Seleccione un cliente</option>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?php echo $cliente['id']; ?>"
                                        <?php echo ($reparacion && $reparacion['cliente_id'] == $cliente['id']) ? 'selected' : ''; ?>>
                                        <?php echo $cliente['nombre']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tipo de Equipo -->
                        <div class="col-md-6 mb-3">
                            <label for="tipo_equipo" class="form-label">Tipo de Equipo *</label>
                            <select class="form-select" id="tipo_equipo" name="tipo_equipo" required>
                                <option value="PC" <?php echo ($reparacion && $reparacion['tipo_equipo'] == 'PC') ? 'selected' : ''; ?>>PC</option>
                                <option value="Laptop" <?php echo ($reparacion && $reparacion['tipo_equipo'] == 'Laptop') ? 'selected' : ''; ?>>Laptop</option>
                                <option value="Otro" <?php echo ($reparacion && $reparacion['tipo_equipo'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                            </select>
                        </div>

                        <!-- Marca -->
                        <div class="col-md-6 mb-3">
                            <label for="marca" class="form-label">Marca</label>
                            <input type="text" class="form-control" id="marca" name="marca"
                                value="<?php echo $reparacion['marca'] ?? ''; ?>"
                                placeholder="Ej: HP, Dell, Lenovo">
                        </div>

                        <!-- Modelo -->
                        <div class="col-md-6 mb-3">
                            <label for="modelo" class="form-label">Modelo</label>
                            <input type="text" class="form-control" id="modelo" name="modelo"
                                value="<?php echo $reparacion['modelo'] ?? ''; ?>"
                                placeholder="Ej: Pavilion 15">
                        </div>

                        <!-- Falla Reportada -->
                        <div class="col-12 mb-3">
                            <label for="falla_reportada" class="form-label">Falla Reportada *</label>
                            <textarea class="form-control" id="falla_reportada" name="falla_reportada"
                                rows="3" required><?php echo $reparacion['falla_reportada'] ?? ''; ?></textarea>
                        </div>

                        <!-- Trabajo Realizado -->
                        <div class="col-12 mb-3">
                            <label for="trabajo_realizado" class="form-label">Trabajo Realizado</label>
                            <textarea class="form-control" id="trabajo_realizado" name="trabajo_realizado"
                                rows="3"><?php echo $reparacion['trabajo_realizado'] ?? ''; ?></textarea>
                        </div>

                        <!-- Fecha Ingreso -->
                        <div class="col-md-6 mb-3">
                            <label for="fecha_ingreso" class="form-label">Fecha de Ingreso *</label>
                            <input type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso"
                                value="<?php echo $reparacion['fecha_ingreso'] ?? date('Y-m-d'); ?>" required>
                        </div>

                        <!-- Fecha Entrega -->
                        <div class="col-md-6 mb-3">
                            <label for="fecha_entrega" class="form-label">Fecha de Entrega</label>
                            <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega"
                                value="<?php echo $reparacion['fecha_entrega'] ?? ''; ?>">
                        </div>

                        <!-- Precio -->
                        <div class="col-md-4 mb-3">
                            <label for="precio" class="form-label">Precio (S/)</label>
                            <input type="number" step="0.01" class="form-control" id="precio" name="precio"
                                value="<?php echo $reparacion['precio'] ?? ''; ?>"
                                placeholder="0.00">
                        </div>

                        <!-- Días de Garantía -->
                        <div class="col-md-4 mb-3">
                            <label for="dias_garantia" class="form-label">Días de Garantía</label>
                            <input type="number" class="form-control" id="dias_garantia" name="dias_garantia"
                                value="<?php echo $reparacion['dias_garantia'] ?? '30'; ?>"
                                placeholder="30">
                            <small class="text-muted">Por defecto: 30 días</small>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-4 mb-3">
                            <label for="estado" class="form-label">Estado *</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="pendiente" <?php echo ($reparacion && $reparacion['estado'] == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="en_proceso" <?php echo ($reparacion && $reparacion['estado'] == 'en_proceso') ? 'selected' : ''; ?>>En Proceso</option>
                                <option value="entregado" <?php echo ($reparacion && $reparacion['estado'] == 'entregado') ? 'selected' : ''; ?>>Entregado</option>
                                <option value="cancelado" <?php echo ($reparacion && $reparacion['estado'] == 'cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Guardar Reparación
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>