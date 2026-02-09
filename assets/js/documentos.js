/**
 * Gestión de Documentos - JavaScript
 * Manejo de modal y operaciones AJAX con subida múltiple
 */

class DocumentosManager {
  constructor(tipoServicio, servicioId) {
    this.tipoServicio = tipoServicio;
    this.servicioId = servicioId;
    this.apiUrl = BASE_URL + "controllers/documentos-api.php";
    this.init();
  }

  init() {
    // Solo vincular eventos una vez globalmente
    if (!DocumentosManager.eventsInitialized) {
      this.bindEvents();
      DocumentosManager.eventsInitialized = true;
    }
    if (this.servicioId) {
      this.cargarDocumentos();
    }
  }

  bindEvents() {
    // Botón para abrir modal
    document
      .getElementById("btn-gestionar-documentos")
      ?.addEventListener("click", () => {
        this.abrirModal();
      });

    // Formulario de subida - usar window.documentosManager para acceder a la instancia actual
    document
      .getElementById("form-subir-documento")
      ?.addEventListener("submit", (e) => {
        e.preventDefault();
        if (window.documentosManager) {
          window.documentosManager.subirDocumentos();
        }
      });

    // Preview de archivos seleccionados
    document
      .getElementById("archivos-input")
      ?.addEventListener("change", (e) => {
        if (window.documentosManager) {
          window.documentosManager.mostrarPreviewArchivos(e.target.files);
        }
      });
  }

  mostrarPreviewArchivos(files) {
    const preview = document.getElementById("preview-archivos");
    const lista = document.getElementById("lista-preview");
    const btnSubir = document.getElementById("btn-subir-docs");

    if (files.length === 0) {
      preview.style.display = "none";
      if (btnSubir) btnSubir.disabled = true;
      return;
    }

    preview.style.display = "block";
    lista.innerHTML = `
                ${Array.from(files)
                  .map(
                    (file) => `
                    <div class="d-flex align-items-center mb-1">
                        <i class="bi bi-file-earmark-text text-primary me-2"></i> 
                        <span>${file.name}</span>
                        <small class="text-muted ms-2">(${this.formatBytes(file.size)})</small>
                    </div>
                `,
                  )
                  .join("")}
        `;

    // Habilitar botón de subir
    if (btnSubir) btnSubir.disabled = false;
  }

  formatBytes(bytes) {
    if (bytes === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + " " + sizes[i];
  }

  abrirModal() {
    const modal = new bootstrap.Modal(
      document.getElementById("modalDocumentos"),
    );
    modal.show();
    this.cargarDocumentos();
  }

  async cargarDocumentos() {
    if (!this.servicioId) return;

    try {
      const response = await fetch(
        `${this.apiUrl}?action=listar&tipo_servicio=${this.tipoServicio}&servicio_id=${this.servicioId}`,
      );
      const data = await response.json();

      if (data.success) {
        this.renderizarDocumentos(data.documentos);
        this.actualizarContador(data.documentos.length);
      }
    } catch (error) {
      console.error("Error al cargar documentos:", error);
    }
  }

  renderizarDocumentos(documentos) {
    const container = document.getElementById("lista-documentos");

    if (documentos.length === 0) {
      container.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                    <p class="mt-2">No hay documentos adjuntos</p>
                </div>
            `;
      return;
    }

    container.innerHTML = documentos
      .map((doc) => {
        const iconInfo = this.getFileIcon(doc.ruta_archivo);
        return `
            <div class="card mb-2">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center flex-grow-1">
                            <i class="bi ${iconInfo.icon} ${iconInfo.color} fs-3 me-3"></i>
                            <div>
                                <h6 class="mb-0">${this.escapeHtml(doc.nombre_original)}</h6>
                                <small class="text-muted">
                                    <span class="badge bg-${this.getBadgeColor(doc.tipo_documento)}">${this.getTipoLabel(doc.tipo_documento)}</span>
                                    · ${this.formatearFecha(doc.fecha_subida)}
                                </small>
                                ${doc.descripcion ? `<p class="mb-0 mt-1 small text-muted">${this.escapeHtml(doc.descripcion)}</p>` : ""}
                            </div>
                        </div>
                        <div class="btn-group">
                            <button
                                type="button"
                                onclick="openfile('${doc.ruta_archivo}', '${this.escapeHtml(doc.nombre_original)}')"
                                class="btn btn-sm btn-outline-primary"
                                title="Ver documento">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button onclick="documentosManager.eliminarDocumento(${doc.id})" 
                                    class="btn btn-sm btn-outline-danger" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
      })
      .join("");
  }

