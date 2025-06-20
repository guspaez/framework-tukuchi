<?php
/**
 * Framework Tukuchi - Router
 * Gestión de rutas y URLs del framework
 */

namespace Tukuchi\Core;

class Router
{
    private $routes = [];
    private $defaultController = 'home';
    private $defaultAction = 'index';

    /**
     * Agregar una ruta
     */
    public function addRoute($pattern, $controller, $action = 'index', $params = [])
    {
        $this->routes[] = [
            'pattern' => $pattern,
            'controller' => $controller,
            'action' => $action,
            'params' => $params
        ];
    }

    /**
     * Procesar una URL y devolver información de ruta
     */
    public function parseUrl($url)
    {
        // Limpiar URL
        $url = trim($url, '/');
        $segments = $url ? explode('/', $url) : [];

        // Verificar rutas personalizadas primero
        foreach ($this->routes as $route) {
            if ($this->matchRoute($route['pattern'], $url)) {
                return [
                    'controller' => $route['controller'],
                    'action' => $route['action'],
                    'params' => array_merge($route['params'], $this->extractParams($route['pattern'], $url))
                ];
            }
        }

        // Ruta por defecto: /controlador/accion/param1/param2/...
        // O para rutas anidadas: /admin/auth/login
        
        if (empty($segments)) {
            // URL vacía, usar controlador por defecto
            return [
                'controller' => $this->defaultController,
                'action' => $this->defaultAction,
                'params' => []
            ];
        }
        
        // Determinar si es una ruta anidada (admin/auth) o simple (user)
        if (count($segments) >= 2) {
            // Para rutas admin, siempre usar la estructura anidada
            if ($segments[0] === 'admin') {
                $nestedController = $segments[0] . '/' . $segments[1];
                $nestedAction = isset($segments[2]) && !empty($segments[2]) ? $segments[2] : $this->defaultAction;
                $nestedParams = array_slice($segments, 3);
                
                return [
                    'controller' => $nestedController,
                    'action' => $nestedAction,
                    'params' => $nestedParams
                ];
            }
            
            // Para otras rutas, usar la estructura simple
            $controller = $segments[0];
            $action = $segments[1];
            $params = array_slice($segments, 2);
            
            return [
                'controller' => $controller,
                'action' => $action,
                'params' => $params
            ];
        } else {
            // Ruta simple con un solo segmento
            $controller = $segments[0];
            $action = $this->defaultAction;
            $params = [];
            
            return [
                'controller' => $controller,
                'action' => $action,
                'params' => $params
            ];
        }
    }

    /**
     * Verificar si una ruta coincide con un patrón
     */
    private function matchRoute($pattern, $url)
    {
        // Convertir patrón a expresión regular
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $url);
    }

    /**
     * Extraer parámetros de una URL basada en un patrón
     */
    private function extractParams($pattern, $url)
    {
        $params = [];
        
        // Encontrar nombres de parámetros en el patrón
        preg_match_all('/\{([^}]+)\}/', $pattern, $paramNames);
        
        // Convertir patrón a expresión regular y extraer valores
        $regexPattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        $regexPattern = '#^' . $regexPattern . '$#';
        
        if (preg_match($regexPattern, $url, $matches)) {
            array_shift($matches); // Remover la coincidencia completa
            
            foreach ($paramNames[1] as $index => $name) {
                if (isset($matches[$index])) {
                    $params[$name] = $matches[$index];
                }
            }
        }
        
        return $params;
    }

    /**
     * Generar URL basada en controlador y acción
     */
    public function generateUrl($controller, $action = 'index', $params = [])
    {
        // Si es la página principal (home/index), devolver cadena vacía
        if ($controller === $this->defaultController && $action === $this->defaultAction) {
            return '';
        }
        
        $url = $controller;
        
        if ($action !== 'index') {
            $url .= '/' . $action;
        }
        
        if (!empty($params)) {
            $url .= '/' . implode('/', $params);
        }
        
        return $url;
    }
}