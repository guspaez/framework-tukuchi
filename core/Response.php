<?php
/**
 * Framework Tukuchi - Response
 * Manejo de respuestas HTTP
 */

namespace Tukuchi\Core;

class Response
{
    private $headers = [];
    private $statusCode = 200;
    private $content = '';

    /**
     * Establecer código de estado HTTP
     */
    public function setStatusCode($code)
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Agregar header
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Establecer tipo de contenido
     */
    public function setContentType($type, $charset = 'UTF-8')
    {
        $this->setHeader('Content-Type', $type . '; charset=' . $charset);
        return $this;
    }

    /**
     * Establecer contenido
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Enviar respuesta JSON
     */
    public function json($data, $statusCode = 200)
    {
        $this->setStatusCode($statusCode);
        $this->setContentType('application/json');
        $this->setContent(json_encode($data, JSON_UNESCAPED_UNICODE));
        $this->send();
    }

    /**
     * Enviar respuesta HTML
     */
    public function html($content, $statusCode = 200)
    {
        $this->setStatusCode($statusCode);
        $this->setContentType('text/html');
        $this->setContent($content);
        $this->send();
    }

    /**
     * Enviar respuesta de texto plano
     */
    public function text($content, $statusCode = 200)
    {
        $this->setStatusCode($statusCode);
        $this->setContentType('text/plain');
        $this->setContent($content);
        $this->send();
    }

    /**
     * Redireccionar
     */
    public function redirect($url, $statusCode = 302)
    {
        $this->setStatusCode($statusCode);
        $this->setHeader('Location', $url);
        $this->send();
    }

    /**
     * Enviar archivo para descarga
     */
    public function download($filePath, $filename = null, $contentType = 'application/octet-stream')
    {
        if (!file_exists($filePath)) {
            throw new \Exception("Archivo no encontrado: {$filePath}");
        }

        $filename = $filename ?: basename($filePath);
        
        $this->setHeader('Content-Type', $contentType);
        $this->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $this->setHeader('Content-Length', filesize($filePath));
        $this->setHeader('Cache-Control', 'private');
        $this->setHeader('Pragma', 'private');
        
        $this->sendHeaders();
        readfile($filePath);
        exit;
    }

    /**
     * Establecer cookie
     */
    public function setCookie($name, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = true)
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
        return $this;
    }

    /**
     * Eliminar cookie
     */
    public function deleteCookie($name, $path = '/', $domain = '')
    {
        $this->setCookie($name, '', time() - 3600, $path, $domain);
        return $this;
    }

    /**
     * Enviar headers
     */
    private function sendHeaders()
    {
        if (!headers_sent()) {
            // Enviar código de estado
            http_response_code($this->statusCode);
            
            // Enviar headers personalizados
            foreach ($this->headers as $name => $value) {
                header($name . ': ' . $value);
            }
        }
    }

    /**
     * Enviar respuesta completa
     */
    public function send()
    {
        $this->sendHeaders();
        echo $this->content;
        exit;
    }

    /**
     * Obtener código de estado
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Obtener headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Obtener contenido
     */
    public function getContent()
    {
        return $this->content;
    }
}