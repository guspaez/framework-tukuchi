#!/usr/bin/env php
<?php
/**
 * Framework Tukuchi - CLI Tool
 * Herramienta de línea de comandos para el framework
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

// Procesar argumentos de línea de comandos PRIMERO
$command = $argv[1] ?? 'help';
$args = array_slice($argv, 2);

// Cargar configuración
$config = require_once TUKUCHI_CONFIG_PATH . '/app.php';

// Variables globales para usar en funciones
$globalConfig = $config;
$database = null;
$migrationManager = null;

// Solo crear instancias si no es comando de creación de BD
if ($command !== 'db:create' && $command !== 'help' && $command !== 'version') {
    try {
        $database = new Tukuchi\Core\Database($config['database']);
        $migrationManager = new Tukuchi\Core\MigrationManager($database);
    } catch (Exception $e) {
        echo "❌ Error de conexión a BD. Usa 'php tukuchi.php db:create' primero.\n";
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "🐦 Framework Tukuchi - CLI Tool\n";
echo "================================\n\n";

try {
    switch ($command) {
        case 'migrate':
            echo "Ejecutando migraciones...\n";
            $migrationManager->migrate();
            break;
            
        case 'migrate:rollback':
            $steps = isset($args[0]) ? (int)$args[0] : 1;
            echo "Revirtiendo {$steps} migración(es)...\n";
            $migrationManager->rollback($steps);
            break;
            
        case 'migrate:status':
            echo "Estado de migraciones:\n";
            $migrationManager->status();
            break;
            
        case 'make:migration':
            if (empty($args[0])) {
                echo "Error: Debes proporcionar un nombre para la migración.\n";
                echo "Uso: php tukuchi.php make:migration create_users_table\n";
                exit(1);
            }
            
            $name = $args[0];
            echo "Creando migración: {$name}\n";
            $filePath = $migrationManager->makeMigration($name);
            echo "Migración creada en: {$filePath}\n";
            break;
            
        case 'db:create':
            echo "Creando base de datos...\n";
            createDatabase($globalConfig);
            break;
            
        case 'test:connection':
            echo "Probando conexión a la base de datos...\n";
            testDatabaseConnection($database);
            break;
            
        case 'logs:clear':
            echo "Limpiando logs...\n";
            clearLogs();
            break;
            
        case 'cache:clear':
            echo "Limpiando caché...\n";
            clearCache();
            break;
            
        case 'version':
            echo "Framework Tukuchi v1.0.0\n";
            echo "PHP " . PHP_VERSION . "\n";
            break;
            
        case 'help':
        default:
            showHelp();
            break;
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n✅ Comando ejecutado exitosamente.\n";

/**
 * Mostrar ayuda
 */
function showHelp()
{
    echo "Comandos disponibles:\n\n";
    echo "  migrate              Ejecutar migraciones pendientes\n";
    echo "  migrate:rollback     Revertir migraciones (opcional: número de pasos)\n";
    echo "  migrate:status       Mostrar estado de migraciones\n";
    echo "  make:migration       Crear nueva migración\n";
    echo "  db:create            Crear base de datos\n";
    echo "  test:connection      Probar conexión a la base de datos\n";
    echo "  logs:clear           Limpiar archivos de log\n";
    echo "  cache:clear          Limpiar archivos de caché\n";
    echo "  version              Mostrar versión del framework\n";
    echo "  help                 Mostrar esta ayuda\n\n";
    
    echo "Ejemplos:\n";
    echo "  php tukuchi.php migrate\n";
    echo "  php tukuchi.php make:migration create_products_table\n";
    echo "  php tukuchi.php migrate:rollback 2\n";
    echo "  php tukuchi.php migrate:status\n\n";
}

/**
 * Crear base de datos
 */
function createDatabase($config)
{
    $dbConfig = $config['database']['default'];
    
    try {
        // Conectar sin especificar base de datos
        $dsn = "{$dbConfig['driver']}:host={$dbConfig['host']};port={$dbConfig['port']};charset={$dbConfig['charset']}";
        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);
        
        // Crear base de datos
        $dbName = $dbConfig['database'];
        $sql = "CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET {$dbConfig['charset']} COLLATE {$dbConfig['collation']}";
        $pdo->exec($sql);
        
        echo "Base de datos '{$dbName}' creada exitosamente.\n";
        
    } catch (PDOException $e) {
        throw new Exception("Error creando base de datos: " . $e->getMessage());
    }
}

/**
 * Probar conexión a la base de datos
 */
function testDatabaseConnection($database)
{
    try {
        $info = $database->getConnectionInfo();
        
        echo "✅ Conexión exitosa!\n";
        echo "Driver: {$info['driver']}\n";
        echo "Versión: {$info['version']}\n";
        echo "Estado: {$info['connection_status']}\n";
        
        // Probar una consulta simple
        $result = $database->fetchColumn("SELECT 1");
        if ($result == 1) {
            echo "✅ Consulta de prueba exitosa.\n";
        }
        
    } catch (Exception $e) {
        throw new Exception("Error de conexión: " . $e->getMessage());
    }
}

/**
 * Limpiar logs
 */
function clearLogs()
{
    $logsPath = TUKUCHI_PATH . '/storage/logs';
    
    if (!is_dir($logsPath)) {
        echo "Directorio de logs no existe.\n";
        return;
    }
    
    $files = glob($logsPath . '/*.log*');
    $count = 0;
    
    foreach ($files as $file) {
        if (unlink($file)) {
            $count++;
        }
    }
    
    echo "Eliminados {$count} archivos de log.\n";
}

/**
 * Limpiar caché
 */
function clearCache()
{
    $cachePath = TUKUCHI_PATH . '/storage/cache';
    
    if (!is_dir($cachePath)) {
        echo "Directorio de caché no existe.\n";
        return;
    }
    
    $files = glob($cachePath . '/*');
    $count = 0;
    
    foreach ($files as $file) {
        if (is_file($file) && unlink($file)) {
            $count++;
        }
    }
    
    echo "Eliminados {$count} archivos de caché.\n";
}