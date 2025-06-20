<?php
/**
 * Framework Tukuchi - Autoloader
 * Sistema de carga automática de clases
 */

namespace Tukuchi\Core;

class Autoloader
{
    private static $registered = false;
    private static $namespaces = [];

    /**
     * Registrar el autoloader
     */
    public static function register()
    {
        if (!self::$registered) {
            spl_autoload_register([__CLASS__, 'load']);
            self::$registered = true;
            
            // Registrar namespaces del framework
            self::addNamespace('Tukuchi\\Core', TUKUCHI_CORE_PATH);
            self::addNamespace('Tukuchi\\App', TUKUCHI_APP_PATH);
        }
    }

    /**
     * Agregar un namespace
     */
    public static function addNamespace($namespace, $path)
    {
        self::$namespaces[$namespace] = $path;
    }

    /**
     * Cargar una clase
     */
    public static function load($className)
    {
        // Convertir namespace a ruta de archivo
        foreach (self::$namespaces as $namespace => $path) {
            if (strpos($className, $namespace) === 0) {
                $relativePath = substr($className, strlen($namespace));
                $filePath = $path . str_replace('\\', DIRECTORY_SEPARATOR, $relativePath) . '.php';
                
                if (file_exists($filePath)) {
                    require_once $filePath;
                    return true;
                }
            }
        }

        // Buscar en el directorio core por defecto
        $filePath = TUKUCHI_CORE_PATH . '/' . str_replace('\\', '/', $className) . '.php';
        if (file_exists($filePath)) {
            require_once $filePath;
            return true;
        }

        return false;
    }
}