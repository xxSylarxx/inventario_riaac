<?php
$pageTitle = 'Ventas - Sistema RIAAC';
include '../layouts/header.php';

$ventaModel = new Venta($pdo);
$ventas = $ventaModel->obtenerTodos();
?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold"><i class="bi bi-cart-fill"></i> Ventas de Productos</h2>
                <p class="text-muted">Historial de ventas realizadas</p>
            </div>
            <div>
                <a href="form.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nueva Venta
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
                                <th>Fecha</th>
                                <th>Producto</th>
                                <th>Cliente</th>
                                <th>Cant.</th>
                                <th>Precio Venta</th>
                                <th>Garantía</th>
                                <!--  <th>Comprobante</th> -->
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ventas as $venta): ?>
                                <tr>
                                    <td><?php echo $venta['id']; ?></td>
                                    <td><?php echo formatearFecha($venta['fecha_venta']); ?></td>
                                    <td><strong><?php echo $venta['producto_nombre']; ?></strong></td>
                                    <td><?php echo $venta['cliente_nombre'] ?? 'Sin cliente'; ?></td>
                                    <td><?php echo $venta['cantidad']; ?></td>
                                    <td><?php echo formatearMoneda($venta['precio_venta']); ?></td>
                                    <td><?php echo getBadgeGarantia($venta['estado_garantia']); ?></td>
                                    <!-- <td>
                                        <//?php if ($venta['comprobante_url']): ?>
                                            <a href="<//?php echo BASE_URL . 'uploads/' . $venta['comprobante_url']; ?>"
                                                target="_blank" class="btn btn-sm btn-info">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                            </a>
                                        <//?php else: ?>
                                            -
                                        <//?php endif; ?>
                                    </td> -->
                                    <td>
                                        <a href="form.php?id=<?php echo $venta['id']; ?>"
                                            class="btn btn-sm btn-warning" title="Editar">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <button onclick="abrirDocumentos(<?php echo $venta['id']; ?>)"
                                            class="btn btn-sm btn-outline-primary" title="Gestionar Documentos">
                                            <i class="bi bi-files"></i>
                                            <span class="badge bg-primary rounded-pill ms-1"
                                                id="badge-doc-<?php echo $venta['id']; ?>"
                                                style="display:none;">0</span>
                                        </button>
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
<!-- Modal de Documentos (reutilizable para todos los registros) -->
<?php include '../components/modal-documentos.php'; ?>

<!-- Scripts de Documentos -->
<script src="<?php echo BASE_URL; ?>assets/js/documentos.js"></script>

<!-- Fancybox 3.x para visor de documentos - DESPUÉS de jQuery -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>

<script>
    // Función para abrir documentos desde el listado
    window.abrirDocumentos = function(servicioId) {
        // Crear o actualizar el gestor de documentos
        if (!window.documentosManager || window.documentosManager.servicioId !== servicioId) {
            window.documentosManager = new DocumentosManager('venta', servicioId);
        }

        const modalElement = document.getElementById('modalDocumentos');
        if (!modalElement) {
            console.error('Modal de documentos no encontrado');
            return;
        }

        const modal = new bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: true
        });

        modal.show();
        window.documentosManager.cargarDocumentos();
    };

    // Función principal del visor de archivos
    window.openfile = function(src, titulo) {
        console.log('openfile llamado con:', src, titulo);

        // Verificar que jQuery y Fancybox estén disponibles
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

        // Helper para abrir Fancybox
        function openFancybox(options) {
            $.fancybox.open(options);
            $('.fancybox-container').css('z-index', '999999999999');
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
            // Archivos ZIP → descarga directa
        } else if (['zip', 'rar', '7z', 'tar', 'gz', 'tgz', 'bz2', 'cab', 'iso'].includes(extension)) {
            window.location.href = fileUrl;
            // Videos → reproductor Fancybox
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
            // Imágenes → galería Fancybox
        } else if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(extension)) {
            openFancybox({
                src: fileUrl,
                type: 'image',
                opts: {
                    caption: titulo || 'Imagen',
                    buttons: ['zoom', 'fullScreen', 'close']
                }
            });
            // PDFs y otros → iframe
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
    };

    // Función para cargar contadores de documentos
    function cargarContadores() {
        <?php foreach ($ventas as $v): ?>
            fetch(BASE_URL + 'controllers/documentos-api.php?action=listar&tipo_servicio=venta&servicio_id=<?php echo $v['id']; ?>')
                .then(r => r.json())
                .then(d => {
                    const badge = document.getElementById('badge-doc-<?php echo $v['id']; ?>');
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
                fetch(BASE_URL + 'controllers/documentos-api.php?action=listar&tipo_servicio=venta&servicio_id=' + servicioId)
                    .then(r => r.json())
                    .then(d => {
                        const badge = document.getElementById('badge-doc-' + servicioId);
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