<?php
/**
 * Framework Tukuchi - Request
 * Manejo de peticiones HTTP
 */

namespace Tukuchi\Core;

class Request
{
    private $get;
    private $post;
    private $server;
    private $files;
    private $cookies;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
        $this->files = $_FILES;
        $this->cookies = $_COOKIE;
    }

    /**
     * Obtener datos GET
     */
    public function getGet($key = null, $default = null)
    {
        if ($key === null) {
            return $this->get;
        }
        
        return isset($this->get[$key]) ? $this->sanitize($this->get[$key]) : $default;
    }

    /**
     * Obtener datos POST
     */
    public function getPost($key = null, $default = null)
    {
        if ($key === null) {
            return $this->post;
        }
        
        return isset($this->post[$key]) ? $this->sanitize($this->post[$key]) : $default;
    }

    /**
     * Obtener archivos subidos
     */
    public function getFiles($key = null)
    {
        if ($key === null) {
            return $this->files;
        }
        
        return isset($this->files[$key]) ? $this->files[$key] : null;
    }

    /**
     * Obtener cookies
     */
    public function getCookie($key = null, $default = null)
    {
        if ($key === null) {
            return $this->cookies;
        }
        
        return isset($this->cookies[$key]) ? $this->cookies[$key] : $default;
    }

    /**
     * Obtener datos del servidor
     */
    public function getServer($key = null, $default = null)
    {
        if ($key === null) {
            return $this->server;
        }
        
        return isset($this->server[$key]) ? $this->server[$key] : $default;
    }

    /**
     * Obtener método HTTP
     */
    public function getMethod()
    {
        return $this->getServer('REQUEST_METHOD', 'GET');
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

    /**
     * Verificar si es AJAX
     */
    public function isAjax()
    {
        return strtolower($this->getServer('HTTP_X_REQUESTED_WITH', '')) === 'xmlhttprequest';
    }

    /**
     * Obtener IP del cliente
     */
    public function getClientIp()
    {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($this->server[$key])) {
                $ip = $this->server[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $this->getServer('REMOTE_ADDR', '0.0.0.0');
    }

    /**
     * Obtener User Agent
     */
    public function getUserAgent()
    {
        return $this->getServer('HTTP_USER_AGENT', '');
    }

    /**
     * Obtener URL completa
     */
    public function getUrl()
    {
        $protocol = $this->isSecure() ? 'https' : 'http';
        $host = $this->getServer('HTTP_HOST');
        $uri = $this->getServer('REQUEST_URI');
        
        return $protocol . '://' . $host . $uri;
    }

    /**
     * Verificar si la conexión es segura (HTTPS)
     */
    public function isSecure()
    {
        return (!empty($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off') ||
               $this->getServer('SERVER_PORT') == 443;
    }

    /**
     * Sanitizar datos de entrada
     */
    private function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        
        // Remover espacios en blanco
        $data = trim($data);
        
        // Convertir caracteres especiales a entidades HTML
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        
        return $data;
    }

    /**
     * Validar email
     */
    public function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validar URL
     */
    public function validateUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validar entero
     */
    public function validateInt($value, $min = null, $max = null)
    {
        $options = [];
        if ($min !== null) $options['min_range'] = $min;
        if ($max !== null) $options['max_range'] = $max;
        
        return filter_var($value, FILTER_VALIDATE_INT, ['options' => $options]) !== false;
    }
}