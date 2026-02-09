<?php
// Cargar configuración ANTES del header
require_once '../../config/config.php';
require_once '../../includes/autoload.php';
require_once '../../includes/helpers.php';
require_once '../../includes/auth.php';

$productoModel = new Producto($pdo);
$pdo->query("SELECT 1 FROM categorias_productos LIMIT 1"); // Para crear el modelo
$pdo->query("SELECT 1 FROM proveedores LIMIT 1"); // Para crear el modelo

$categorias = $pdo->query("SELECT * FROM categorias_productos ORDER BY nombre")->fetchAll();
$proveedores = $pdo->query("SELECT * FROM proveedores WHERE estado = 'activo' ORDER BY nombre")->fetchAll();

$isEdit = isset($_GET['id']);
$producto = null;

if ($isEdit) {
    $producto = $productoModel->obtenerPorId($_GET['id']);
    if (!$producto) {
        setMensaje('Producto no encontrado', 'danger');
        header('Location: index.php');
        exit;
    }
}

// Procesar formulario ANTES de incluir header
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre' => $_POST['nombre'],
        'codigo' => $_POST['codigo'] ?? null,
        'categoria_id' => !empty($_POST['categoria_id']) ? $_POST['categoria_id'] : null,
        'proveedor_id' => !empty($_POST['proveedor_id']) ? $_POST['proveedor_id'] : null,
        'stock' => $_POST['stock'] ?? 0,
        'precio_compra' => $_POST['precio_compra'] ?? null,
        'fecha_compra' => $_POST['fecha_compra'] ?? null,
        'descripcion' => $_POST['descripcion'] ?? null,
        'estado' => $_POST['estado']
    ];

    try {
        if ($isEdit) {
            $productoModel->actualizar($_GET['id'], $datos);
            setMensaje('Producto actualizado exitosamente', 'success');
        } else {
            $productoModel->crear($datos);
            setMensaje('Producto registrado exitosamente', 'success');
        }
        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        setMensaje('Error al guardar el producto: ' . $e->getMessage(), 'danger');
    }
}

