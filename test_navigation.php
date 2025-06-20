<?php
/**
 * Framework Tukuchi - Test Navigation
 * Script para probar la navegación entre páginas
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

// Cargar configuración
$config = require_once TUKUCHI_CONFIG_PATH . '/app.php';

use Tukuchi\Core\App;

echo "🐦 Framework Tukuchi - Probando Navegación\n";
echo "==========================================\n\n";

// Simular diferentes escenarios de navegación
$scenarios = [
    [
        'description' => 'Página principal',
        'REQUEST_URI' => '/tukuchi/',
        'expected_controller' => 'home',
        'expected_action' => 'index'
    ],
    [
        'description' => 'Acerca de',
        'REQUEST_URI' => '/tukuchi/home/about',
        'expected_controller' => 'home',
        'expected_action' => 'about'
    ],
    [
        'description' => 'Contacto',
        'REQUEST_URI' => '/tukuchi/home/contact',
        'expected_controller' => 'home',
        'expected_action' => 'contact'
    ],
    [
        'description' => 'Lista de usuarios',
        'REQUEST_URI' => '/tukuchi/user',
        'expected_controller' => 'user',
        'expected_action' => 'index'
    ],
    [
        'description' => 'Ver usuario específico',
        'REQUEST_URI' => '/tukuchi/user/show/1',
        'expected_controller' => 'user',
        'expected_action' => 'show'
    ]
];

foreach ($scenarios as $scenario) {
    echo "📋 Probando: {$scenario['description']}\n";
    echo "URL: {$scenario['REQUEST_URI']}\n";
    
    // Simular variables de servidor
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['SCRIPT_NAME'] = '/tukuchi/index.php';
    $_SERVER['REQUEST_URI'] = $scenario['REQUEST_URI'];
    $_SERVER['REQUEST_METHOD'] = 'GET';
    
    try {
        // Crear instancia de la aplicación
        $app = new App($config);
        
        // Obtener el service locator para probar el routing
        $serviceLocator = $app->getServiceLocator();
        $url = $serviceLocator->get('url');
        
        // Parsear la URL actual
        $route = $url->parseCurrentUrl();
        
        echo "✅ Controlador: {$route['controller']}\n";
        echo "✅ Acción: {$route['action']}\n";
        
        if (!empty($route['params'])) {
            echo "✅ Parámetros: [" . implode(', ', $route['params']) . "]\n";
        }
        
        // Verificar si coincide con lo esperado
        if ($route['controller'] === $scenario['expected_controller'] && 
            $route['action'] === $scenario['expected_action']) {
            echo "🎉 ¡Routing correcto!\n";
        } else {
            echo "❌ Error en routing. Esperado: {$scenario['expected_controller']}/{$scenario['expected_action']}\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
    
    echo str_repeat('-', 50) . "\n\n";
}

echo "🎯 Prueba de generación de URLs desde diferentes contextos:\n";
echo str_repeat('-', 50) . "\n";

// Simular estar en la página "about"
$_SERVER['REQUEST_URI'] = '/tukuchi/home/about';

$app = new App($config);
$serviceLocator = $app->getServiceLocator();

// Crear una vista para probar la generación de URLs
$view = new \Tukuchi\Core\View();

echo "Desde /home/about, generar URLs:\n";
echo "- Inicio: " . $view->url('home') . "\n";
echo "- Acerca de: " . $view->url('home', 'about') . "\n";
echo "- Contacto: " . $view->url('home', 'contact') . "\n";
echo "- Usuarios: " . $view->url('user') . "\n";

echo "\n🎉 Pruebas de navegación completadas!\n";