<?php
// Cargar configuración ANTES del header
require_once '../../config/config.php';
require_once '../../includes/autoload.php';
require_once '../../includes/helpers.php';
require_once '../../includes/auth.php';

$hostingModel = new HostingDominio($pdo);
$clienteModel = new Cliente($pdo);
$clientes = $clienteModel->obtenerActivos();

$isEdit = isset($_GET['id']);
$hosting = null;
$accion = $isEdit ? 'editar' : 'crear'; // Keep 'accion' for HTML display

if ($isEdit) {
    $hosting = $hostingModel->obtenerPorId($_GET['id']);
    if (!$hosting) {
        setMensaje('Servicio de hosting no encontrado', 'danger');
        header('Location: index.php');
        exit;
    }
}

// Procesar formulario ANTES de incluir header
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'cliente_id' => $_POST['cliente_id'],
        'dominio' => $_POST['dominio'],
        'proveedor' => $_POST['proveedor'] ?? null,
        'fecha_inicio' => $_POST['fecha_inicio'],
        'fecha_vencimiento' => $_POST['fecha_vencimiento'],
        'precio_compra' => $_POST['precio_compra'] ?? null,
        'precio_venta' => $_POST['precio_venta'] ?? null,
        'observaciones' => $_POST['observaciones'] ?? null,
        'correos_corporativos' => $_POST['correos_corporativos'] ?? null,
        'estado' => $_POST['estado']
    ];

    // Manejar archivo de presupuesto
    $archivoAnterior = $hosting['archivo_presupuesto'] ?? null;

    if (isset($_FILES['archivo_presupuesto']) && $_FILES['archivo_presupuesto']['error'] !== UPLOAD_ERR_NO_FILE) {
        $resultado = subirArchivo($_FILES['archivo_presupuesto'], 'presupuestos');

        if ($resultado['success']) {
            $datos['archivo_presupuesto'] = $resultado['ruta'];
            // Eliminar archivo anterior si existe
            if ($archivoAnterior) {
                eliminarArchivo($archivoAnterior);
            }
        } else {
            setMensaje('Error al subir archivo: ' . $resultado['error'], 'warning');
        }
    } else {
        // Mantener el archivo anterior si no se sube uno nuevo
        $datos['archivo_presupuesto'] = $archivoAnterior;
    }

    try {
        if ($isEdit) {
            $hostingModel->actualizar($_GET['id'], $datos);
            setMensaje('Servicio actualizado exitosamente', 'success');
        } else {
            $hostingModel->crear($datos);
            setMensaje('Servicio registrado exitosamente', 'success');
        }
        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        setMensaje('Error al guardar el servicio: ' . $e->getMessage(), 'danger');
    }
}

