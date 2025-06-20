<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->escape($title) ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --admin-primary: #2c3e50;
            --admin-secondary: #34495e;
            --admin-accent: #3498db;
        }

        body {
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            max-width: 400px;
            width: 100%;
        }

        .login-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-accent) 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .login-header h3 {
            margin: 0;
            font-weight: 600;
        }

        .login-header .subtitle {
            opacity: 0.9;
            margin-top: 0.5rem;
        }

        .login-body {
            padding: 2rem;
        }

        .form-floating .form-control {
            border: 2px solid #e9ecef;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .form-floating .form-control:focus {
            border-color: var(--admin-accent);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .btn-admin {
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-accent) 100%);
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .alert {
            border-radius: 0.5rem;
            border: none;
        }

        .form-check-input:checked {
            background-color: var(--admin-accent);
            border-color: var(--admin-accent);
        }

        .login-footer {
            background-color: #f8f9fa;
            padding: 1rem 2rem;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }

        .brand-logo {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .floating-shapes::before,
        .floating-shapes::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 6s ease-in-out infinite;
        }

        .floating-shapes::before {
            width: 200px;
            height: 200px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-shapes::after {
            width: 150px;
            height: 150px;
            bottom: 10%;
            right: 10%;
            animation-delay: 3s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <div class="floating-shapes"></div>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-container mx-auto">
                    <div class="login-card">
                        <div class="login-header">
                            <div class="brand-logo">🐦</div>
                            <h3>Panel de Administración</h3>
                            <div class="subtitle">Framework Tukuchi</div>
                        </div>
                        
                        <div class="login-body">
                            <?php if ($error): ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <?= $this->escape($error) ?>
                            </div>
                            <?php endif; ?>

                            <?php if ($success): ?>
                            <div class="alert alert-success d-flex align-items-center" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <?= $this->escape($success) ?>
                            </div>
                            <?php endif; ?>

                            <form method="POST" action="<?= $this->url('admin/auth', 'authenticate') ?>" id="loginForm">
                                <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                                
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="email" name="email" 
                                           placeholder="admin@tukuchi.com" required>
                                    <label for="email">
                                        <i class="bi bi-envelope me-2"></i>Correo Electrónico
                                    </label>
                                </div>
                                
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Contraseña" required>
                                    <label for="password">
                                        <i class="bi bi-lock me-2"></i>Contraseña
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                                    <label class="form-check-label" for="remember">
                                        Recordar sesión
                                    </label>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-admin text-white">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>
                                        Iniciar Sesión
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <div class="login-footer">
                            <small class="text-muted">
                                <i class="bi bi-shield-check me-1"></i>
                                Acceso seguro al panel de administración
                            </small>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="<?= $this->url('home') ?>" class="text-white text-decoration-none">
                            <i class="bi bi-arrow-left me-2"></i>Volver al sitio web
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Focus en el primer campo
            $('#email').focus();
            
            // Auto-hide alerts
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);
            
            // Nota: Se ha desactivado temporalmente la validación del formulario y el manejo del botón
            // para diagnosticar problemas de redirección.
            /*
            // Validación del formulario
            $('#loginForm').on('submit', function(e) {
                const email = $('#email').val();
                const password = $('#password').val();
                
                if (!email || !password) {
                    e.preventDefault();
                    alert('Por favor completa todos los campos');
                    return false;
                }
                
                // Mostrar loading en el botón
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="bi bi-hourglass-split me-2"></i>Iniciando sesión...');
                submitBtn.prop('disabled', true);
                
                // Si hay error, restaurar el botón después de 3 segundos
                setTimeout(function() {
                    submitBtn.html(originalText);
                    submitBtn.prop('disabled', false);
                }, 3000);
            });
            */
        });
    </script>
</body>
</html>
