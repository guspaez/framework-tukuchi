<div class="admin-card shadow-sm">
    <div class="admin-card-header bg-light">
        <h5 class="mb-0 text-primary">
            <i class="bi bi-person me-2"></i>
            Detalle de Usuario
        </h5>
    </div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">ID</dt>
            <dd class="col-sm-9"><?= $this->escape($user->id) ?></dd>

            <dt class="col-sm-3">Nombre</dt>
            <dd class="col-sm-9"><?= $this->escape($user->name) ?></dd>

            <dt class="col-sm-3">Correo Electrónico</dt>
            <dd class="col-sm-9"><?= $this->escape($user->email) ?></dd>

            <dt class="col-sm-3">Rol</dt>
            <dd class="col-sm-9">
                <span class="badge <?= $user->role == 'admin' ? 'bg-primary' : 'bg-secondary' ?>">
                    <?= $this->escape(ucfirst($user->role)) ?>
                </span>
            </dd>

            <dt class="col-sm-3">Fecha de Registro</dt>
            <dd class="col-sm-9"><?= $this->escape($user->created_at) ?></dd>
        </dl>
        <a href="<?= $this->url('admin/users') ?>" class="btn btn-secondary mt-3">
            <i class="bi bi-arrow-left"></i> Volver a la lista
        </a>
    </div>
</div>