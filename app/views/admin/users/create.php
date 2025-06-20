<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-admin-primary mb-1">Crear Nuevo Usuario</h2>
                <p class="text-muted mb-0">Completa el formulario para agregar un nuevo usuario al sistema</p>
            </div>
            <div class="text-end">
                <a href="<?= $this->url('admin/users') ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a la Lista
                </a>
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

<!-- Create User Form -->
<div class="row">
    <div class="col-lg-8 col-md-10 col-12 mx-auto">
        <div class="admin-card shadow-sm">
            <div class="admin-card-header bg-light">
                <h5 class="mb-0 text-primary">
                    <i class="bi bi-person-plus me-2"></i>
                    Datos del Nuevo Usuario
                </h5>
            </div>
            <div class="card-body">
                <form action="<?= $this->url('admin/users/store') ?>" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre Completo</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="name" name="name" required placeholder="Ingrese el nombre completo">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" required placeholder="Ingrese el correo electrónico">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required placeholder="Ingrese una contraseña segura">
                        </div>
                        <div class="form-text">La contraseña debe tener al menos 6 caracteres.</div>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Rol del Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                            <select class="form-select" id="role" name="role" required>
                                <option value="user" selected>Usuario Regular</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <div class="form-text">Los administradores tienen acceso completo al panel de control.</div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i> Guardar Usuario
                        </button>
                        <a href="<?= $this->url('admin/users') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-x me-2"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->startSection('scripts') ?>
<script>
$(document).ready(function() {
    // Enfocar el primer campo al cargar la página
    $('#name').focus();
});
</script>
<?php $this->endSection() ?>
