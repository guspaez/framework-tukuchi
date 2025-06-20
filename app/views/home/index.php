<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4"><?= $this->escape($title) ?></h1>
                <p class="lead mb-4"><?= $this->escape($message) ?></p>
                <p class="mb-4">
                    Framework PHP diseñado específicamente para acelerar el desarrollo de 
                    soluciones digitales para pequeños negocios. Con arquitectura MVC, 
                    inyección de dependencias y herramientas de seguridad integradas.
                </p>
                <div class="d-flex gap-3">
                    <a href="<?= $this->url('home', 'about') ?>" class="btn btn-light btn-lg">
                        Conocer Más
                    </a>
                    <a href="<?= $this->url('home', 'contact') ?>" class="btn btn-outline-light btn-lg">
                        Contacto
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="p-4">
                    <div style="font-size: 8rem; opacity: 0.8;">🐦</div>
                    <h3 class="mt-3">Agilidad como un Colibrí</h3>
                    <p>Soluciones rápidas y precisas</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 fw-bold">Características Principales</h2>
                <p class="lead text-muted">Todo lo que necesitas para desarrollar aplicaciones web modernas</p>
            </div>
        </div>
        
        <div class="row g-4">
            <?php foreach ($features as $index => $feature): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card feature-card h-100 p-4 text-center">
                    <div class="card-body">
                        <div class="mb-3">
                            <?php
                            $icons = ['🏗️', '💉', '🛣️', '🔒', '⚡'];
                            echo '<span style="font-size: 3rem;">' . $icons[$index] . '</span>';
                            ?>
                        </div>
                        <h5 class="card-title"><?= $this->escape($feature) ?></h5>
                        <p class="card-text text-muted">
                            <?php
                            $descriptions = [
                                'Separación clara de responsabilidades con el patrón Modelo-Vista-Controlador',
                                'Service Locator integrado para gestión eficiente de dependencias',
                                'Sistema de rutas flexible y fácil de configurar',
                                'Protección CSRF, validación de datos y prevención de ataques',
                                'Desarrollo rápido con componentes reutilizables'
                            ];
                            echo $descriptions[$index];
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Quick Start Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-6 fw-bold mb-4">Inicio Rápido</h2>
                <p class="lead mb-4">
                    Comienza a desarrollar con Framework Tukuchi en minutos
                </p>
                
                <div class="row g-4 mt-4">
                    <div class="col-md-4">
                        <div class="p-3">
                            <div class="mb-3">
                                <span class="badge bg-primary rounded-pill fs-6">1</span>
                            </div>
                            <h5>Configurar</h5>
                            <p class="text-muted">Edita el archivo de configuración con tus datos de base de datos</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <div class="mb-3">
                                <span class="badge bg-primary rounded-pill fs-6">2</span>
                            </div>
                            <h5>Crear</h5>
                            <p class="text-muted">Desarrolla tus controladores y vistas siguiendo la estructura MVC</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <div class="mb-3">
                                <span class="badge bg-primary rounded-pill fs-6">3</span>
                            </div>
                            <h5>Desplegar</h5>
                            <p class="text-muted">Sube tu aplicación y comienza a transformar negocios digitalmente</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 col-6 mb-4">
                <div class="p-3">
                    <h3 class="display-6 fw-bold text-primary">MVC</h3>
                    <p class="text-muted">Arquitectura</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="p-3">
                    <h3 class="display-6 fw-bold text-primary">PHP 8+</h3>
                    <p class="text-muted">Compatible</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="p-3">
                    <h3 class="display-6 fw-bold text-primary">100%</h3>
                    <p class="text-muted">Open Source</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="p-3">
                    <h3 class="display-6 fw-bold text-primary">v<?= $this->escape($version) ?></h3>
                    <p class="text-muted">Versión Actual</p>
                </div>
            </div>
        </div>
    </div>
</section>