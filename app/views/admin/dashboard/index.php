<!-- Welcome Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-admin-primary mb-1">¡Bienvenido, <?= $this->escape($admin_user->name) ?>!</h2>
                <p class="text-muted mb-0">Aquí tienes un resumen del estado actual del sistema</p>
            </div>
            <div class="text-end">
                <small class="text-muted">Última actualización: <?= date('d/m/Y H:i:s') ?></small>
                <br>
                <button class="btn btn-sm btn-outline-primary" onclick="refreshStats()">
                    <i class="bi bi-arrow-clockwise"></i> Actualizar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <!-- Users Stats -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card primary">
            <div class="d-flex align-items-center">
                <div class="stats-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="ms-3 flex-grow-1">
                    <div class="text-muted small">Total Usuarios</div>
                    <div class="h4 mb-0"><?= number_format($stats['users']['total']) ?></div>
                    <div class="small text-success">
                        <i class="bi bi-check-circle"></i>
                        <?= $stats['users']['active'] ?> activos
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Database Stats -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card success">
            <div class="d-flex align-items-center">
                <div class="stats-icon">
                    <i class="bi bi-database"></i>
                </div>
                <div class="ms-3 flex-grow-1">
                    <div class="text-muted small">Base de Datos</div>
                    <div class="h4 mb-0"><?= $stats['database']['size'] ?></div>
                    <div class="small text-info">
                        <i class="bi bi-table"></i>
                        <?= $stats['database']['tables'] ?> tablas
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logs Stats -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card warning">
            <div class="d-flex align-items-center">
                <div class="stats-icon">
                    <i class="bi bi-file-text"></i>
                </div>
                <div class="ms-3 flex-grow-1">
                    <div class="text-muted small">Logs del Sistema</div>
                    <div class="h4 mb-0"><?= number_format($stats['logs']['total']) ?></div>
                    <div class="small <?= $stats['logs']['errors'] > 0 ? 'text-danger' : 'text-success' ?>">
                        <i class="bi <?= $stats['logs']['errors'] > 0 ? 'bi-exclamation-triangle' : 'bi-check-circle' ?>"></i>
                        <?= $stats['logs']['errors'] ?> errores
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Stats -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card danger">
            <div class="d-flex align-items-center">
                <div class="stats-icon">
                    <i class="bi bi-cpu"></i>
                </div>
                <div class="ms-3 flex-grow-1">
                    <div class="text-muted small">Memoria</div>
                    <div class="h4 mb-0"><?= $stats['system']['memory_usage'] ?></div>
                    <div class="small text-info">
                        <i class="bi bi-arrow-up"></i>
                        Pico: <?= $stats['system']['memory_peak'] ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    Acciones Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="<?= $this->url('admin/users') ?>" class="btn btn-outline-primary w-100">
                            <i class="bi bi-people d-block mb-2" style="font-size: 1.5rem;"></i>
                            Gestionar Usuarios
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="<?= $this->url('admin/logs') ?>" class="btn btn-outline-success w-100">
                            <i class="bi bi-file-text d-block mb-2" style="font-size: 1.5rem;"></i>
                            Ver Logs
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="<?= $this->url('admin/database') ?>" class="btn btn-outline-warning w-100">
                            <i class="bi bi-database d-block mb-2" style="font-size: 1.5rem;"></i>
                            Base de Datos
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="<?= $this->url('admin/settings') ?>" class="btn btn-outline-info w-100">
                            <i class="bi bi-gear d-block mb-2" style="font-size: 1.5rem;"></i>
                            Configuración
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Row -->
<div class="row">
    <!-- Recent Activity -->
    <div class="col-lg-8 mb-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="mb-0">
                    <i class="bi bi-activity me-2"></i>
                    Actividad Reciente
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_activity)): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">No hay actividad reciente</p>
                    </div>
                <?php else: ?>
                    <div class="activity-list">
                        <?php foreach ($recent_activity as $activity): ?>
                        <div class="activity-item d-flex align-items-start mb-3 border-left-<?= strpos($activity['icon'], 'danger') !== false ? 'danger' : (strpos($activity['icon'], 'warning') !== false ? 'warning' : 'info') ?> pl-2">
                            <div class="activity-icon me-3">
                                <i class="<?= $activity['icon'] ?>" style="font-size: 1.5rem;"></i>
                            </div>
                            <div class="activity-content flex-grow-1">
                                <div class="activity-message font-weight-bold">
                                    <?= $this->escape(substr($activity['message'], 0, 100)) . (strlen($activity['message']) > 100 ? '...' : '') ?>
                                </div>
                                <div class="activity-details text-muted small mt-1">
                                    <?php 
                                        $details = json_decode($activity['details'] ?? '{}', true);
                                        $ip = $details['ip'] ?? 'N/A';
                                        $userAgent = $details['user_agent'] ?? 'N/A';
                                        $userAgentShort = substr($userAgent, 0, 30) . (strlen($userAgent) > 30 ? '...' : '');
                                    ?>
                                    <span><i class="bi bi-globe me-1"></i> IP: <?= $this->escape($ip) ?></span> | 
                                    <span title="<?= $this->escape($userAgent) ?>"><i class="bi bi-device-ssd me-1"></i> <?= $this->escape($userAgentShort) ?></span>
                                </div>
                                <div class="activity-time text-muted small mt-1">
                                    <i class="bi bi-clock me-1"></i>
                                    <?= $activity['timestamp'] ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="<?= $this->url('admin/logs') ?>" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-eye me-1"></i>
                            Ver todos los logs
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="col-lg-4 mb-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Información del Sistema
                </h5>
            </div>
            <div class="card-body">
                <div class="system-info">
                    <div class="info-item d-flex justify-content-between mb-2">
                        <span class="text-muted">Framework:</span>
                        <span class="fw-bold">Tukuchi v<?= $system_info['framework_version'] ?></span>
                    </div>
                    <div class="info-item d-flex justify-content-between mb-2">
                        <span class="text-muted">PHP:</span>
                        <span><?= $system_info['php_version'] ?></span>
                    </div>
                    <div class="info-item d-flex justify-content-between mb-2">
                        <span class="text-muted">Servidor:</span>
                        <span class="text-truncate" style="max-width: 150px;" title="<?= $system_info['server_software'] ?>">
                            <?= $this->truncate($system_info['server_software'], 20) ?>
                        </span>
                    </div>
                    <div class="info-item d-flex justify-content-between mb-2">
                        <span class="text-muted">Puerto:</span>
                        <span><?= $system_info['server_port'] ?></span>
                    </div>
                    <div class="info-item d-flex justify-content-between mb-2">
                        <span class="text-muted">Extensiones PHP:</span>
                        <span><?= $system_info['loaded_extensions'] ?></span>
                    </div>
                    <div class="info-item d-flex justify-content-between mb-2">
                        <span class="text-muted">Límite memoria:</span>
                        <span><?= $system_info['memory_limit'] ?></span>
                    </div>
                    <div class="info-item d-flex justify-content-between mb-2">
                        <span class="text-muted">Max upload:</span>
                        <span><?= $system_info['upload_max_filesize'] ?></span>
                    </div>
                    <div class="info-item d-flex justify-content-between">
                        <span class="text-muted">Tiempo ejecución:</span>
                        <span><?= $system_info['max_execution_time'] ?>s</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->startSection('scripts') ?>
<script>
function refreshStats() {
    const btn = event.target.closest('button');
    const originalHtml = btn.innerHTML;
    
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Actualizando...';
    btn.disabled = true;
    
    // Simular actualización (en una implementación real harías una petición AJAX)
    setTimeout(function() {
        location.reload();
    }, 1000);
}

// Auto-refresh cada 5 minutos
setInterval(function() {
    // En una implementación real, actualizarías solo las estadísticas via AJAX
    console.log('Auto-refresh stats...');
}, 300000);

// Animaciones para las tarjetas de estadísticas
$(document).ready(function() {
    $('.stats-card').each(function(index) {
        $(this).css('animation-delay', (index * 0.1) + 's');
        $(this).addClass('animate__animated animate__fadeInUp');
    });
});
</script>
<?php $this->endSection() ?>