// AHORA sí incluir el header
$pageTitle = 'Formulario de Producto - Sistema RIAAC';
include '../layouts/header.php';
?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold">
                    <i class="bi bi-box-seam"></i>
                    <?php echo $isEdit ? 'Editar Producto' : 'Nuevo Producto'; ?>
                </h2>
                <p class="text-muted">Gestión de inventario</p>
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
                            <label for="nombre" class="form-label">Nombre del Producto *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre"
                                value="<?php echo $producto['nombre'] ?? ''; ?>"
                                placeholder="Ej: Mouse inalámbrico Logitech" required>
                        </div>

                        <!-- Código -->
                        <div class="col-md-6 mb-3">
                            <label for="codigo" class="form-label">Código/SKU</label>
                            <input type="text" class="form-control" id="codigo" name="codigo"
                                value="<?php echo $producto['codigo'] ?? ''; ?>"
                                placeholder="Ej: MOUSELOG01">
                        </div>

                        <!-- Categoría -->
                        <div class="col-md-6 mb-3">
                            <label for="categoria_id" class="form-label">Categoría</label>
                            <select class="form-select" id="categoria_id" name="categoria_id">
                                <option value="">Sin categoría</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"
                                        <?php echo ($producto && $producto['categoria_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo $cat['nombre']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Proveedor -->
                        <div class="col-md-6 mb-3">
                            <label for="proveedor_id" class="form-label">Proveedor</label>
                            <select class="form-select" id="proveedor_id" name="proveedor_id">
                                <option value="">Sin proveedor</option>
                                <?php foreach ($proveedores as $prov): ?>
                                    <option value="<?php echo $prov['id']; ?>"
                                        <?php echo ($producto && $producto['proveedor_id'] == $prov['id']) ? 'selected' : ''; ?>>
                                        <?php echo $prov['nombre']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Stock -->
                        <div class="col-md-6 mb-3">
                            <label for="stock" class="form-label">Stock Inicial</label>
                            <input type="number" class="form-control" id="stock" name="stock"
                                value="<?php echo $producto['stock'] ?? '0'; ?>"
                                placeholder="0" min="0">
                            <small class="text-muted">Se actualiza automáticamente con las ventas</small>
                        </div>

                        <!-- Precio Compra -->
                        <div class="col-md-4 mb-3">
                            <label for="precio_compra" class="form-label">Precio de Compra (S/)</label>
                            <input type="number" step="0.01" class="form-control" id="precio_compra" name="precio_compra"
                                value="<?php echo $producto['precio_compra'] ?? ''; ?>"
                                placeholder="0.00">
                        </div>

                        <!-- Fecha de Compra -->
                        <div class="col-md-4 mb-3">
                            <label for="fecha_compra" class="form-label">Fecha de Compra</label>
                            <input type="date" class="form-control" id="fecha_compra" name="fecha_compra"
                                value="<?php echo $producto['fecha_compra'] ?? ''; ?>">
                        </div>

                        <!-- Descripción -->
                        <div class="col-12 mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion"
                                rows="3"><?php echo $producto['descripcion'] ?? ''; ?></textarea>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-12 mb-3">
                            <label for="estado" class="form-label">Estado *</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="activo" <?php echo ($producto && $producto['estado'] == 'activo') ? 'selected' : ''; ?>>Activo</option>
                                <option value="inactivo" <?php echo ($producto && $producto['estado'] == 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Guardar Producto
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>

                            <?php if ($isEdit): ?>
                                <button type="button" class="btn btn-outline-primary" onclick="abrirDocumentos(<?php echo $_GET['id']; ?>)">
                                    <i class="bi bi-files"></i> Gestionar Documentos
                                    <span class="badge bg-primary rounded-pill ms-1" id="docs-count" style="display:none;">0</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>

<?php if ($isEdit): ?>
    <!-- Modal de Documentos -->
    <?php include '../components/modal-documentos.php'; ?>

    <!-- Fancybox 3.x para visor de documentos -->
    <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>

    <!-- Scripts de Documentos -->
    <script src="<?php echo BASE_URL; ?>assets/js/documentos.js"></script>

    <script>
        // Variable global para el producto actual
        var productoActualId = <?php echo $_GET['id']; ?>;

        // Función para abrir documentos
        window.abrirDocumentos = function(productoId) {
            if (!window.documentosManager || window.documentosManager.servicioId !== productoId) {
                window.documentosManager = new DocumentosManager('productos', productoId);
            }

            const modalElement = document.getElementById('modalDocumentos');
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: true
            });

            modal.show();
            window.documentosManager.cargarDocumentos();
        }

        // Función principal del visor de archivos
        window.openfile = function(src, titulo) {
            if (typeof jQuery === 'undefined') {
                console.error('jQuery no está cargado');
                alert('Error: jQuery no está disponible');
                return;
            }

            if (typeof $.fancybox === 'undefined') {
                console.error('Fancybox no está cargado');
                alert('Error: Fancybox no está disponible. Abriendo en nueva pestaña...');
                window.open(BASE_URL + src, '_blank');
                return;
            }

            var extension = src.split('.').pop().toLowerCase();
            var fileUrl = BASE_URL + src;

            function openFancybox(options) {
                $.fancybox.open(options);
                $('.fancybox-container').css('z-index', '999999');
            }

            if (['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'].includes(extension)) {
                var officeUrl = "https://view.officeapps.live.com/op/embed.aspx?src=" + encodeURIComponent(fileUrl) + "&embedded=true";
                openFancybox({
                    src: officeUrl,
                    type: 'iframe',
                    opts: {
                        caption: titulo || 'Documento Office',
                        iframe: {
                            preload: false
                        },
                        buttons: ['fullScreen', 'close']
                    }
                });
            } else if (['zip', 'rar', '7z', 'tar', 'gz'].includes(extension)) {
                window.location.href = fileUrl;
            } else if (['mp4', 'webm', 'ogg', 'avi', 'mov'].includes(extension)) {
                openFancybox({
                    src: fileUrl,
                    type: 'video',
                    opts: {
                        caption: titulo || 'Video',
                        video: {
                            autoplay: false,
                            controls: true
                        },
                        buttons: ['fullScreen', 'close']
                    }
                });
            } else if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(extension)) {
                openFancybox({
                    src: fileUrl,
                    type: 'image',
                    opts: {
                        caption: titulo || 'Imagen',
                        buttons: ['zoom', 'fullScreen', 'close']
                    }
                });
            } else {
                openFancybox({
                    src: fileUrl,
                    type: 'iframe',
                    opts: {
                        caption: titulo || 'Documento',
                        iframe: {
                            preload: false
                        },
                        buttons: ['fullScreen', 'close']
                    }
                });
            }
        }

        // Cargar contador de documentos
        function cargarContador() {
            fetch(BASE_URL + 'controllers/documentos-api.php?action=listar&tipo_servicio=productos&servicio_id=' + productoActualId)
                .then(r => r.json())
                .then(d => {
                    const badge = document.getElementById('docs-count');
                    if (d.success && d.documentos.length > 0) {
                        badge.textContent = d.documentos.length;
                        badge.style.display = 'inline';
                    } else {
                        badge.style.display = 'none';
                    }
                })
                .catch(e => console.error('Error cargando documentos:', e));
        }

        // Cargar contador al inicio
        document.addEventListener('DOMContentLoaded', function() {
            cargarContador();

            // Escuchar eventos de actualización
            document.addEventListener('documentosActualizados', function(e) {
                cargarContador();
            });
        });
    </script>
<?php endif; ?>