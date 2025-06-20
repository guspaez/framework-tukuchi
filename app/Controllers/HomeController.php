<?php
/**
 * Framework Tukuchi - Home Controller
 * Controlador de ejemplo para la página principal
 */

namespace Tukuchi\App\Controllers;

use Tukuchi\Core\Controller;

class HomeController extends Controller
{
    /**
     * Acción principal (index)
     */
    public function indexAction($params = [])
    {
        $data = [
            'title' => 'Bienvenido a Framework Tukuchi',
            'message' => 'Potenciando la Transformación Digital',
            'version' => '1.0.0',
            'features' => [
                'Arquitectura MVC',
                'Inyección de Dependencias',
                'Routing Flexible',
                'Seguridad Integrada',
                'Fácil de Usar'
            ]
        ];

        $this->renderWithLayout('home/index', $data);
    }

    /**
     * Página de información
     */
    public function aboutAction($params = [])
    {
        $data = [
            'title' => 'Acerca de Framework Tukuchi',
            'description' => 'Framework PHP diseñado para agilizar el desarrollo de soluciones digitales para pequeños negocios.'
        ];

        $this->renderWithLayout('home/about', $data);
    }

    /**
     * API de ejemplo que devuelve JSON
     */
    public function apiAction($params = [])
    {
        $data = [
            'status' => 'success',
            'message' => 'API de Framework Tukuchi funcionando correctamente',
            'timestamp' => date('Y-m-d H:i:s'),
            'framework' => 'Tukuchi v1.0.0'
        ];

        $this->json($data);
    }

    /**
     * Ejemplo de formulario con CSRF
     */
    public function contactAction($params = [])
    {
        if ($this->isPost()) {
            try {
                // Validar token CSRF
                $this->validateCsrfToken();
                
                // Procesar datos del formulario
                $name = $this->getPost('name');
                $email = $this->getPost('email');
                $message = $this->getPost('message');
                
                // Aquí iría la lógica para procesar el contacto
                // Por ejemplo, guardar en base de datos o enviar email
                
                $response = [
                    'status' => 'success',
                    'message' => 'Mensaje enviado correctamente'
                ];
                
                if ($this->isAjax()) {
                    $this->json($response);
                } else {
                    // Redireccionar con mensaje de éxito
                    $this->redirect('home', 'contact', ['success' => 1]);
                }
                
            } catch (\Exception $e) {
                $response = [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
                
                if ($this->isAjax()) {
                    $this->json($response, 400);
                } else {
                    $data['error'] = $e->getMessage();
                }
            }
        }
        
        // Generar token CSRF para el formulario
        $data = [
            'title' => 'Contacto',
            'csrf_token' => $this->generateCsrfToken(),
            'success' => $this->getGet('success')
        ];
        
        $this->renderWithLayout('home/contact', $data);
    }
}