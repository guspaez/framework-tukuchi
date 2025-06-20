<?php
/**
 * Framework Tukuchi - Test Logging
 * Script para probar el sistema de logging
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

// Crear logger
$logger = new Tukuchi\Core\Logger($config['logging']);

echo "🐦 Framework Tukuchi - Probando Sistema de Logging\n";
echo "=================================================\n\n";

try {
    // Probar diferentes niveles de log
    $logger->debug('Mensaje de debug', ['user_id' => 1, 'action' => 'test']);
    echo "✅ Log de debug creado\n";

    $logger->info('Usuario inició sesión', ['user_id' => 1, 'ip' => '127.0.0.1']);
    echo "✅ Log de info creado\n";

    $logger->warning('Intento de acceso no autorizado', ['ip' => '192.168.1.100']);
    echo "✅ Log de warning creado\n";

    $logger->error('Error de conexión a base de datos', ['error' => 'Connection timeout']);
    echo "✅ Log de error creado\n";

    $logger->critical('Sistema fuera de línea', ['timestamp' => time()]);
    echo "✅ Log crítico creado\n";

    // Probar log de excepción
    try {
        throw new Exception('Esta es una excepción de prueba');
    } catch (Exception $e) {
        $logger->logException($e);
        echo "✅ Log de excepción creado\n";
    }

    echo "\n📁 Logs guardados en: " . $config['logging']['path'] . "\n";
    
    // Mostrar logs recientes
    echo "\n📋 Logs recientes:\n";
    echo str_repeat('-', 50) . "\n";
    
    $recentLogs = $logger->getRecentLogs(10);
    foreach ($recentLogs as $log) {
        echo $log . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}