  async subirDocumentos() {
    const form = document.getElementById("form-subir-documento");
    const archivosInput = document.getElementById("archivos-input");
    const files = archivosInput.files;

    if (files.length === 0) {
      this.mostrarAlert("Selecciona al menos un archivo", "warning");
      return;
    }

    const tipoDocumento = form.querySelector('[name="tipo_documento"]').value;
    const descripcion = form.querySelector('[name="descripcion"]').value;

    const btnSubmit = form.querySelector('button[type="submit"]');
    btnSubmit.disabled = true;

    let exitosos = 0;
    let errores = 0;

    // Subir cada archivo
    for (let i = 0; i < files.length; i++) {
      btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Subiendo ${i + 1}/${files.length}...`;

      const formData = new FormData();
      formData.append("archivo", files[i]);
      formData.append("tipo_servicio", this.tipoServicio);
      formData.append("servicio_id", this.servicioId);
      formData.append("tipo_documento", tipoDocumento);
      formData.append("descripcion", descripcion);

      try {
        const response = await fetch(`${this.apiUrl}?action=subir`, {
          method: "POST",
          body: formData,
        });

        const data = await response.json();

        if (data.success) {
          exitosos++;
        } else {
          errores++;
          console.error("Error:", data.error);
        }
      } catch (error) {
        errores++;
        console.error("Error de red:", error);
      }
    }

    // Mostrar resultado
    if (exitosos > 0 && errores === 0) {
      this.mostrarAlert(
        `✅ ${exitosos} documento(s) subido(s) exitosamente`,
        "success",
      );
    } else if (exitosos > 0 && errores > 0) {
      this.mostrarAlert(
        `⚠️ ${exitosos} subidos, ${errores} fallaron`,
        "warning",
      );
    } else {
      this.mostrarAlert("❌ Error al subir documentos", "danger");
    }

    // Limpiar y recargar
    form.reset();
    document.getElementById("preview-archivos").style.display = "none";
    this.cargarDocumentos();

    // Disparar evento para notificar a otros componentes
    document.dispatchEvent(
      new CustomEvent("documentosActualizados", {
        detail: { servicioId: this.servicioId },
      }),
    );

    btnSubmit.disabled = true; // Deshabilitar hasta que se seleccionen nuevos archivos
    btnSubmit.innerHTML = '<i class="bi bi-cloud-upload"></i> Subir Archivos';
  }

  async eliminarDocumento(id) {
    if (!confirm("¿Estás seguro de eliminar este documento?")) {
      return;
    }

    try {
      const formData = new FormData();
      formData.append("id", id);

      const response = await fetch(`${this.apiUrl}?action=eliminar`, {
        method: "POST",
        body: formData,
      });

      const data = await response.json();

      if (data.success) {
        this.mostrarAlert("Documento eliminado exitosamente", "success");
        this.cargarDocumentos();

        // Disparar evento para notificar a otros componentes
        document.dispatchEvent(
          new CustomEvent("documentosActualizados", {
            detail: { servicioId: this.servicioId },
          }),
        );
      } else {
        this.mostrarAlert(data.error || "Error al eliminar", "danger");
      }
    } catch (error) {
      this.mostrarAlert("Error de conexión", "danger");
    }
  }

  actualizarContador(cantidad) {
    const badge = document.getElementById("contador-documentos");
    if (badge) {
      badge.textContent = cantidad;
      badge.style.display = cantidad > 0 ? "inline" : "none";
    }
  }

  getFileIcon(ruta) {
    const ext = ruta.split(".").pop().toLowerCase();

    // PDFs
    if (ext === "pdf") {
      return { icon: "bi-file-earmark-pdf", color: "text-danger" };
    }
    // Word
    if (["doc", "docx"].includes(ext)) {
      return { icon: "bi-file-earmark-word", color: "text-primary" };
    }
    // Excel
    if (["xls", "xlsx"].includes(ext)) {
      return { icon: "bi-file-earmark-excel", color: "text-success" };
    }
    // PowerPoint
    if (["ppt", "pptx"].includes(ext)) {
      return { icon: "bi-file-earmark-ppt", color: "text-warning" };
    }
    // Imágenes
    if (["jpg", "jpeg", "png", "gif", "bmp", "webp"].includes(ext)) {
      return { icon: "bi-file-earmark-image", color: "text-info" };
    }
    // Videos
    if (["mp4", "webm", "ogg", "avi", "mov"].includes(ext)) {
      return { icon: "bi-file-earmark-play", color: "text-purple" };
    }
    // Comprimidos
    if (["zip", "rar", "7z", "tar", "gz"].includes(ext)) {
      return { icon: "bi-file-earmark-zip", color: "text-dark" };
    }
    // Otros
    return { icon: "bi-file-earmark", color: "text-secondary" };
  }

  getBadgeColor(tipo) {
    const colors = {
      presupuesto: "warning",
      contrato: "primary",
      factura: "success",
      comprobante: "info",
      otro: "secondary",
    };
    return colors[tipo] || "secondary";
  }

  getTipoLabel(tipo) {
    const labels = {
      presupuesto: "Presupuesto",
      contrato: "Contrato",
      factura: "Factura",
      comprobante: "Comprobante",
      otro: "Otro",
    };
    return labels[tipo] || tipo;
  }

  formatearFecha(fecha) {
    return new Date(fecha).toLocaleDateString("es-PE");
  }

  escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
  }

  mostrarAlert(mensaje, tipo) {
    const alertContainer = document.getElementById("alert-documentos");
    alertContainer.innerHTML = `
            <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
    setTimeout(() => {
      alertContainer.innerHTML = "";
    }, 4000);
  }
}

// Variable global - usar window.documentosManager en su lugar para evitar conflictos
// let documentosManager;
