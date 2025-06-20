<?php
/**
 * Framework Tukuchi - Controller Base
 * Clase padre para todos los controladores
 */

namespace Tukuchi\Core;

class Controller extends CoreObject
{
    protected $serviceLocator;
    protected $view;
    protected $request;
    protected $response;

    public function __construct(ServiceLocator $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $this->view = new View();
        $this->request = new Request();
        $this->response = new Response();
        
        // Llamar método de inicialización si existe
        if (method_exists($this, 'init')) {
            $this->init();
        }
    }

    /**
     * Método de inicialización (puede ser sobrescrito por controladores hijos)
     */
    protected function init()
    {
        // Método base vacío que puede ser sobrescrito
    }

    /**
     * Renderizar una vista
     */
    protected function render($template, $data = [])
    {
        $this->view->render($template, $data);
    }

    /**
     * Renderizar vista con layout
     */
    protected function renderWithLayout($template, $data = [], $layout = 'main')
    {
        $this->view->renderWithLayout($template, $data, $layout);
    }

    /**
     * Devolver respuesta JSON
     */
    protected function json($data, $statusCode = 200)
    {
        $this->response->json($data, $statusCode);
    }

    /**
     * Redireccionar
     */
    protected function redirect($controller, $action = 'index', $params = [])
    {
        $url = $this->serviceLocator->get('url');
        $url->redirect($controller, $action, $params);
    }

    /**
     * Obtener servicio del Service Locator
     */
    protected function getService($name)
    {
        return $this->serviceLocator->get($name);
    }

    /**
     * Verificar si la petición es POST
     */
    protected function isPost()
    {
        return $this->request->isPost();
    }

    /**
     * Verificar si la petición es AJAX
     */
    protected function isAjax()
    {
        return $this->request->isAjax();
    }

    /**
     * Obtener datos POST
     */
    protected function getPost($key = null, $default = null)
    {
        return $this->request->getPost($key, $default);
    }

    /**
     * Obtener datos GET
     */
    protected function getGet($key = null, $default = null)
    {
        return $this->request->getGet($key, $default);
    }

    /**
     * Validar token CSRF
     */
    protected function validateCsrfToken()
    {
        $config = $this->serviceLocator->get('config');
        if ($config['security']['csrf_protection']) {
            $session = $this->serviceLocator->get('session');
            $tokenName = $config['security']['csrf_token_name'];
            
            $sessionToken = $session->get('csrf_token');
            $requestToken = $this->getPost($tokenName);
            
            if (!$sessionToken || !$requestToken || !hash_equals($sessionToken, $requestToken)) {
                throw new \Exception('Token CSRF inválido');
            }
        }
    }

    /**
     * Generar token CSRF
     */
    protected function generateCsrfToken()
    {
        $session = $this->serviceLocator->get('session');
        $token = bin2hex(random_bytes(32));
        $session->set('csrf_token', $token);
        return $token;
    }

    /**
     * Método call para redirección de ejecución
     */
    public function call($action, $params = [])
    {
        $actionMethod = $action . 'Action';
        
        if (method_exists($this, $actionMethod)) {
            return $this->$actionMethod($params);
        }
        
        throw new \Exception("Acción no encontrada: {$action}");
    }
}