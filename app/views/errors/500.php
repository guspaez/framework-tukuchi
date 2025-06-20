<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Error del servidor | Framework Tukuchi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
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
                    <div class="error-code">500</div>
                    <h1 class="display-4 mb-4">Error interno del servidor</h1>
                    <p class="lead mb-4">
                        Algo salió mal en nuestro servidor. Estamos trabajando para solucionarlo.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="/" class="btn btn-light btn-lg">
                            🏠 Ir al inicio
                        </a>
                        <button onclick="location.reload()" class="btn btn-outline-light btn-lg">
                            🔄 Intentar de nuevo
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