<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $this->escape($title) . ' - ' : '' ?>Framework Tukuchi</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        .navbar-brand {
            font-weight: bold;
            color: #007bff !important;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
        }
        
        .feature-card {
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        footer {
            background-color: #343a40;
            color: white;
            margin-top: 3rem;
        }
        
        .tukuchi-logo {
            font-size: 1.5rem;
            font-weight: bold;
            background: linear-gradient(45deg, #007bff, #6f42c1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
    
    <?= $this->showSection('head') ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">
            <a class="navbar-brand tukuchi-logo" href="<?= $this->url('home') ?>">
                🐦 Framework Tukuchi
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->url('home') ?>">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->url('home', 'about') ?>">Acerca de</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->url('home', 'contact') ?>">Contacto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->url('home', 'api') ?>" target="_blank">API</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="tukuchi-logo">🐦 Framework Tukuchi</h5>
                    <p class="mb-0">Potenciando la Transformación Digital</p>
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