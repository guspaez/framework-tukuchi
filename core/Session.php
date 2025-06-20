<?php
/**
 * Framework Tukuchi - Session
 * Gestión de sesiones y estado de la aplicación
 */

namespace Tukuchi\Core;

class Session
{
    private $started = false;
    private $config = [];

    public function __construct($config = [])
    {
        $this->config = array_merge([
            'name' => 'TUKUCHI_SESSION',
            'lifetime' => 7200,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ], $config);
    }

    /**
     * Iniciar sesión
     */
    public function start()
    {
        if ($this->started) {
            return true;
        }

        // Configurar parámetros de sesión
        ini_set('session.name', $this->config['name']);
        ini_set('session.gc_maxlifetime', $this->config['lifetime']);
        ini_set('session.cookie_lifetime', $this->config['lifetime']);
        ini_set('session.cookie_path', $this->config['path']);
        ini_set('session.cookie_domain', $this->config['domain']);
        ini_set('session.cookie_secure', $this->config['secure']);
        ini_set('session.cookie_httponly', $this->config['httponly']);
        ini_set('session.cookie_samesite', $this->config['samesite']);
        
        // Configuraciones de seguridad
        ini_set('session.use_strict_mode', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.use_trans_sid', 0);

        // Iniciar sesión
        if (session_status() === PHP_SESSION_NONE) {
            $this->started = session_start();
        } else {
            $this->started = true;
        }

        // Regenerar ID de sesión periódicamente para seguridad
        if (!$this->has('_session_started')) {
            session_regenerate_id(true);
            $this->set('_session_started', time());
        } elseif (time() - $this->get('_session_started') > 1800) { // 30 minutos
            session_regenerate_id(true);
            $this->set('_session_started', time());
        }

        return $this->started;
    }

    /**
     * Establecer un valor en la sesión
     */
    public function set($key, $value)
    {
        $this->ensureStarted();
        $_SESSION[$key] = $value;
    }

    /**
     * Obtener un valor de la sesión
     */
    public function get($key, $default = null)
    {
        $this->ensureStarted();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    /**
     * Verificar si existe una clave en la sesión
     */
    public function has($key)
    {
        $this->ensureStarted();
        return isset($_SESSION[$key]);
    }

    /**
     * Eliminar un valor de la sesión
     */
    public function remove($key)
    {
        $this->ensureStarted();
        unset($_SESSION[$key]);
    }

    /**
     * Limpiar toda la sesión
     */
    public function clear()
    {
        $this->ensureStarted();
        $_SESSION = [];
    }

    /**
     * Destruir la sesión completamente
     */
    public function destroy()
    {
        $this->ensureStarted();
        
        // Limpiar variables de sesión
        $_SESSION = [];
        
        // Eliminar cookie de sesión
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        // Destruir sesión
        session_destroy();
        $this->started = false;
    }

    /**
     * Regenerar ID de sesión
     */
    public function regenerateId($deleteOld = true)
    {
        $this->ensureStarted();
        session_regenerate_id($deleteOld);
    }

    /**
     * Obtener ID de sesión
     */
    public function getId()
    {
        $this->ensureStarted();
        return session_id();
    }

    /**
     * Establecer ID de sesión
     */
    public function setId($id)
    {
        if ($this->started) {
            throw new \Exception('No se puede cambiar el ID de sesión después de iniciarla');
        }
        session_id($id);
    }

    /**
     * Obtener nombre de sesión
     */
    public function getName()
    {
        return session_name();
    }

    /**
     * Establecer nombre de sesión
     */
    public function setName($name)
    {
        if ($this->started) {
            throw new \Exception('No se puede cambiar el nombre de sesión después de iniciarla');
        }
        session_name($name);
    }

    /**
     * Verificar si la sesión está iniciada
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * Obtener todos los datos de sesión
     */
    public function all()
    {
        $this->ensureStarted();
        return $_SESSION;
    }

    /**
     * Establecer múltiples valores
     */
    public function setMultiple(array $data)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Flash messages - Mensajes que se muestran una sola vez
     */
    public function flash($key, $message = null)
    {
        if ($message === null) {
            // Obtener mensaje flash
            $message = $this->get('_flash_' . $key);
            $this->remove('_flash_' . $key);
            return $message;
        } else {
            // Establecer mensaje flash
            $this->set('_flash_' . $key, $message);
        }
    }

    /**
     * Verificar si existe un mensaje flash
     */
    public function hasFlash($key)
    {
        return $this->has('_flash_' . $key);
    }

    /**
     * Obtener IP del cliente para diferenciación de sesiones
     */
    public function getClientIp()
    {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Asegurar que la sesión esté iniciada
     */
    private function ensureStarted()
    {
        if (!$this->started) {
            $this->start();
        }
    }
}