<?php
// Cargar configuración ANTES del header
require_once '../../config/config.php';
require_once '../../includes/autoload.php';
require_once '../../includes/helpers.php';
require_once '../../includes/auth.php';

$clienteModel = new Cliente($pdo);
$cliente = null;
$accion = 'crear';

// Si hay ID, estamos editando
if (isset($_GET['id'])) {
    $accion = 'editar';
    $cliente = $clienteModel->obtenerPorId($_GET['id']);
    if (!$cliente) {
        setMensaje('Cliente no encontrado', 'danger');
        header('Location: index.php');
        exit;
    }
}

// Procesar formulario ANTES de incluir header
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'tipo' => sanitize($_POST['tipo']),
        'nombre' => sanitize($_POST['nombre']),
        'dni_ruc' => sanitize($_POST['dni_ruc']),
        'email' => sanitize($_POST['email']),
        'telefono' => sanitize($_POST['telefono']),
        'whatsapp' => sanitize($_POST['whatsapp']),
        'direccion' => sanitize($_POST['direccion']),
        'ubicacion_google_maps' => sanitize($_POST['ubicacion_google_maps']),
        'estado' => sanitize($_POST['estado'])
    ];

    if ($accion === 'crear') {
        if ($clienteModel->crear($datos)) {
            setMensaje('Cliente creado exitosamente', 'success');
            header('Location: index.php');
            exit;
        } else {
            setMensaje('Error al crear cliente', 'danger');
        }
    } else {
        if ($clienteModel->actualizar($_GET['id'], $datos)) {
            setMensaje('Cliente actualizado exitosamente', 'success');
            header('Location: index.php');
            exit;
        } else {
            setMensaje('Error al actualizar cliente', 'danger');
        }
    }
}

// AHORA sí incluir el header
$pageTitle = 'Cliente - Sistema RIAAC';
include '../layouts/header.php';
?>

<div class="row">
    <div class="col-12 mb-4">
        <h2 class="fw-bold">
            <i class="bi bi-person-<?php echo $accion === 'crear' ? 'plus' : 'gear'; ?>-fill"></i>
            <?php echo $accion === 'crear' ? 'Nuevo Cliente' : 'Editar Cliente'; ?>
        </h2>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tipo" class="form-label">Tipo de Cliente</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="persona" <?php echo ($cliente['tipo'] ?? '') === 'persona' ? 'selected' : ''; ?>>Persona</option>
                                <option value="empresa" <?php echo ($cliente['tipo'] ?? '') === 'empresa' ? 'selected' : ''; ?>>Empresa</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre / Razón Social</label>
                            <input type="text" class="form-control" id="nombre" name="nombre"
                                value="<?php echo $cliente['nombre'] ?? ''; ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="dni_ruc" class="form-label">DNI / RUC</label>
                            <input type="text" class="form-control" id="dni_ruc" name="dni_ruc"
                                value="<?php echo $cliente['dni_ruc'] ?? ''; ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?php echo $cliente['email'] ?? ''; ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono"
                                value="<?php echo $cliente['telefono'] ?? ''; ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="whatsapp" class="form-label">WhatsApp</label>
                            <input type="text" class="form-control" id="whatsapp" name="whatsapp"
                                value="<?php echo $cliente['whatsapp'] ?? ''; ?>">
                        </div>

                        <div class="col-12 mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="2"><?php echo $cliente['direccion'] ?? ''; ?></textarea>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="ubicacion_google_maps" class="form-label">
                                <i class="bi bi-geo-alt-fill text-danger"></i> Ubicación Google Maps
                            </label>
                            <div class="input-group">
                                <input type="url" class="form-control" id="ubicacion_google_maps" name="ubicacion_google_maps"
                                    value="<?php echo $cliente['ubicacion_google_maps'] ?? ''; ?>"
                                    placeholder="https://maps.google.com/?q=...">
                                <button class="btn btn-outline-secondary" type="button" id="btn-abrir-mapa"
                                    onclick="abrirUbicacion()" title="Abrir en Google Maps">
                                    <i class="bi bi-box-arrow-up-right"></i> Abrir
                                </button>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> Pega el enlace de Google Maps de la ubicación del cliente
                            </small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="activo" <?php echo ($cliente['estado'] ?? 'activo') === 'activo' ? 'selected' : ''; ?>>Activo</option>
                                <option value="inactivo" <?php echo ($cliente['estado'] ?? '') === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function abrirUbicacion() {
        const url = document.getElementById('ubicacion_google_maps').value;
        if (url && url.trim() !== '') {
            window.open(url, '_blank');
        } else {
            alert('Por favor, ingresa primero una URL de Google Maps');
        }
    }
</script>

<?php include '../layouts/footer.php'; ?>