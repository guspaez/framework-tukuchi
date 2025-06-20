<?php
/**
 * Framework Tukuchi - Test Admin Routing
 * Script para probar el routing de administración
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

use Tukuchi\Core\Router;
use Tukuchi\Core\App;

echo "🐦 Framework Tukuchi - Probando Routing de Administración\n";
echo "========================================================\n\n";

// Probar el Router
$router = new Router();

$testUrls = [
    'admin/auth/login',
    'admin/dashboard',
    'admin/users',
    'user/index',
    'home/about',
    ''
];

echo "📋 Pruebas del Router:\n";
echo str_repeat('-', 40) . "\n";

foreach ($testUrls as $testUrl) {
    $parsed = $router->parseUrl($testUrl);
    echo "✅ '{$testUrl}' -> {$parsed['controller']}/{$parsed['action']} " . 
         (empty($parsed['params']) ? '' : '[' . implode(', ', $parsed['params']) . ']') . "\n";
}

echo "\n📋 Pruebas de construcción de nombres de controlador:\n";
echo str_repeat('-', 50) . "\n";

// Simular la clase App para probar buildControllerName
class TestApp extends Tukuchi\Core\App {
    public function testBuildControllerName($controllerPath) {
        // Hacer público el método privado para testing
        $reflection = new ReflectionClass($this);
        $method = $reflection->getMethod('buildControllerName');
        $method->setAccessible(true);
        return $method->invoke($this, $controllerPath);
    }
}

$config = require_once TUKUCHI_CONFIG_PATH . '/app.php';
$testApp = new TestApp($config);

$controllerPaths = [
    'admin/auth',
    'admin/dashboard',
    'user',
    'home'
];

foreach ($controllerPaths as $path) {
    try {
        $className = $testApp->testBuildControllerName($path);
        $exists = class_exists($className) ? '✅' : '❌';
        echo "{$exists} '{$path}' -> {$className}\n";
    } catch (Exception $e) {
        echo "❌ '{$path}' -> Error: " . $e->getMessage() . "\n";
    }
}

echo "\n🎉 Pruebas de routing completadas!\n";