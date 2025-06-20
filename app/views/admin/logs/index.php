<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-admin-primary mb-1">Logs del Sistema</h2>
                <p class="text-muted mb-0">Aquí puedes ver y gestionar los archivos de log del sistema</p>
            </div>
            <div class="text-end">
                <button class="btn btn-sm btn-outline-primary" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise"></i> Actualizar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Messages will be handled by controller if needed -->
<?php if (isset($success_message) && $success_message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        <?= $this->escape($success_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if (isset($error_message) && $error_message): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <?= $this->escape($error_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Logs List -->
<div class="row">
    <div class="col-12">
        <div class="admin-card shadow-sm">
            <div class="admin-card-header bg-light">
                <h5 class="mb-0 text-primary">
                    <i class="bi bi-file-text me-2"></i>
                    Archivos de Log
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($logs)): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">No hay archivos de log disponibles</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0">Nombre del Archivo</th>
                                    <th class="border-0">Tamaño</th>
                                    <th class="border-0">Última Modificación</th>
                                    <th class="border-0 text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td class="align-middle"><?= $this->escape($log['name']) ?></td>
                                        <td class="align-middle"><?= $this->escape($log['size']) ?></td>
                                        <td class="align-middle"><?= $this->escape($log['modified']) ?></td>
                                        <td class="align-middle text-end">
                                            <div class="btn-group" role="group">
                                                <a href="<?= $this->url('admin/logs', ['file' => $log['name']]) ?>" class="btn btn-outline-primary btn-sm rounded-0">
                                                    <i class="bi bi-eye"></i> Ver
                                                </a>
                                                <a href="<?= $this->url('admin/logs/download', ['file' => $log['name']]) ?>" class="btn btn-outline-success btn-sm rounded-0">
                                                    <i class="bi bi-download"></i> Descargar
                                                </a>
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-0" onclick="confirmDelete('<?= $this->escape($log['name']) ?>')">
                                                    <i class="bi bi-trash"></i> Eliminar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Log Content Viewer -->
<?php if ($selected_log_content): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="admin-card shadow-sm">
                <div class="admin-card-header bg-light">
                    <h5 class="mb-0 text-primary">
                        <i class="bi bi-file-text me-2"></i>
                        Contenido de: <?= $this->escape($selected_log_name) ?>
                    </h5>
                </div>
                <div class="card-body p-3 bg-dark text-light" style="max-height: 400px; overflow-y: auto; font-family: monospace; white-space: pre-wrap; border-radius: 0 0 5px 5px;">
                    <?= $this->escape($selected_log_content) ?>
                </div>
                <div class="card-footer text-end bg-light">
                    <a href="<?= $this->url('admin/logs') ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x"></i> Cerrar Vista
                    </a>
                    <a href="<?= $this->url('admin/logs/download', ['file' => $selected_log_name]) ?>" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-download"></i> Descargar Completo
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php $this->startSection('scripts') ?>
<script>
function confirmDelete(fileName) {
    if (confirm('¿Estás seguro de que deseas eliminar el archivo de log "' + fileName + '"? Esta acción no se puede deshacer.')) {
        // Crear formulario oculto para enviar la solicitud POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= $this->url('admin/logs/delete') ?>';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'file';
        input.value = fileName;
        form.appendChild(input);
        
        // Agregar token CSRF si está disponible
        const csrfToken = '<?= $this->generateCsrfToken() ?>';
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
        }
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
<?php $this->endSection() ?>
