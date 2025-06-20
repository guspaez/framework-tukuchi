<?php
/**
 * Framework Tukuchi - Punto de entrada principal
 * Potenciando la Transformación Digital
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

// Inicializar la aplicación
$app = new Tukuchi\Core\App($config);

// Ejecutar la aplicación con depuración
error_log("Depuración: Iniciando aplicación Tukuchi");
$app->run();
error_log("Depuración: Aplicación Tukuchi ejecutada");
