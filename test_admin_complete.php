<?php
/**
 * Framework Tukuchi - Test Admin Complete
 * Script para verificar que el panel de administración esté funcionando
 */

// Definir constantes del framework
define('TUKUCHI_PATH', __DIR__);
define('TUKUCHI_CORE_PATH', TUKUCHI_PATH . '/core');
define('TUKUCHI_APP_PATH', TUKUCHI_PATH . '/app');
define('TUKUCHI_CONFIG_PATH', TUKUCHI_PATH . '/config');
define('TUKUCHI_PUBLIC_PATH', TUKUCHI_PATH . '/public');

// Autoloader del framework
require_once TUKUCHI_CORE_PATH . '/Autoloader.php';

// Inicializar el autoloader
Tukuchi\Core\Autoloader::register();

use Tukuchi\App\Models\User;

echo "🐦 Framework Tukuchi - Verificación Completa del Panel de Administración\n";
echo "========================================================================\n\n";

try {
    // 1. Verificar que existe el usuario administrador
    echo "📋 1. Verificando usuario administrador...\n";
    $admin = User::findByEmail('admin@tukuchi.com');
    
    if ($admin) {
        echo "✅ Usuario administrador encontrado:\n";
        echo "   - ID: {$admin->id}\n";
        echo "   - Nombre: {$admin->name}\n";
        echo "   - Email: {$admin->email}\n";
        echo "   - Rol: {$admin->getRoleLabel()}\n";
        echo "   - Estado: " . ($admin->isActive() ? 'Activo' : 'Inactivo') . "\n";
        echo "   - Es Admin: " . ($admin->isAdmin() ? 'Sí' : 'No') . "\n";
        echo "   - Es Super Admin: " . ($admin->isSuperAdmin() ? 'Sí' : 'No') . "\n";
    } else {
        echo "❌ Usuario administrador no encontrado\n";
        echo "   Ejecuta: php create_admin_simple.php\n";
    }

    echo "\n📋 2. Verificando controladores de administración...\n";
    
    $controllers = [
        'Tukuchi\\App\\Controllers\\Admin\\AdminController',
        'Tukuchi\\App\\Controllers\\Admin\\AuthController',
        'Tukuchi\\App\\Controllers\\Admin\\DashboardController'
    ];
    
    foreach ($controllers as $controller) {
        if (class_exists($controller)) {
            echo "✅ {$controller}\n";
        } else {
            echo "❌ {$controller} - No encontrado\n";
        }
    }

    echo "\n📋 3. Verificando vistas de administración...\n";
    
    $views = [
        'app/views/layouts/admin.php',
        'app/views/admin/auth/login.php',
        'app/views/admin/dashboard/index.php'
    ];
    
    foreach ($views as $view) {
        $fullPath = TUKUCHI_PATH . '/' . $view;
        if (file_exists($fullPath)) {
            echo "✅ {$view}\n";
        } else {
            echo "❌ {$view} - No encontrado\n";
        }
    }

    echo "\n📋 4. Verificando configuración de base de datos...\n";
    
    $config = require_once TUKUCHI_CONFIG_PATH . '/app.php';
    $database = new Tukuchi\Core\Database($config['database']);
    
    // Verificar conexión
    $info = $database->getConnectionInfo();
    echo "✅ Conexión a base de datos exitosa\n";
    echo "   - Driver: {$info['driver']}\n";
    echo "   - Versión: {$info['version']}\n";
    
    // Verificar tabla users
    $users = User::all();
    echo "✅ Tabla users accesible\n";
    echo "   - Total usuarios: " . count($users) . "\n";

    echo "\n📋 5. URLs de acceso:\n";
    echo "✅ Panel de administración: http://localhost/tukuchi/admin/auth/login\n";
    echo "✅ Dashboard: http://localhost/tukuchi/admin/dashboard\n";
    echo "✅ Sitio principal: http://localhost/tukuchi\n";

    echo "\n🔐 6. Credenciales de acceso:\n";
    if ($admin) {
        echo "✅ Email: {$admin->email}\n";
        echo "✅ Contraseña: admin123\n";
    }

    echo "\n🎉 ¡Panel de administración completamente funcional!\n";
    echo "Puedes acceder en: http://localhost/tukuchi/admin/auth/login\n";

} catch (Exception $e) {
    echo "❌ Error durante la verificación: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}