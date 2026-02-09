<!-- Modal de Gestión de Documentos -->
<div class="modal fade" id="modalDocumentos" tabindex="-1" aria-labelledby="modalDocumentosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-files"></i> Documentos del Servicio
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="alert-documentos"></div>

                <!-- Formulario de Subida -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <i class="bi bi-upload"></i> Adjuntar Nuevos Documentos
                    </div>
                    <div class="card-body">
                        <form id="form-subir-documento">
                            <div class="row">
                                <!-- Tipo de documento -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tipo de Documento *</label>
                                    <select class="form-select" name="tipo_documento" required>
                                        <option value="presupuesto">Presupuesto</option>
                                        <option value="contrato">Contrato</option>
                                        <option value="factura">Factura</option>
                                        <option value="comprobante">Comprobante</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>

                                <!-- Descripción -->
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Descripción (Opcional)</label>
                                    <input type="text" class="form-control" name="descripcion"
                                        placeholder="Ej: Presupuesto aprobado por el cliente">
                                </div>

                                <!-- Área de selección de archivos -->
                                <div class="col-12 mb-3">
                                    <div class="border border-2 border-dashed rounded p-4 text-center" style="background-color: #f8f9fa;">
                                        <i class="bi bi-cloud-upload fs-1 text-primary d-block mb-2"></i>
                                        <p class="mb-2 fw-bold">Selecciona uno o varios archivos</p>
                                        <input type="file" class="form-control d-inline-block w-auto" id="archivos-input"
                                            name="archivos[]" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar" multiple required>
                                        <p class="text-muted small mt-2 mb-0">
                                            <i class="bi bi-info-circle"></i> PDF, Imágenes, Word, Excel, PowerPoint, ZIP (máx 5MB por archivo)
                                        </p>
                                    </div>
                                </div>

                                <!-- Preview de archivos seleccionados -->
                                <div class="col-12 mb-3" id="preview-archivos" style="display:none;">
                                    <div class="alert alert-success mb-0">
                                        <strong><i class="bi bi-check-circle"></i> Archivos listos para subir:</strong>
                                        <div id="lista-preview" class="mt-2"></div>
                                    </div>
                                </div>

                                <!-- Botón de subida -->
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg w-100" id="btn-subir-docs" disabled>
                                        <i class="bi bi-cloud-upload"></i> Subir Archivos
                                    </button>
                                    <small class="text-muted d-block mt-2 text-center">
                                        <i class="bi bi-info-circle"></i> Los archivos se guardarán automáticamente
                                    </small>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lista de Documentos -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list-ul"></i> Documentos Adjuntos
                    </div>
                    <div class="card-body" id="lista-documentos">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer del Modal -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>