<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-admin-primary mb-1">Gestión de Usuarios</h2>
                <p class="text-muted mb-0">Aquí puedes ver y gestionar los usuarios del sistema</p>
            </div>
            <div class="text-end">
                <a href="<?= $this->url('admin/users/create') ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-person-plus"></i> Nuevo Usuario
                </a>
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

<!-- Users List -->
<div class="row">
    <div class="col-12">
        <div class="admin-card shadow-sm">
            <div class="admin-card-header bg-light">
                <h5 class="mb-0 text-primary">
                    <i class="bi bi-people me-2"></i>
                    Lista de Usuarios
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($users)): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">No hay usuarios registrados</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0">ID</th>
                                    <th class="border-0">Nombre</th>
                                    <th class="border-0">Correo Electrónico</th>
                                    <th class="border-0">Rol</th>
                                    <th class="border-0">Fecha de Registro</th>
                                    <th class="border-0 text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td class="align-middle"><?= $this->escape($user->id) ?></td>
                                        <td class="align-middle"><?= $this->escape($user->name) ?></td>
                                        <td class="align-middle"><?= $this->escape($user->email) ?></td>
                                        <td class="align-middle">
                                            <span class="badge <?= $user->role == 'admin' ? 'bg-primary' : 'bg-secondary' ?>">
                                                <?= $this->escape(ucfirst($user->role)) ?>
                                            </span>
                                        </td>
                                        <td class="align-middle"><?= $this->escape($user->created_at) ?></td>
                                        <td class="align-middle text-end">
                                            <div class="btn-group" role="group">
                                                <a href="<?= $this->url('admin/users/show', ['id' => $user->id]) ?>" class="btn btn-outline-info btn-sm rounded-0">
                                                    <i class="bi bi-eye"></i> Ver
                                                </a>
                                                <a href="<?= $this->url('admin/users/edit', ['id' => $user->id]) ?>" class="btn btn-outline-primary btn-sm rounded-0">
                                                    <i class="bi bi-pencil"></i> Editar
                                                </a>
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-0" onclick="confirmDelete(<?= $user->id ?>, '<?= $this->escape($user->name) ?>')">
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

<?php $this->startSection('scripts') ?>
<script>
function confirmDelete(userId, userName) {
    if (confirm('¿Estás seguro de que deseas eliminar al usuario "' + userName + '"? Esta acción no se puede deshacer.')) {
        // Crear formulario oculto para enviar la solicitud POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= $this->url('admin/users/delete', ['id' => '']) ?>' + userId;
        
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
