# 🐦 Framework Tukuchi

![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue)
![License](https://img.shields.io/badge/license-MIT-green)
![Estado](https://img.shields.io/badge/estado-en%20desarrollo-yellow)

**Potenciando la Transformación Digital**

Tukuchi es un framework PHP ágil y seguro, diseñado para acelerar el desarrollo de aplicaciones web modernas. Pensado especialmente para pequeños negocios y equipos que buscan rapidez, buenas prácticas y facilidad de uso, Tukuchi ofrece una arquitectura MVC robusta, validación avanzada, seguridad integrada y herramientas para el desarrollo profesional.

---

## Tabla de Contenidos

- [Características Principales](#-características-principales)
- [Requisitos](#-requisitos)
- [Instalación](#-instalación)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Inicio Rápido](#-inicio-rápido)
- [Guía "Hello World"](#-guía-hello-world)
- [Configuración](#-configuración)
- [Seguridad](#-seguridad)
- [API y AJAX](#-api-y-ajax)
- [Vistas y Layouts](#-vistas-y-layouts)
- [Routing](#-routing)
- [Base de Datos](#-base-de-datos)
- [Validación Avanzada](#-validación-avanzada)
- [Sistema de Logging](#-sistema-de-logging)
- [Herramienta CLI](#-herramienta-cli)
- [Testing](#-testing)
- [Contribuir](#-contribuir)
- [Licencia](#-licencia)
- [¿Por qué Tukuchi?](#-por-qué-tukuchi)
- [Documentación Adicional](#-documentación-adicional)

---

## 🚀 Características Principales

- **Arquitectura MVC**: Separación clara de responsabilidades.
- **Inyección de Dependencias**: Service Locator integrado.
- **Routing Flexible**: Sistema de rutas fácil de configurar.
- **Seguridad Integrada**: Protección CSRF, validación de datos.
- **Fácil de Usar**: Desarrollo rápido con componentes reutilizables.

---

## 📋 Requisitos

- PHP 7.4 o superior
- Apache con mod_rewrite habilitado
- MySQL 5.7 o superior (opcional)

---

## 🛠️ Instalación

1. **Clonar o descargar** el framework en tu directorio web:
   ```bash
   cd /path/to/your/webserver
   git clone [repository-url] tukuchi
   ```

2. **Configurar la base de datos** (opcional):
   - Edita `config/app.php`
   - Configura los parámetros de conexión a la base de datos

3. **Configurar permisos**:
   ```bash
   chmod -R 755 tukuchi/
   chmod -R 777 tukuchi/storage/
   ```

4. **Acceder a tu aplicación**:
   ```
   http://localhost/tukuchi
   ```

---

## 📁 Estructura del Proyecto

```
tukuchi/
├── app/                    # Aplicación
│   ├── Controllers/        # Controladores
│   ├── Models/             # Modelos
│   └── views/              # Vistas
│       ├── layouts/        # Layouts
│       └── home/           # Vistas del controlador Home
├── config/                 # Configuración
│   └── app.php             # Configuración principal
├── core/                   # Núcleo del framework
│   ├── App.php             # Aplicación principal
│   ├── Controller.php      # Controlador base
│   ├── CoreObject.php      # Objeto base
│   ├── ServiceLocator.php  # Inyector de dependencias
│   └── ...                 # Otros componentes core
├── public/                 # Archivos públicos
│   ├── css/                # Estilos CSS
│   ├── js/                 # JavaScript
│   └── uploads/            # Archivos subidos
├── storage/                # Almacenamiento
│   ├── cache/              # Caché
│   └── logs/               # Logs
├── .htaccess               # Configuración Apache
├── index.php               # Punto de entrada
└── README.md               # Este archivo
```

---

## 🎯 Inicio Rápido

### 1. Crear un Controlador

```php
<?php
namespace Tukuchi\App\Controllers;

use Tukuchi\Core\Controller;

class MiControlador extends Controller
{
    public function indexAction($params = [])
    {
        $data = [
            'titulo' => 'Mi Página',
            'mensaje' => 'Hola Mundo!'
        ];
        
        $this->renderWithLayout('mi-vista/index', $data);
    }
}
```

### 2. Crear una Vista

Crear archivo `app/views/mi-vista/index.php`:

```php
<div class="container">
    <h1><?= $this->escape($titulo) ?></h1>
    <p><?= $this->escape($mensaje) ?></p>
</div>
```

### 3. Acceder a la Página

```
http://localhost/tukuchi/mi-controlador
```

---

## 👋 Guía "Hello World"

1. Crea un controlador en `app/Controllers/HelloController.php`:

```php
<?php
namespace Tukuchi\App\Controllers;

use Tukuchi\Core\Controller;

class HelloController extends Controller
{
    public function indexAction()
    {
        $data = ['mensaje' => '¡Hola, mundo desde Tukuchi!'];
        $this->renderWithLayout('hello/index', $data);
    }
}
```

2. Crea la vista en `app/views/hello/index.php`:

```php
<h1><?= $this->escape($mensaje) ?></h1>
```

3. Accede en tu navegador a:

```
http://localhost/tukuchi/hello
```

---

## 🔧 Configuración

### Base de Datos

Edita `config/app.php`:

```php
'database' => [
    'default' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'mi_base_datos',
        'username' => 'usuario',
        'password' => 'contraseña',
        // ...
    ]
]
```

### URLs Base

```php
'app' => [
    'base_url' => 'http://localhost/tukuchi',
    // ...
]
```

---

## 🛡️ Seguridad

### Protección CSRF

```php
// En el controlador
$token = $this->generateCsrfToken();

// En la vista
<input type="hidden" name="_token" value="<?= $csrf_token ?>">

// Validar en el controlador
$this->validateCsrfToken();
```

### Validación de Datos

```php
// Obtener datos POST sanitizados
$nombre = $this->getPost('nombre');
$email = $this->getPost('email');

// Validación
if (!$this->request->validateEmail($email)) {
    throw new \Exception('Email inválido');
}
```

---

## 📡 API y AJAX

### Respuesta JSON

```php
public function apiAction($params = [])
{
    $data = [
        'status' => 'success',
        'message' => 'Datos obtenidos correctamente',
        'data' => $this->obtenerDatos()
    ];
    
    $this->json($data);
}
```

### Peticiones AJAX (JavaScript)

```javascript
Tukuchi.utils.ajax({
    url: '/mi-controlador/api',
    method: 'POST',
    body: { param1: 'valor1' }
})
.then(response => {
    console.log(response);
})
.catch(error => {
    console.error(error);
});
```

---

## 🎨 Vistas y Layouts

### Layout Principal

```php
// app/views/layouts/main.php
<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?? 'Mi App' ?></title>
</head>
<body>
    <?= $content ?>
</body>
</html>
```

### Usar Layout

```php
$this->renderWithLayout('mi-vista', $data, 'main');
```

---

## 🔄 Routing

### Rutas Personalizadas

```php
// En el constructor de tu controlador o en un archivo de rutas
$router = new Router();
$router->addRoute('productos/{id}', 'producto', 'detalle');
```

### URLs Amigables

```php
// Generar URL
$url = $this->url('producto', 'detalle', ['id' => 123]);
// Resultado: /producto/detalle/123
```

---

## 📊 Base de Datos

El framework incluye un sistema completo de base de datos:

### Configuración
```php
// config/app.php
'database' => [
    'default' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'mi_base_datos',
        'username' => 'usuario',
        'password' => 'contraseña',
        // ...
    ]
]
```

### Modelos ActiveRecord
```php
// app/Models/User.php
class User extends Model
{
    protected $fillable = ['name', 'email', 'password'];
    
    public static function findByEmail($email)
    {
        return static::where('email', $email)[0] ?? null;
    }
}

// Uso
$user = new User(['name' => 'Juan', 'email' => 'juan@example.com']);
$user->save();

$user = User::find(1);
$users = User::all();
$activeUsers = User::where('status', 'active');
```

### Migraciones
```bash
# Crear migración
php tukuchi.php make:migration create_users_table

# Ejecutar migraciones
php tukuchi.php migrate

# Revertir migraciones
php tukuchi.php migrate:rollback

# Ver estado
php tukuchi.php migrate:status
```

### Ejemplo de Migración
```php
class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->schema->create('users', function($table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->timestamps();
        });
    }
    
    public function down()
    {
        $this->schema->drop('users');
    }
}
```

---

## 🔍 Validación Avanzada

Sistema de validación robusto para formularios:

```php
use Tukuchi\Core\Validator;

$validator = Validator::make($data, [
    'name' => 'required|min:2|max:100|alpha',
    'email' => 'required|email|unique:users',
    'password' => 'required|min:6|confirmed',
    'age' => 'numeric|between:18,65'
], [
    'name.required' => 'El nombre es obligatorio',
    'email.unique' => 'Este email ya está registrado'
]);

if ($validator->fails()) {
    $errors = $validator->errors();
}
```

### Reglas Disponibles
- `required`, `email`, `numeric`, `integer`
- `min:n`, `max:n`, `between:min,max`
- `alpha`, `alpha_num`, `url`
- `confirmed`, `same:field`, `different:field`
- `in:value1,value2`, `not_in:value1,value2`
- `regex:pattern`, `date`, `before:date`, `after:date`

---

## 📝 Sistema de Logging

Logging completo con diferentes niveles:

```php
$logger = $this->getService('logger');

$logger->debug('Información de debug');
$logger->info('Información general');
$logger->warning('Advertencia');
$logger->error('Error', ['context' => $data]);
$logger->critical('Error crítico');

// Log de excepciones
$logger->logException($exception);
```

### Configuración de Logs
```php
'logging' => [
    'enabled' => true,
    'level' => 'info', // debug, info, warning, error, critical
    'path' => TUKUCHI_PATH . '/storage/logs',
    'max_file_size' => 10485760, // 10MB
    'max_files' => 5
]
```

---

## 🛠️ Herramienta CLI

Comandos de línea para gestión del framework:

```bash
# Migraciones
php tukuchi.php migrate
php tukuchi.php migrate:rollback
php tukuchi.php make:migration create_products_table

# Base de datos
php tukuchi.php db:create
php tukuchi.php test:connection

# Mantenimiento
php tukuchi.php logs:clear
php tukuchi.php cache:clear

# Información
php tukuchi.php version
php tukuchi.php help
```

---

## 🧪 Testing

Sistema de testing integrado (en desarrollo):
- TestCase base
- Mocks y fixtures
- Pruebas unitarias e integración

---

## 🤝 Contribuir

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -am 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Crea un Pull Request

---

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

---

## 🐦 ¿Por qué Tukuchi?

Tukuchi significa "colibrí" en la lengua Yepuana. Como un colibrí, este framework es:
- **Ágil**: Desarrollo rápido y preciso
- **Eficiente**: Consume pocos recursos
- **Versátil**: Se adapta a diferentes necesidades
- **Hermoso**: Código limpio y elegante

---

## 📚 Documentación Adicional

- [Documentación completa del framework](documentacion/FrameworkTukuchi.txt)

---

**Framework Tukuchi** - Potenciando la Transformación Digital 🚀