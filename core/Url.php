<?php
/**
 * Framework Tukuchi - Url
 * Procesamiento y construcción de URLs
 */

namespace Tukuchi\Core;

class Url
{
    private $baseUrl;
    private $router;

    public function __construct($baseUrl = '')
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->router = new Router();
    }

    /**
     * Obtener la URL actual
     */
    public function getCurrentUrl()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $uri = $_SERVER['REQUEST_URI'];
        
        return $protocol . '://' . $host . $uri;
    }

    /**
     * Obtener la ruta actual (sin dominio)
     */
    public function getCurrentPath()
    {
        $uri = $_SERVER['REQUEST_URI'];
        
        // Remover query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        // Remover el directorio base del proyecto
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = dirname($scriptName);
        
        // Si estamos en el directorio ra��z, no hacer nada
        if ($basePath !== '/' && $basePath !== '\\') {
            if (strpos($uri, $basePath) === 0) {
                $uri = substr($uri, strlen($basePath));
            }
        }
        
        return trim($uri, '/');
    }

    /**
     * Procesar URL actual y devolver información de ruta
     */
    public function parseCurrentUrl()
    {
        $path = $this->getCurrentPath();
        return $this->router->parseUrl($path);
    }

    /**
     * Construir URL completa
     */
    public function build($controller, $action = 'index', $params = [], $absolute = false)
    {
        $url = $this->router->generateUrl($controller, $action, $params);
        
        if ($absolute) {
            return $this->baseUrl . '/' . $url;
        }
        
        return $url;
    }

    /**
     * Crear URL con parámetros GET
     */
    public function buildWithQuery($controller, $action = 'index', $params = [], $queryParams = [])
    {
        $url = $this->build($controller, $action, $params);
        
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }
        
        return $url;
    }

    /**
     * Redireccionar a una URL
     */
    public function redirect($controller, $action = 'index', $params = [])
    {
        $url = $this->build($controller, $action, $params, true);
        header("Location: $url");
        exit;
    }

    /**
     * Redireccionar a URL externa
     */
    public function redirectTo($url)
    {
        header("Location: $url");
        exit;
    }

    /**
     * Obtener parámetros GET
     */
    public function getQueryParams()
    {
        return $_GET;
    }

    /**
     * Obtener un parámetro GET específico
     */
    public function getQueryParam($name, $default = null)
    {
        return isset($_GET[$name]) ? $_GET[$name] : $default;
    }

    /**
     * Verificar si es una petición AJAX
     */
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Obtener método HTTP
     */
    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Verificar si es POST
     */
    public function isPost()
    {
        return $this->getMethod() === 'POST';
    }

    /**
     * Verificar si es GET
     */
    public function isGet()
    {
        return $this->getMethod() === 'GET';
    }
}