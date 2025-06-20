<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $this->escape($title) . ' - ' : '' ?>Panel de Administración - Tukuchi</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom Admin CSS -->
    <style>
        :root {
            --admin-primary: #2c3e50;
            --admin-secondary: #34495e;
            --admin-accent: #3498db;
            --admin-success: #27ae60;
            --admin-warning: #f39c12;
            --admin-danger: #e74c3c;
            --admin-sidebar-width: 250px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        /* Sidebar */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--admin-sidebar-width);
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
            color: white;
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .admin-sidebar .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .admin-sidebar .sidebar-brand {
            font-size: 1.25rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .admin-sidebar .sidebar-brand:hover {
            color: var(--admin-accent);
        }

        .admin-sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin: 0.25rem 1rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .admin-sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .admin-sidebar .nav-link.active {
            color: white;
            background-color: var(--admin-accent);
        }

        .admin-sidebar .nav-link i {
            margin-right: 0.75rem;
            width: 1.25rem;
            text-align: center;
        }

        /* Main Content */
        .admin-main {
            margin-left: var(--admin-sidebar-width);
            min-height: 100vh;
        }

        /* Top Navigation */
        .admin-topbar {
            background: white;
            border-bottom: 1px solid #dee2e6;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .admin-content {
            padding: 2rem 1.5rem;
        }

        /* Cards */
        .admin-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.3s ease;
        }

        .admin-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .admin-card-header {
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
            color: white;
            border-radius: 0.75rem 0.75rem 0 0 !important;
            padding: 1rem 1.5rem;
            border: none;
        }

        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .stats-card .stats-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stats-card.primary .stats-icon { background: var(--admin-primary); }
        .stats-card.success .stats-icon { background: var(--admin-success); }
        .stats-card.warning .stats-icon { background: var(--admin-warning); }
        .stats-card.danger .stats-icon { background: var(--admin-danger); }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            
            .admin-sidebar.show {
                transform: translateX(0);
            }
            
            .admin-main {
                margin-left: 0;
            }
        }

        /* Utilities */
        .text-admin-primary { color: var(--admin-primary) !important; }
        .bg-admin-primary { background-color: var(--admin-primary) !important; }
        .border-admin-primary { border-color: var(--admin-primary) !important; }

        /* User dropdown */
        .user-dropdown .dropdown-toggle::after {
            display: none;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--admin-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
    </style>
    
    <?= $this->showSection('head') ?>
</head>
<body>
    <!-- Sidebar -->
    <nav class="admin-sidebar">
        <div class="sidebar-header">
            <a href="<?= $this->url('admin/dashboard') ?>" class="sidebar-brand">
                🐦 Tukuchi Admin
            </a>
        </div>
        
        <ul class="nav flex-column">
            <?php foreach ($admin_menu as $item): ?>
            <li class="nav-item">
                <a href="<?= $item['url'] ?>" class="nav-link <?= $item['active'] ? 'active' : '' ?>">
                    <i class="bi <?= $item['icon'] ?>"></i>
                    <?= $item['title'] ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="admin-main">
        <!-- Top Navigation -->
        <div class="admin-topbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-link d-md-none" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0 text-admin-primary"><?= $this->escape($title ?? 'Panel de Administración') ?></h5>
            </div>
            
            <div class="d-flex align-items-center">
                <!-- Notifications -->
                <div class="dropdown me-3">
                    <button class="btn btn-link position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell text-admin-primary"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            3
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Notificaciones</h6></li>
                        <li><a class="dropdown-item" href="#">Nuevo usuario registrado</a></li>
                        <li><a class="dropdown-item" href="#">Error en el sistema</a></li>
                        <li><a class="dropdown-item" href="#">Backup completado</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todas</a></li>
                    </ul>
                </div>

                <!-- User Menu -->
                <div class="dropdown user-dropdown">
                    <button class="btn btn-link d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                        <div class="user-avatar me-2">
                            <?= strtoupper(substr($admin_user->name, 0, 1)) ?>
                        </div>
                        <span class="text-admin-primary"><?= $this->escape($admin_user->name) ?></span>
                        <i class="bi bi-chevron-down ms-1 text-admin-primary"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header"><?= $this->escape($admin_user->email) ?></h6></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Configuración</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= $this->url('admin/auth', 'logout') ?>">
                            <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="admin-content">
            <?= $content ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Admin JS -->
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.admin-sidebar').classList.toggle('show');
        });

        // Auto-hide alerts
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
    
    <?= $this->showSection('scripts') ?>
</body>
</html>
