<?php
$pageTitle = 'Productos - Sistema RIAAC';
include '../layouts/header.php';

$productoModel = new Producto($pdo);
$productos = $productoModel->obtenerTodos();
?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold"><i class="bi bi-box-seam"></i> Inventario de Productos</h2>
                <p class="text-muted">Gestión de productos y stock</p>
            </div>
            <div>
                <a href="form.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nuevo Producto
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
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Proveedor</th>
                                <th>Stock</th>
                                <th>Precio Compra</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos as $prod): ?>
                                <tr>
                                    <td><?php echo $prod['id']; ?></td>
                                    <td><?php echo $prod['codigo'] ?? '-'; ?></td>
                                    <td><strong><?php echo $prod['nombre']; ?></strong></td>
                                    <td><?php echo $prod['categoria_nombre'] ?? '-'; ?></td>
                                    <td><?php echo $prod['proveedor_nombre'] ?? '-'; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $prod['stock'] > 0 ? 'success' : 'danger'; ?>">
                                            <?php echo $prod['stock']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo formatearMoneda($prod['precio_compra'] ?? 0); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $prod['estado'] === 'activo' ? 'success' : 'danger'; ?>">
                                            <?php echo ucfirst($prod['estado']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button onclick="abrirDocumentos(<?php echo $prod['id']; ?>)"
                                                class="btn btn-sm btn-outline-primary" title="Gestionar Documentos">
                                                <i class="bi bi-files"></i>
                                                <span class="badge bg-primary rounded-pill ms-1"
                                                    id="docs-count-<?php echo $prod['id']; ?>" style="display:none;">0</span>
                                            </button>
                                            <a href="form.php?id=<?php echo $prod['id']; ?>"
                                                class="btn btn-sm btn-outline-warning" title="Editar">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                        </div>
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

<!-- Modal de Documentos -->
<?php include '../components/modal-documentos.php'; ?>

<!-- Fancybox 3.x para visor de documentos -->
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>

<!-- Scripts de Documentos -->
<script src="<?php echo BASE_URL; ?>assets/js/documentos.js"></script>

<script>
    // Variable global para el servicio actual
    var productoActualId = null;

    // Función para abrir documentos desde el listado
    window.abrirDocumentos = function(productoId) {
        productoActualId = productoId;

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
        console.log('openfile llamado con:', src, titulo);

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

        // Archivos de Office → visor online de Microsoft
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

    // Función para cargar contadores de documentos
    function cargarContadores() {
        <?php foreach ($productos as $prod): ?>
            fetch(BASE_URL + 'controllers/documentos-api.php?action=listar&tipo_servicio=productos&servicio_id=<?php echo $prod['id']; ?>')
                .then(r => r.json())
                .then(d => {
                    const badge = document.getElementById('docs-count-<?php echo $prod['id']; ?>');
                    if (d.success && d.documentos.length > 0) {
                        badge.textContent = d.documentos.length;
                        badge.style.display = 'inline';
                    } else {
                        badge.style.display = 'none';
                    }
                })
                .catch(e => console.error('Error cargando documentos:', e));
        <?php endforeach; ?>
    }

    // Cargar contadores al inicio
    document.addEventListener('DOMContentLoaded', function() {
        cargarContadores();

        // Escuchar eventos de actualización de documentos
        document.addEventListener('documentosActualizados', function(e) {
            if (e.detail && e.detail.servicioId) {
                const servicioId = e.detail.servicioId;
                fetch(BASE_URL + 'controllers/documentos-api.php?action=listar&tipo_servicio=productos&servicio_id=' + servicioId)
                    .then(r => r.json())
                    .then(d => {
                        const badge = document.getElementById('docs-count-' + servicioId);
                        if (badge) {
                            if (d.success && d.documentos.length > 0) {
                                badge.textContent = d.documentos.length;
                                badge.style.display = 'inline';
                            } else {
                                badge.style.display = 'none';
                            }
                        }
                    })
                    .catch(e => console.error('Error recargando contador:', e));
            }
        });
    });
</script>