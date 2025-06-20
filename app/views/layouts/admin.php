<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $this->escape($title) . ' - ' : '' ?>Panel de Administración - Tukuchi</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <style>
        body {
            background: #f8f9fa;
        }
        .admin-sidebar {
            min-width: 220px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            min-height: 100vh;
        }
        .admin-sidebar .nav-link {
            color: #fff;
            font-weight: 500;
            border-radius: 0.375rem;
            margin-bottom: 0.5rem;
            transition: background 0.2s;
        }
        .admin-sidebar .nav-link.active,
        .admin-sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: #ffd700;
        }
        .admin-sidebar h4 {
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 2rem;
            background: linear-gradient(45deg, #ffd700, #fff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .admin-content {
            padding: 2rem;
            width: 100%;
        }
        .tukuchi-logo {
            font-size: 1.5rem;
            font-weight: bold;
            background: linear-gradient(45deg, #ffd700, #fff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .admin-card {
            background: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .admin-card-header {
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border-radius: 0.5rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(102,126,234,0.1);
        }
        .stats-card.warning {
            background: linear-gradient(135deg, #ffb347 0%, #ffcc33 100%);
            color: #333;
        }
        .stats-card.danger {
            background: linear-gradient(135deg, #ff5858 0%, #f09819 100%);
            color: #fff;
        }
        .stats-icon {
            font-size: 2rem;
            margin-right: 1rem;
        }
        footer {
            background-color: #343a40;
            color: white;
            margin-top: 3rem;
        }
    </style>
    
    <?= $this->showSection('head') ?>
</head>
<body>
    <div class="admin-wrapper d-flex">
        <!-- Sidebar/Menu lateral -->
        <nav class="admin-sidebar p-3">
            <div class="mb-4 text-center">
                <span class="tukuchi-logo">🐦 Tukuchi Admin</span>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a class="nav-link<?= $_SERVER['REQUEST_URI'] === $this->url('admin/dashboard') ? ' active' : '' ?>" href="<?= $this->url('admin/dashboard') ?>">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link<?= strpos($_SERVER['REQUEST_URI'], $this->url('admin/users')) !== false ? ' active' : '' ?>" href="<?= $this->url('admin/users') ?>">
                        <i class="bi bi-people me-2"></i> Usuarios
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link<?= strpos($_SERVER['REQUEST_URI'], $this->url('admin/logs')) !== false ? ' active' : '' ?>" href="<?= $this->url('admin/logs') ?>">
                        <i class="bi bi-file-text me-2"></i> Logs
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link<?= strpos($_SERVER['REQUEST_URI'], $this->url('admin/database')) !== false ? ' active' : '' ?>" href="<?= $this->url('admin/database') ?>">
                        <i class="bi bi-database me-2"></i> Base de Datos
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link<?= strpos($_SERVER['REQUEST_URI'], $this->url('admin/settings')) !== false ? ' active' : '' ?>" href="<?= $this->url('admin/settings') ?>">
                        <i class="bi bi-gear me-2"></i> Configuración
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <a class="nav-link text-danger" href="<?= $this->url('logout') ?>">
                        <i class="bi bi-box-arrow-right me-2"></i> Salir
                    </a>
                </li>
            </ul>
        </nav>
        <!-- Contenido principal -->
        <main class="admin-content flex-grow-1" id="main-content">
            <?= $content ?>
        </main>
    </div>
    <footer class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="tukuchi-logo">🐦 Framework Tukuchi</h5>
                    <p class="mb-0">Panel de Administración</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        &copy; <?= date('Y') ?> Framework Tukuchi. 
                        <small class="text-muted">Versión 1.0.0</small>
                    </p>
                </div>
            </div>
        </div>
    </footer>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <?= $this->showSection('scripts') ?>
</body>
</html>
