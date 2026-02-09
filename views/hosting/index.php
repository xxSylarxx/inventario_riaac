<?php
$pageTitle = 'Hosting y Dominios - Sistema RIAAC';
include '../layouts/header.php';

$hostingModel = new HostingDominio($pdo);
$hosting = $hostingModel->obtenerTodos();
?>
<!-- <style>
    .txt-compact {
      display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    }
</style> -->

<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold"><i class="bi bi-globe"></i> Hosting y Dominios</h2>
                <p class="text-muted">Gestión de servicios de hosting y dominios</p>
            </div>
            <div>
                <a href="form.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nuevo Servicio
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
                                <th>Dominio</th>
                                <th>Cliente</th>
                                <th>Proveedor</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Venc.</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($hosting as $h): ?>
                                <tr>
                                    <td><?php echo $h['id']; ?></td>
                                    <td style="width:110px;"><strong><?php echo $h['dominio']; ?></strong></td>
                                    <td class="txt-compact"><?php echo mb_strimwidth($h['cliente_nombre'], 0, 15, "..."); ?></td>
                                    <td><?php echo $h['proveedor'] ?? '-'; ?></td>
                                    <td><?php echo formatearFecha($h['fecha_inicio']); ?></td>
                                    <td><?php echo formatearFecha($h['fecha_vencimiento']); ?></td>
                                    <td>
                                        <?php echo getBadgeAlerta($h['dias_para_vencer']); ?>
                                        <span class="badge bg-<?php echo $h['estado'] === 'activo' ? 'success' : 'danger'; ?>">
                                            <?php echo ucfirst($h['estado']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button onclick="abrirDocumentos(<?php echo $h['id']; ?>)"
                                                class="btn btn-sm btn-outline-primary" title="Gestionar Documentos">
                                                <i class="bi bi-files"></i>
                                                <span class="badge bg-primary rounded-pill ms-1" id="docs-count-<?php echo $h['id']; ?>" style="display:none;">0</span>
                                            </button>
                                            <a href="form.php?id=<?php echo $h['id']; ?>"
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

<!-- Modal de Documentos (reutilizable para todos los registros) -->
<?php include '../components/modal-documentos.php'; ?>


<!-- Scripts de Documentos -->
<script src="<?php echo BASE_URL; ?>assets/js/documentos.js"></script>

<!-- Fancybox 3.x para visor de documentos - DESPUÉS de jQuery -->
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>

<script>
    // Variables globales (ya declaradas en documentos.js)
    // let documentosManager;
    var servicioActualId = null;

    // Función para abrir documentos desde el listado
    window.abrirDocumentos = function(servicioId) {
        servicioActualId = servicioId;

        if (!window.documentosManager || window.documentosManager.servicioId !== servicioId) {
            window.documentosManager = new DocumentosManager('hosting', servicioId);
        }

        const modalElement = document.getElementById('modalDocumentos');
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: true
        });

        modal.show();
        window.documentosManager.cargarDocumentos();
    }


    // Función principal del visor de archivos (del proyecto helpdesk)
    window.openfile = function(src, titulo) {
        console.log('openfile llamado con:', src, titulo);

        // Verificar que jQuery y Fancybox estén disponibles
        if (typeof jQuery === 'undefined') {
            console.error('jQuery no está cargado');
            alert('Error: jQuery no está disponible');
            return;
        }

        console.log('jQuery está disponible, versión:', jQuery.fn.jquery);
        console.log('Fancybox disponible:', typeof $.fancybox);

        if (typeof $.fancybox === 'undefined') {
            console.error('Fancybox no está cargado');
            alert('Error: Fancybox no está disponible. Abriendo en nueva pestaña...');
            window.open(BASE_URL + src, '_blank');
            return;
        }

        var extension = src.split('.').pop().toLowerCase();
        var fileUrl = BASE_URL + src;

        console.log('Extensión:', extension);
        console.log('URL completa:', fileUrl);

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
    }

    // Función para cargar contadores de documentos
    function cargarContadores() {
        <?php foreach ($hosting as $h): ?>
            fetch(BASE_URL + 'controllers/documentos-api.php?action=listar&tipo_servicio=hosting&servicio_id=<?php echo $h['id']; ?>')
                .then(r => r.json())
                .then(d => {
                    const badge = document.getElementById('docs-count-<?php echo $h['id']; ?>');
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

    // Cargar contadores de documentos al inicio
    document.addEventListener('DOMContentLoaded', function() {
        cargarContadores();

        // Escuchar eventos de actualización de documentos
        document.addEventListener('documentosActualizados', function(e) {
            // Recargar contador solo del servicio actualizado
            if (e.detail && e.detail.servicioId) {
                const servicioId = e.detail.servicioId;
                fetch(BASE_URL + 'controllers/documentos-api.php?action=listar&tipo_servicio=hosting&servicio_id=' + servicioId)
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