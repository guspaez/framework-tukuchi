<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="display-6"><?= $this->escape($title) ?></h1>
                <a href="<?= $this->url('user', 'create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nuevo Usuario
                </a>
            </div>

            <!-- Barra de búsqueda -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" id="searchInput" class="form-control" placeholder="Buscar usuarios...">
                                <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                    <i class="bi bi-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="text-muted">Total: <strong><?= count($users) ?></strong> usuarios</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de usuarios -->
            <div class="card">
                <div class="card-body">
                    <?php if (empty($users)): ?>
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-people" style="font-size: 4rem; color: #6c757d;"></i>
                            </div>
                            <h5 class="text-muted">No hay usuarios registrados</h5>
                            <p class="text-muted">Comienza creando tu primer usuario.</p>
                            <a href="<?= $this->url('user', 'create') ?>" class="btn btn-primary">
                                Crear Usuario
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="usersTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Estado</th>
                                        <th>Fecha Creación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= $this->escape($user->id) ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <?= strtoupper(substr($user->name, 0, 1)) ?>
                                                </div>
                                                <?= $this->escape($user->name) ?>
                                            </div>
                                        </td>
                                        <td><?= $this->escape($user->email) ?></td>
                                        <td>
                                            <?php if ($user->status === 'active'): ?>
                                                <span class="badge bg-success">Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($user->created_at)): ?>
                                                <?= $this->formatDate($user->created_at, 'd/m/Y H:i') ?>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?= $this->url('user', 'show', [$user->id]) ?>" 
                                                   class="btn btn-outline-info" title="Ver">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= $this->url('user', 'edit', [$user->id]) ?>" 
                                                   class="btn btn-outline-warning" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" 
                                                        onclick="deleteUser(<?= $user->id ?>, '<?= $this->escape($user->name) ?>')" 
                                                        title="Eliminar">
                                                    <i class="bi bi-trash"></i>
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
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar al usuario <strong id="deleteUserName"></strong>?</p>
                <p class="text-muted">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->startSection('head') ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 0.875rem;
    }
</style>
<?php $this->endSection() ?>

<?php $this->startSection('scripts') ?>
<script>
$(document).ready(function() {
    // Búsqueda en tiempo real
    $('#searchInput').on('input', Tukuchi.utils.debounce(function() {
        const query = $(this).val().toLowerCase();
        
        $('#usersTable tbody tr').each(function() {
            const row = $(this);
            const name = row.find('td:nth-child(2)').text().toLowerCase();
            const email = row.find('td:nth-child(3)').text().toLowerCase();
            
            if (name.includes(query) || email.includes(query)) {
                row.show();
            } else {
                row.hide();
            }
        });
    }, 300));

    // Búsqueda con botón
    $('#searchBtn').on('click', function() {
        const query = $('#searchInput').val();
        
        if (query.length < 2) {
            Tukuchi.utils.notify('Ingresa al menos 2 caracteres para buscar', 'warning');
            return;
        }

        // Búsqueda via AJAX
        Tukuchi.utils.ajax({
            url: '<?= $this->url('user', 'search') ?>?q=' + encodeURIComponent(query),
            method: 'GET'
        })
        .then(response => {
            if (response.status === 'success') {
                updateUsersTable(response.users);
                Tukuchi.utils.notify(`Se encontraron ${response.total} usuarios`, 'info');
            }
        })
        .catch(error => {
            Tukuchi.utils.notify('Error en la búsqueda', 'danger');
        });
    });

    // Limpiar búsqueda con Enter
    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) {
            $('#searchBtn').click();
        }
    });
});

function deleteUser(userId, userName) {
    $('#deleteUserName').text(userName);
    $('#deleteForm').attr('action', '<?= $this->url('user', 'delete') ?>/' + userId);
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function updateUsersTable(users) {
    const tbody = $('#usersTable tbody');
    tbody.empty();
    
    if (users.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="6" class="text-center py-4">
                    <span class="text-muted">No se encontraron usuarios</span>
                </td>
            </tr>
        `);
        return;
    }
    
    users.forEach(user => {
        const statusBadge = user.status === 'active' 
            ? '<span class="badge bg-success">Activo</span>'
            : '<span class="badge bg-secondary">Inactivo</span>';
            
        const createdAt = user.created_at 
            ? new Date(user.created_at).toLocaleDateString('es-ES')
            : 'N/A';
            
        const row = `
            <tr>
                <td>${user.id}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                            ${user.name.charAt(0).toUpperCase()}
                        </div>
                        ${user.name}
                    </div>
                </td>
                <td>${user.email}</td>
                <td>${statusBadge}</td>
                <td>${createdAt}</td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="<?= $this->url('user', 'show') ?>/${user.id}" class="btn btn-outline-info" title="Ver">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="<?= $this->url('user', 'edit') ?>/${user.id}" class="btn btn-outline-warning" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-outline-danger" 
                                onclick="deleteUser(${user.id}, '${user.name}')" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}
</script>
<?php $this->endSection() ?>