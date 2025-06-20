<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold"><?= $this->escape($title) ?></h1>
                <p class="lead text-muted"><?= $this->escape($description) ?></p>
            </div>

            <div class="row g-5">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="text-center mb-3">
                                <span style="font-size: 3rem;">🎯</span>
                            </div>
                            <h4 class="card-title text-center">Nuestra Misión</h4>
                            <p class="card-text">
                                Proporcionar un framework PHP robusto y fácil de usar que permita a los 
                                desarrolladores crear soluciones digitales de manera rápida y eficiente, 
                                especialmente enfocado en las necesidades de pequeños negocios.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="text-center mb-3">
                                <span style="font-size: 3rem;">🚀</span>
                            </div>
                            <h4 class="card-title text-center">Nuestra Visión</h4>
                            <p class="card-text">
                                Ser la herramienta preferida para conectar negocios locales con el mundo 
                                digital, facilitando la transformación digital de manera accesible, 
                                personalizada y sostenible.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <h3 class="mb-4">¿Por qué Framework Tukuchi?</h3>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary rounded-pill">⚡</span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5>Agilidad</h5>
                                <p class="text-muted">
                                    Desarrollo rápido y preciso, como un colibrí en movimiento. 
                                    Reduce significativamente el tiempo de desarrollo.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary rounded-pill">🧠</span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5>Innovación</h5>
                                <p class="text-muted">
                                    Diseñado para integrar fácilmente tecnologías avanzadas 
                                    como IA y APIs modernas.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary rounded-pill">🌱</span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5>Sostenibilidad</h5>
                                <p class="text-muted">
                                    Código optimizado y eficiente que contribuye a 
                                    soluciones más sostenibles y mantenibles.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary rounded-pill">🔒</span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5>Seguridad</h5>
                                <p class="text-muted">
                                    Protección integrada contra vulnerabilidades comunes 
                                    como inyecciones SQL y ataques XSS.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <h3 class="mb-4">Componentes Principales</h3>
                
                <div class="accordion" id="componentsAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingCore">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCore">
                                Núcleo (Core)
                            </button>
                        </h2>
                        <div id="collapseCore" class="accordion-collapse collapse show" data-bs-parent="#componentsAccordion">
                            <div class="accordion-body">
                                <strong>Service Locator:</strong> Corazón del framework para la gestión de dependencias.<br>
                                <strong>App:</strong> Gestión de la aplicación y procesamiento de peticiones.<br>
                                <strong>Configuración:</strong> Archivos centralizados para parámetros globales.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingMVC">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMVC">
                                Arquitectura MVC
                            </button>
                        </h2>
                        <div id="collapseMVC" class="accordion-collapse collapse" data-bs-parent="#componentsAccordion">
                            <div class="accordion-body">
                                <strong>Modelo:</strong> Lógica de negocio y acceso a datos.<br>
                                <strong>Vista:</strong> Presentación e interfaz de usuario.<br>
                                <strong>Controlador:</strong> Gestión de peticiones y flujo de la aplicación.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingSecurity">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSecurity">
                                Seguridad
                            </button>
                        </h2>
                        <div id="collapseSecurity" class="accordion-collapse collapse" data-bs-parent="#componentsAccordion">
                            <div class="accordion-body">
                                <strong>Protección CSRF:</strong> Prevención de ataques de falsificación de solicitudes.<br>
                                <strong>Validación:</strong> Sanitización automática de datos de entrada.<br>
                                <strong>Cifrado:</strong> Funciones seguras para manejo de contraseñas.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5">
                <a href="<?= $this->url('home', 'contact') ?>" class="btn btn-primary btn-lg">
                    ¿Tienes preguntas? Contáctanos
                </a>
            </div>
        </div>
    </div>
</div>