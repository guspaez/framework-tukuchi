<?php
/**
 * Framework Tukuchi - View
 * Sistema de vistas y plantillas
 */

namespace Tukuchi\Core;

class View
{
    private $viewPath;
    private $layoutPath;
    private $data = [];
    private $sections = [];
    private $currentSection = null;

    public function __construct()
    {
        $this->viewPath = TUKUCHI_APP_PATH . '/views';
        $this->layoutPath = TUKUCHI_APP_PATH . '/views/layouts';
    }

    /**
     * Renderizar una vista
     */
    public function render($template, $data = [])
    {
        $this->data = array_merge($this->data, $data);
        
        $templateFile = $this->viewPath . '/' . $template . '.php';
        
        if (!file_exists($templateFile)) {
            throw new \Exception("Vista no encontrada: {$templateFile}");
        }
        
        // Extraer variables para la vista
        extract($this->data);
        
        // Capturar salida
        ob_start();
        include $templateFile;
        $content = ob_get_clean();
        
        echo $content;
    }

    /**
     * Renderizar vista con layout
     */
    public function renderWithLayout($template, $data = [], $layout = 'main')
    {
        $this->data = array_merge($this->data, $data);
        
        // Renderizar la vista principal
        $templateFile = $this->viewPath . '/' . $template . '.php';
        
        if (!file_exists($templateFile)) {
            throw new \Exception("Vista no encontrada: {$templateFile}");
        }
        
        // Extraer variables para la vista
        extract($this->data);
        
        // Capturar contenido de la vista
        ob_start();
        include $templateFile;
        $content = ob_get_clean();
        
        // Renderizar layout
        $layoutFile = $this->layoutPath . '/' . $layout . '.php';
        
        if (!file_exists($layoutFile)) {
            throw new \Exception("Layout no encontrado: {$layoutFile}");
        }
        
        // Hacer disponible el contenido en el layout
        $this->data['content'] = $content;
        extract($this->data);
        
        include $layoutFile;
    }

    /**
     * Incluir una vista parcial
     */
    public function partial($template, $data = [])
    {
        $partialData = array_merge($this->data, $data);
        $templateFile = $this->viewPath . '/partials/' . $template . '.php';
        
        if (!file_exists($templateFile)) {
            throw new \Exception("Vista parcial no encontrada: {$templateFile}");
        }
        
        extract($partialData);
        include $templateFile;
    }

    /**
     * Iniciar una sección
     */
    public function startSection($name)
    {
        $this->currentSection = $name;
        ob_start();
    }

    /**
     * Finalizar una sección
     */
    public function endSection()
    {
        if ($this->currentSection) {
            $this->sections[$this->currentSection] = ob_get_clean();
            $this->currentSection = null;
        }
    }

    /**
     * Mostrar una sección
     */
    public function showSection($name, $default = '')
    {
        return isset($this->sections[$name]) ? $this->sections[$name] : $default;
    }

    /**
     * Escapar HTML
     */
    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generar URL
     */
    public function url($controller, $action = 'index', $params = [])
    {
        $baseUrl = $this->getBaseUrl();
        
        // Construir URL absoluta para evitar problemas de routing
        $url = $baseUrl;
        
        // Agregar controlador y acción
        if ($controller === 'home' && $action === 'index') {
            // Para la página principal, solo devolver la base URL
            return $url;
        } else {
            $url .= '/' . $controller;
            
            if ($action !== 'index') {
                $url .= '/' . $action;
            }
        }
        
        // Agregar parámetros si existen
        if (!empty($params)) {
            $url .= '/' . implode('/', $params);
        }
        
        return $url;
    }

    /**
     * Incluir CSS
     */
    public function css($file)
    {
        $baseUrl = $this->getBaseUrl();
        echo '<link rel="stylesheet" href="' . $baseUrl . '/public/css/' . $file . '.css">';
    }

    /**
     * Incluir JavaScript
     */
    public function js($file)
    {
        $baseUrl = $this->getBaseUrl();
        echo '<script src="' . $baseUrl . '/public/js/' . $file . '.js"></script>';
    }

    /**
     * Obtener URL base
     */
    private function getBaseUrl()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        
        // Obtener el directorio base del script
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = dirname($scriptName);
        
        // Si estamos en el directorio raíz, no agregar nada
        if ($basePath === '/' || $basePath === '\\') {
            $basePath = '';
        }
        
        return $protocol . '://' . $host . $basePath;
    }

    /**
     * Formatear fecha
     */
    public function formatDate($date, $format = 'Y-m-d H:i:s')
    {
        if ($date instanceof \DateTime) {
            return $date->format($format);
        }
        
        return date($format, strtotime($date));
    }

    /**
     * Truncar texto
     */
    public function truncate($text, $length = 100, $suffix = '...')
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . $suffix;
    }

    /**
     * Establecer datos globales para todas las vistas
     */
    public function setGlobalData($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Obtener datos de la vista
     */
    public function getData()
    {
        return $this->data;
    }
}