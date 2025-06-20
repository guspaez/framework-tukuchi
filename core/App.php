<?php
/**
 * Framework Tukuchi - Aplicación Principal
 * Gestión de la aplicación y procesamiento de peticiones
 */

namespace Tukuchi\Core;

class App
{
    private $config;
    private $serviceLocator;
    private $router;

    public function __construct($config)
    {
        $this->config = $config;
        $this->serviceLocator = new ServiceLocator();
        $this->router = new Router();
        
        // Configurar servicios básicos
        $this->setupServices();
    }

    /**
     * Configurar servicios del framework
     */
    private function setupServices()
    {
        // Registrar servicios en el Service Locator
        $this->serviceLocator->register('config', $this->config);
        
        $this->serviceLocator->register('database', function() {
            return new Database($this->config['database']);
        });
        
        $this->serviceLocator->register('session', function() {
            return new Session($this->config['session'] ?? []);
        });
        
        $this->serviceLocator->register('url', function() {
            return new Url($this->config['app']['base_url']);
        });
        
        $this->serviceLocator->register('logger', function() {
            return new Logger($this->config['logging'] ?? []);
        });
        
        $this->serviceLocator->register('migrationManager', function() {
            $database = $this->serviceLocator->get('database');
            return new MigrationManager($database);
        });
    }

    /**
     * Ejecutar la aplicación
     */
    public function run()
    {
        try {
            // Inicializar sesión
            $session = $this->serviceLocator->get('session');
            $session->start();

            // Procesar URL y obtener ruta
            $url = $this->serviceLocator->get('url');
            $route = $url->parseCurrentUrl();

            // Ejecutar controlador
            $this->executeController($route);

        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

    /**
     * Ejecutar controlador basado en la ruta
     */
    private function executeController($route)
    {
        $controllerName = $this->buildControllerName($route['controller']);
        $actionName = $route['action'] . 'Action';

        if (!class_exists($controllerName)) {
            throw new \Exception("Controlador no encontrado: {$controllerName}");
        }

        $controller = new $controllerName($this->serviceLocator);
        
        if (!method_exists($controller, $actionName)) {
            throw new \Exception("Acción no encontrada: {$actionName}");
        }

        // Ejecutar acción del controlador
        $controller->$actionName($route['params']);
    }

    /**
     * Construir nombre del controlador con namespace
     */
    private function buildControllerName($controllerPath)
    {
        // Dividir la ruta por '/'
        $parts = explode('/', $controllerPath);
        
        // Construir el namespace base
        $namespace = 'Tukuchi\\App\\Controllers';
        
        // Si hay más de una parte, las primeras son subdirectorios
        if (count($parts) > 1) {
            // Agregar subdirectorios al namespace (excepto el último que es el controlador)
            for ($i = 0; $i < count($parts) - 1; $i++) {
                $namespace .= '\\' . ucfirst($parts[$i]);
            }
            
            // El último elemento es el nombre del controlador
            $controllerName = ucfirst(end($parts)) . 'Controller';
        } else {
            // Solo hay una parte, es el nombre del controlador
            $controllerName = ucfirst($parts[0]) . 'Controller';
        }
        
        // Depuración: Registrar el nombre del controlador construido
        error_log("Depuración: Nombre del controlador construido: " . $namespace . '\\' . $controllerName);
        
        return $namespace . '\\' . $controllerName;
    }

    /**
     * Manejar errores
     */
    private function handleError(\Exception $e)
    {
        if ($this->config['app']['debug']) {
            echo "<h1>Error del Framework Tukuchi</h1>";
            echo "<p><strong>Mensaje:</strong> " . $e->getMessage() . "</p>";
            echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
            echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        } else {
            // En producción, mostrar página de error genérica
            include TUKUCHI_APP_PATH . '/views/errors/500.php';
        }
    }

    /**
     * Obtener el Service Locator
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