// AHORA sí incluir el header
$pageTitle = 'Formulario de Hosting/Dominio - Sistema RIAAC';
include '../layouts/header.php';
?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold">
                    <i class="bi bi-globe"></i>
                    <?php echo $accion === 'crear' ? 'Nuevo Servicio' : 'Editar Servicio'; ?>
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cliente_id" class="form-label">Cliente</label>
                            <select class="form-select" id="cliente_id" name="cliente_id" required>
                                <option value="">Seleccionar cliente...</option>
                                <?php foreach ($clientes as $c): ?>
                                    <option value="<?php echo $c['id']; ?>"
                                        <?php echo ($hosting['cliente_id'] ?? '') == $c['id'] ? 'selected' : ''; ?>>
                                        <?php echo $c['nombre']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="dominio" class="form-label">Dominio</label>
                            <input type="text" class="form-control" id="dominio" name="dominio"
                                value="<?php echo $hosting['dominio'] ?? ''; ?>"
                                placeholder="ejemplo.com" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="proveedor" class="form-label">Proveedor</label>
                            <input type="text" class="form-control" id="proveedor" name="proveedor"
                                value="<?php echo $hosting['proveedor'] ?? ''; ?>"
                                placeholder="Hostinger, GoDaddy, etc.">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="activo" <?php echo ($hosting['estado'] ?? 'activo') === 'activo' ? 'selected' : ''; ?>>Activo</option>
                                <option value="vencido" <?php echo ($hosting['estado'] ?? '') === 'vencido' ? 'selected' : ''; ?>>Vencido</option>
                                <option value="cancelado" <?php echo ($hosting['estado'] ?? '') === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio"
                                value="<?php echo $hosting['fecha_inicio'] ?? date('Y-m-d'); ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                            <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento"
                                value="<?php echo $hosting['fecha_vencimiento'] ?? ''; ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="precio_compra" class="form-label">Precio de Compra</label>
                            <input type="number" step="0.01" class="form-control" id="precio_compra"
                                name="precio_compra" value="<?php echo $hosting['precio_compra'] ?? ''; ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="precio_venta" class="form-label">Precio de Venta</label>
                            <input type="number" step="0.01" class="form-control" id="precio_venta"
                                name="precio_venta" value="<?php echo $hosting['precio_venta'] ?? ''; ?>">
                        </div>

                        <div class="col-12 mb-3">
                            <label for="correos_corporativos" class="form-label">Correos Corporativos Creados</label>
                            <textarea class="form-control" id="correos_corporativos" name="correos_corporativos" rows="4"
                                placeholder="Ej:&#10;- contacto@ejemplo.com&#10;- ventas@ejemplo.com&#10;- soporte@ejemplo.com"><?php echo $hosting['correos_corporativos'] ?? ''; ?></textarea>
                            <small class="text-muted">Lista los correos corporativos creados para este cliente (uno por línea)</small>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <div id="editor-observaciones" style="min-height: 200px; background: white;"></div>
                            <input type="hidden" name="observaciones" id="observaciones">
                            <small class="text-muted">Detalles adicionales del servicio (incluye hosting, página web, etc.)</small>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="estado" class="form-label">Estado *</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="activo" <?php echo ($hosting && $hosting['estado'] == 'activo') ? 'selected' : ''; ?>>Activo</option>
                                <option value="vencido" <?php echo ($hosting && $hosting['estado'] == 'vencido') ? 'selected' : ''; ?>>Vencido</option>
                                <option value="cancelado" <?php echo ($hosting && $hosting['estado'] == 'cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                            </select>
                        </div>

                        <!-- Sección de Documentos -->
                        <?php if ($isEdit): ?>
                            <div class="col-12 mb-3">
                                <div class="card">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="bi bi-files"></i> Documentos Adjuntos
                                            <span class="badge bg-primary rounded-pill ms-2" id="contador-documentos-form" style="display:none;">0</span>
                                        </h6>
                                        <button type="button" class="btn btn-sm btn-primary" id="btn-gestionar-documentos-form">
                                            <i class="bi bi-plus-circle"></i> Gestionar Documentos
                                        </button>
                                    </div>
                                    <div class="card-body" id="lista-documentos-form">
                                        <div class="text-center text-muted py-3">
                                            <div class="spinner-border spinner-border-sm" role="status">
                                                <span class="visually-hidden">Cargando...</span>
                                            </div>
                                            <p class="mb-0 mt-2">Cargando documentos...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="col-12 mb-3">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i>
                                    <strong>Nota:</strong> Guarda el servicio primero para poder adjuntar documentos.
                                </div>
                            </div>
                        <?php endif; ?>
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


<?php include '../layouts/footer.php'; ?>


<!-- Modal de Documentos -->
<?php include '../components/modal-documentos.php'; ?>

<!-- Quill.js CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<!-- Quill.js JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
    // Inicializar Quill.js para el editor de observaciones
    var quill = new Quill('#editor-observaciones', {
        theme: 'snow',
        placeholder: 'Escribe detalles sobre el servicio: hosting contratado, página web desarrollada, etc...',
        modules: {
            toolbar: [
                [{
                    'header': [1, 2, 3, false]
                }],
                ['bold', 'italic', 'underline', 'strike'],
                [{
                    'list': 'ordered'
                }, {
                    'list': 'bullet'
                }],
                [{
                    'color': []
                }, {
                    'background': []
                }],
                ['link'],
                ['clean']
            ]
        }
    });

    // Cargar contenido existente si estamos editando
    <?php if ($hosting && !empty($hosting['observaciones'])): ?>
        quill.root.innerHTML = <?php echo json_encode($hosting['observaciones']); ?>;
    <?php endif; ?>

    // Antes de enviar el formulario, copiar el contenido de Quill al campo oculto
    document.querySelector('form').addEventListener('submit', function() {
        document.getElementById('observaciones').value = quill.root.innerHTML;
    });

    <?php if ($isEdit): ?>
        // Inicializar gestor de documentos para el formulario
        let documentosManagerForm;
        const servicioId = <?php echo $_GET['id']; ?>;

        document.addEventListener('DOMContentLoaded', function() {
            documentosManagerForm = new DocumentosManager('hosting', servicioId);

            // Cargar y mostrar documentos en la lista del formulario
            cargarDocumentosForm();

            // Botón para abrir modal
            document.getElementById('btn-gestionar-documentos-form')?.addEventListener('click', function() {
                const modal = new bootstrap.Modal(document.getElementById('modalDocumentos'));
                modal.show();
                documentosManagerForm.cargarDocumentos();
            });
        });

        // Función para cargar documentos en la vista del formulario
        async function cargarDocumentosForm() {
            try {
                const response = await fetch(
                    `${BASE_URL}controllers/documentos-api.php?action=listar&tipo_servicio=hosting&servicio_id=${servicioId}`
                );
                const data = await response.json();

                if (data.success) {
                    mostrarDocumentosForm(data.documentos);
                    actualizarContadorForm(data.documentos.length);
                }
            } catch (error) {
                console.error('Error al cargar documentos:', error);
            }
        }

        // Función para mostrar documentos en la lista del formulario
        function mostrarDocumentosForm(documentos) {
            const container = document.getElementById('lista-documentos-form');

            if (documentos.length === 0) {
                container.innerHTML = `
                <div class="text-center text-muted py-3">
                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                    <p class="mb-0 mt-2">No hay documentos adjuntos</p>
                    <small>Haz clic en "Gestionar Documentos" para agregar archivos</small>
                </div>
            `;
                return;
            }

            container.innerHTML = `
            <div class="list-group list-group-flush">
                ${documentos.map(doc => {
                    const ext = doc.ruta_archivo.split('.').pop().toLowerCase();
                    let icon = 'bi-file-earmark';
                    let iconColor = 'text-secondary';
                    
                    // Iconos según tipo de archivo
                    if (['pdf'].includes(ext)) {
                        icon = 'bi-file-earmark-pdf';
                        iconColor = 'text-danger';
                    } else if (['doc', 'docx'].includes(ext)) {
                        icon = 'bi-file-earmark-word';
                        iconColor = 'text-primary';
                    } else if (['xls', 'xlsx'].includes(ext)) {
                        icon = 'bi-file-earmark-excel';
                        iconColor = 'text-success';
                    } else if (['ppt', 'pptx'].includes(ext)) {
                        icon = 'bi-file-earmark-ppt';
                        iconColor = 'text-warning';
                    } else if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(ext)) {
                        icon = 'bi-file-earmark-image';
                        iconColor = 'text-info';
                    } else if (['mp4', 'webm', 'ogg', 'avi', 'mov'].includes(ext)) {
                        icon = 'bi-file-earmark-play';
                        iconColor = 'text-purple';
                    } else if (['zip', 'rar', '7z', 'tar', 'gz'].includes(ext)) {
                        icon = 'bi-file-earmark-zip';
                        iconColor = 'text-dark';
                    }
                    
                   return `
  <div class="list-group-item d-flex align-items-center justify-content-between">
    
    <div class="d-flex align-items-center flex-grow-1">
      <i class="bi ${icon} ${iconColor} fs-4 me-3"></i>
      
      <div>
        <h6 class="mb-0">
          ${escapeHtml(doc.nombre_original)}
        </h6>
        
        <small class="text-muted">
          <span class="badge bg-${getBadgeColor(doc.tipo_documento)}">
            ${getTipoLabel(doc.tipo_documento)}
          </span>
          · ${formatearFecha(doc.fecha_subida)}
        </small>
      </div>
    </div>

    <div class="btn-group">
      <button
        type="button"
        onclick="verDocumento('${doc.ruta_archivo}', '${escapeHtml(doc.nombre_original)}')"
        class="btn btn-sm btn-outline-primary"
        title="Ver documento">
        <i class="bi bi-eye"></i>
      </button>

      <a
        href="${BASE_URL}${doc.ruta_archivo}"
        download
        class="btn btn-sm btn-outline-success"
        title="Descargar">
        <i class="bi bi-download"></i>
      </a>
    </div>

  </div>
`;


                }).join('')}
            </div>
        `;
        }

        // Función para ver documento en modal (adaptada de helpdesk)
        function verDocumento(ruta, nombre) {
            openfile(ruta, nombre);
        }

        // Función principal del visor de archivos (del proyecto helpdesk)
        function openfile(src, titulo) {
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
        }

        // Actualizar contador de documentos
        function actualizarContadorForm(cantidad) {
            const badge = document.getElementById('contador-documentos-form');
            if (badge) {
                badge.textContent = cantidad;
                badge.style.display = cantidad > 0 ? 'inline' : 'none';
            }
        }

        // Funciones auxiliares
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function getBadgeColor(tipo) {
            const colors = {
                presupuesto: 'warning',
                contrato: 'primary',
                factura: 'success',
                comprobante: 'info',
                otro: 'secondary'
            };
            return colors[tipo] || 'secondary';
        }

        function getTipoLabel(tipo) {
            const labels = {
                presupuesto: 'Presupuesto',
                contrato: 'Contrato',
                factura: 'Factura',
                comprobante: 'Comprobante',
                otro: 'Otro'
            };
            return labels[tipo] || tipo;
        }

        function formatearFecha(fecha) {
            return new Date(fecha).toLocaleDateString('es-PE');
        }

        // Escuchar eventos de actualización de documentos desde el modal
        document.addEventListener('documentosActualizados', function() {
            cargarDocumentosForm();
        });
    <?php endif; ?>
</script>

<!-- Hosting.js debe cargarse DESPUÉS del footer (donde está jQuery) -->
<script src="<?php echo BASE_URL; ?>assets/js/hosting.js"></script>

<!-- Fancybox 3.x para visor de documentos (compatible con helpdesk) - DESPUÉS de jQuery -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>

<!-- Documentos.js debe cargarse DESPUÉS de Fancybox -->
<script src="<?php echo BASE_URL; ?>assets/js/documentos.js"></script>