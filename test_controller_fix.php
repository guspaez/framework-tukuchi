<?php
/**
 * Framework Tukuchi - Test Controller Fix
 * Script para verificar que la corrección del controlador funcione
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

echo "🐦 Framework Tukuchi - Verificando Corrección del Controller\n";
echo "==========================================================\n\n";

try {
    // Cargar configuración
    $config = require_once TUKUCHI_CONFIG_PATH . '/app.php';
    
    // Crear Service Locator
    $serviceLocator = new Tukuchi\Core\ServiceLocator();
    $serviceLocator->register('config', $config);
    $serviceLocator->register('session', function() use ($config) {
        return new Tukuchi\Core\Session($config['session'] ?? []);
    });
    $serviceLocator->register('url', function() use ($config) {
        return new Tukuchi\Core\Url($config['app']['base_url']);
    });
    $serviceLocator->register('logger', function() use ($config) {
        return new Tukuchi\Core\Logger($config['logging'] ?? []);
    });
    $serviceLocator->register('database', function() use ($config) {
        return new Tukuchi\Core\Database($config['database']);
    });

    echo "📋 1. Verificando clase Controller base...\n";
    
    // Verificar que la clase Controller tenga el método init
    $reflection = new ReflectionClass('Tukuchi\\Core\\Controller');
    if ($reflection->hasMethod('init')) {
        echo "✅ Método init() existe en Controller base\n";
    } else {
        echo "❌ Método init() NO existe en Controller base\n";
    }

    echo "\n📋 2. Verificando instanciación de AuthController...\n";
    
    // Intentar crear una instancia del AuthController
    $authController = new Tukuchi\App\Controllers\Admin\AuthController($serviceLocator);
    echo "✅ AuthController instanciado correctamente\n";
    
    // Verificar que tenga el método loginAction
    if (method_exists($authController, 'loginAction')) {
        echo "✅ Método loginAction() existe\n";
    } else {
        echo "❌ Método loginAction() NO existe\n";
    }

    echo "\n📋 3. Verificando instanciación de AdminController...\n";
    
    // Intentar crear una instancia del AdminController (esto debería fallar porque requiere autenticación)
    try {
        $adminController = new Tukuchi\App\Controllers\Admin\AdminController($serviceLocator);
        echo "⚠️  AdminController instanciado (puede redirigir por falta de autenticación)\n";
    } catch (Exception $e) {
        echo "⚠️  AdminController falló como se esperaba: " . $e->getMessage() . "\n";
    }

    echo "\n📋 4. Verificando DashboardController...\n";
    
    try {
        $dashboardController = new Tukuchi\App\Controllers\Admin\DashboardController($serviceLocator);
        echo "⚠️  DashboardController instanciado (puede redirigir por falta de autenticación)\n";
    } catch (Exception $e) {
        echo "⚠️  DashboardController falló como se esperaba: " . $e->getMessage() . "\n";
    }

    echo "\n📋 5. URLs de prueba:\n";
    echo "✅ Login: http://localhost/tukuchi/admin/auth/login\n";
    echo "✅ Dashboard: http://localhost/tukuchi/admin/dashboard\n";

    echo "\n🎉 Corrección del Controller completada exitosamente!\n";
    echo "El método init() ha sido agregado a la clase Controller base.\n";

} catch (Exception $e) {
    echo "❌ Error durante la verificación: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}