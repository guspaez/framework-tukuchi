<?php
/**
 * Framework Tukuchi - Test URLs
 * Script para probar la generación de URLs
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

// Simular variables de servidor para pruebas
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SCRIPT_NAME'] = '/tukuchi/index.php';
$_SERVER['REQUEST_URI'] = '/tukuchi/';

use Tukuchi\Core\View;
use Tukuchi\Core\Router;
use Tukuchi\Core\Url;

echo "🐦 Framework Tukuchi - Probando Generación de URLs\n";
echo "=================================================\n\n";

// Probar la clase View
echo "📋 Pruebas de la clase View:\n";
echo str_repeat('-', 30) . "\n";

$view = new View();

$urls = [
    ['home', 'index', []], // Página principal
    ['home', 'about', []], // Acerca de
    ['home', 'contact', []], // Contacto
    ['user', 'index', []], // Lista de usuarios
    ['user', 'show', [1]], // Ver usuario específico
    ['user', 'edit', [1]], // Editar usuario
];

foreach ($urls as $urlData) {
    list($controller, $action, $params) = $urlData;
    $url = $view->url($controller, $action, $params);
    echo "✅ {$controller}/{$action}: {$url}\n";
}

echo "\n";

// Probar la clase Router
echo "📋 Pruebas de la clase Router:\n";
echo str_repeat('-', 30) . "\n";

$router = new Router();

foreach ($urls as $urlData) {
    list($controller, $action, $params) = $urlData;
    $url = $router->generateUrl($controller, $action, $params);
    echo "✅ {$controller}/{$action}: '{$url}'\n";
}

echo "\n";

// Probar parsing de URLs
echo "📋 Pruebas de parsing de URLs:\n";
echo str_repeat('-', 30) . "\n";

$testUrls = [
    '',
    'home',
    'home/about',
    'user',
    'user/show/1',
    'user/edit/1'
];

foreach ($testUrls as $testUrl) {
    $parsed = $router->parseUrl($testUrl);
    echo "✅ '{$testUrl}' -> {$parsed['controller']}/{$parsed['action']} " . 
         (empty($parsed['params']) ? '' : '[' . implode(', ', $parsed['params']) . ']') . "\n";
}

echo "\n🎉 Pruebas de URLs completadas!\n";