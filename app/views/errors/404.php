<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada | Framework Tukuchi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .error-code {
            font-size: 8rem;
            font-weight: bold;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="container text-center">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="error-code">404</div>
                    <h1 class="display-4 mb-4">Página no encontrada</h1>
                    <p class="lead mb-4">
                        Lo sentimos, la página que buscas no existe o ha sido movida.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="/" class="btn btn-light btn-lg">
                            🏠 Ir al inicio
                        </a>
                        <button onclick="history.back()" class="btn btn-outline-light btn-lg">
                            ← Volver atrás
                        </button>
                    </div>
                    <div class="mt-5">
                        <small class="text-light opacity-75">
                            🐦 Framework Tukuchi - Potenciando la Transformación Digital
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>