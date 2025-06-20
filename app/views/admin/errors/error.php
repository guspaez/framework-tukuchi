<?php
<div class="admin-card shadow-sm mt-5">
    <div class="admin-card-header bg-danger text-white">
        <h5 class="mb-0">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?= isset($title) ? $this->escape($title) : 'Error' ?>
        </h5>
    </div>
    <div class="card-body">
        <p class="mb-3">
            <?= isset($message) ? $this->escape($message) : 'Ha ocurrido un error inesperado.' ?>
        </p>
        <a href="<?= $this->url('admin/users') ?>" class="btn btn-primary">
            <i class="bi bi-arrow-left"></i> Volver al panel
        </a>
    </div>
</div>