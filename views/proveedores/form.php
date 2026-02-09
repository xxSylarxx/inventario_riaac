<?php
// Cargar configuración ANTES del header
require_once '../../config/config.php';
require_once '../../includes/autoload.php';
require_once '../../includes/helpers.php';
require_once '../../includes/auth.php';

$proveedorModel = new Proveedor($pdo);

$isEdit = isset($_GET['id']);
$proveedor = null;

if ($isEdit) {
    $proveedor = $proveedorModel->obtenerPorId($_GET['id']);
    if (!$proveedor) {
        setMensaje('Proveedor no encontrado', 'danger');
        header('Location: index.php');
        exit;
    }
}

// Procesar formulario ANTES de incluir header
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre' => $_POST['nombre'],
        'contacto' => $_POST['contacto'] ?? null,
        'whatsapp' => $_POST['whatsapp'] ?? null,
        'email' => $_POST['email'] ?? null,
        'direccion' => $_POST['direccion'] ?? null,
        'estado' => $_POST['estado']
    ];

    try {
        if ($isEdit) {
            $proveedorModel->actualizar($_GET['id'], $datos);
            setMensaje('Proveedor actualizado exitosamente', 'success');
        } else {
            $proveedorModel->crear($datos);
            setMensaje('Proveedor registrado exitosamente', 'success');
        }
        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        setMensaje('Error al guardar el proveedor: ' . $e->getMessage(), 'danger');
    }
}

// AHORA sí incluir el header
$pageTitle = 'Formulario de Proveedor - Sistema RIAAC';
include '../layouts/header.php';
?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold">
                    <i class="bi bi-truck"></i>
                    <?php echo $isEdit ? 'Editar Proveedor' : 'Nuevo Proveedor'; ?>
                </h2>
                <p class="text-muted">Gestión de proveedores</p>
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
                        <!-- Nombre -->
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre del Proveedor *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre"
                                value="<?php echo $proveedor['nombre'] ?? ''; ?>"
                                placeholder="Ej: Distribuidora ABC S.A.C." required>
                        </div>

                        <!-- Contacto -->
                        <div class="col-md-6 mb-3">
                            <label for="contacto" class="form-label">Persona de Contacto</label>
                            <input type="text" class="form-control" id="contacto" name="contacto"
                                value="<?php echo $proveedor['contacto'] ?? ''; ?>"
                                placeholder="Ej: Juan Pérez">
                        </div>

                        <!-- WhatsApp -->
                        <div class="col-md-6 mb-3">
                            <label for="whatsapp" class="form-label">WhatsApp</label>
                            <input type="text" class="form-control" id="whatsapp" name="whatsapp"
                                value="<?php echo $proveedor['whatsapp'] ?? ''; ?>"
                                placeholder="Ej: +51 999 999 999">
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> Incluye código de país para WhatsApp
                            </small>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?php echo $proveedor['email'] ?? ''; ?>"
                                placeholder="Ej: contacto@proveedor.com">
                        </div>

                        <!-- Dirección -->
                        <div class="col-12 mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <textarea class="form-control" id="direccion" name="direccion"
                                rows="2" placeholder="Dirección completa del proveedor"><?php echo $proveedor['direccion'] ?? ''; ?></textarea>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-12 mb-3">
                            <label for="estado" class="form-label">Estado *</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="activo" <?php echo ($proveedor && $proveedor['estado'] == 'activo') ? 'selected' : ''; ?>>Activo</option>
                                <option value="inactivo" <?php echo ($proveedor && $proveedor['estado'] == 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Guardar Proveedor
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