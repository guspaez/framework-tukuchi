<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold"><?= $this->escape($title) ?></h1>
                <p class="lead text-muted">
                    ¿Tienes preguntas sobre Framework Tukuchi? Nos encantaría ayudarte.
                </p>
            </div>

            <?php if (isset($success) && $success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                <strong>¡Mensaje enviado!</strong> Gracias por contactarnos. Te responderemos pronto.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <strong>Error:</strong> <?= $this->escape($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="row g-5">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h4 class="card-title mb-4">Envíanos un mensaje</h4>
                            
                            <form id="contactForm" method="POST" action="<?= $this->url('home', 'contact') ?>">
                                <!-- Token CSRF -->
                                <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Nombre completo *</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Correo electrónico *</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="subject" class="form-label">Asunto</label>
                                        <select class="form-select" id="subject" name="subject">
                                            <option value="">Selecciona un tema</option>
                                            <option value="general">Consulta general</option>
                                            <option value="technical">Soporte técnico</option>
                                            <option value="business">Oportunidad de negocio</option>
                                            <option value="feedback">Comentarios y sugerencias</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="message" class="form-label">Mensaje *</label>
                                        <textarea class="form-control" id="message" name="message" rows="5" 
                                                placeholder="Cuéntanos cómo podemos ayudarte..." required></textarea>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="privacy" required>
                                            <label class="form-check-label" for="privacy">
                                                Acepto el tratamiento de mis datos personales según la política de privacidad *
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                            Enviar mensaje
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4">Información de contacto</h5>
                            
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary rounded-pill">📧</span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6>Email</h6>
                                    <p class="text-muted mb-0">info@tukuchi.com</p>
                                </div>
                            </div>

                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary rounded-pill">🌐</span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6>Sitio web</h6>
                                    <p class="text-muted mb-0">www.tukuchi.com</p>
                                </div>
                            </div>

                            <div class="d-flex mb-4">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary rounded-pill">⏰</span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6>Horario de atención</h6>
                                    <p class="text-muted mb-0">
                                        Lunes a Viernes<br>
                                        9:00 AM - 6:00 PM
                                    </p>
                                </div>
                            </div>

                            <hr>

                            <h6 class="mb-3">¿Necesitas ayuda inmediata?</h6>
                            <p class="text-muted small">
                                Revisa nuestra documentación o consulta nuestros ejemplos de código 
                                para resolver dudas comunes sobre el framework.
                            </p>
                            
                            <div class="d-grid gap-2">
                                <a href="<?= $this->url('home', 'api') ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                                    Ver API de ejemplo
                                </a>
                                <a href="<?= $this->url('home', 'about') ?>" class="btn btn-outline-secondary btn-sm">
                                    Conocer más
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->startSection('scripts') ?>
<script>
$(document).ready(function() {
    $('#contactForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const spinner = submitBtn.find('.spinner-border');
        
        // Validación básica
        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }
        
        // Mostrar loading
        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        
        // Enviar formulario via AJAX
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Mostrar mensaje de éxito
                    const alert = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>¡Mensaje enviado!</strong> ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    form.before(alert);
                    form[0].reset();
                } else {
                    throw new Error(response.message || 'Error desconocido');
                }
            },
            error: function(xhr) {
                let message = 'Error al enviar el mensaje. Inténtalo de nuevo.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                const alert = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error:</strong> ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                form.before(alert);
            },
            complete: function() {
                // Ocultar loading
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });
});
</script>
<?php $this->endSection() ?>