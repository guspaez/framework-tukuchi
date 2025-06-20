<?php
/**
 * Framework Tukuchi - Admin Auth Controller
 * Controlador de autenticación para administradores
 */

namespace Tukuchi\App\Controllers\Admin;

use Tukuchi\Core\Controller;
use Tukuchi\Core\Validator;
use Tukuchi\App\Models\User;

class AuthController extends AdminController
{
    /**
     * No requiere autenticación previa
     */
    public function init()
    {
        // No llamar al parent::init() para evitar cualquier verificación de autenticación
        $this->adminUser = null; // Asegurarse de que no haya usuario admin hasta que se autentique
    }

    /**
     * Mostrar formulario de login
     */
    public function loginAction($params = [])
    {
        $session = $this->getService('session');
        
        // Si ya está autenticado, redirigir al dashboard
        if ($session->get('admin_user_id')) {
            $this->redirect('admin/dashboard');
            return;
        }

        $data = [
            'title' => 'Iniciar Sesión - Administración',
            'csrf_token' => $this->generateCsrfToken(),
            'error' => $session->flash('login_error'),
            'success' => $session->flash('login_success')
        ];

        // Renderizar directamente sin usar métodos de AdminController que puedan interferir
        $this->render('admin/auth/login', $data);
    }

    /**
     * Procesar login
     */
    public function authenticateAction($params = [])
    {
        if (!$this->isPost()) {
            $this->redirect('admin/auth', 'login');
            return;
        }

        try {
            // Validar token CSRF
            $this->validateCsrfToken();

            // Obtener datos del formulario
            $email = $this->getPost('email');
            $password = $this->getPost('password');
            $remember = $this->getPost('remember', false);

            // Validar datos
            $validator = Validator::make([
                'email' => $email,
                'password' => $password
            ], [
                'email' => 'required|email',
                'password' => 'required|min:6'
            ], [
                'email.required' => 'El email es requerido',
                'email.email' => 'Ingresa un email válido',
                'password.required' => 'La contraseña es requerida',
                'password.min' => 'La contraseña debe tener al menos 6 caracteres'
            ]);

            if ($validator->fails()) {
                $session = $this->getService('session');
                $session->flash('login_error', 'Por favor corrige los errores en el formulario');
                $this->redirect('admin/auth', 'login');
                return;
            }

            // Buscar usuario por email
            $user = User::findByEmail($email);

            if (!$user) {
                $this->handleLoginError('Credenciales incorrectas');
                return;
            }

            // Verificar contraseña
            if (!$user->verifyPassword($password)) {
                $this->handleLoginError('Credenciales incorrectas');
                return;
            }

            // Verificar que sea administrador
            if (!$user->isAdmin()) {
                $this->handleLoginError('No tienes permisos de administrador');
                return;
            }

            // Verificar que esté activo
            if (!$user->isActive()) {
                $this->handleLoginError('Tu cuenta está desactivada');
                return;
            }

            // Login exitoso
            $session = $this->getService('session');
            $userData = $user->toArray();
            $userId = null;
            // Intentar obtener el ID de los datos del usuario
            if (isset($userData['id'])) {
                $userId = $userData['id'];
            } elseif (property_exists($user, 'id')) {
                $userId = $user->id;
            }
            if ($userId === null) {
                $logger = $this->getService('logger');
                $logger->error('User ID not found in user data, attempting to fetch from database', [
                    'email' => $user->email,
                    'user_data' => json_encode($userData)
                ]);
                // Intentar obtener el ID directamente de la base de datos si es posible
                $dbUserResult = User::where('email', $user->email);
                if (is_array($dbUserResult) && !empty($dbUserResult)) {
                    $dbUser = $dbUserResult[0];
                    if (is_array($dbUser) && isset($dbUser['id'])) {
                        $userId = $dbUser['id'];
                    } elseif (is_object($dbUser) && property_exists($dbUser, 'id')) {
                        $userId = $dbUser->id;
                    }
                } elseif (is_object($dbUserResult) && method_exists($dbUserResult, 'toArray')) {
                    $dbUser = $dbUserResult->toArray();
                    if (is_array($dbUser) && isset($dbUser['id'])) {
                        $userId = $dbUser['id'];
                    }
                } elseif (is_object($dbUserResult) && property_exists($dbUserResult, 'id')) {
                    $userId = $dbUserResult->id;
                }
                if (isset($userId)) {
                    $logger->info('User ID fetched from database', [
                        'email' => $user->email,
                        'user_id' => $userId
                    ]);
                } else {
                    $logger->error('Failed to fetch User ID from database', [
                        'email' => $user->email
                    ]);
                    // Último recurso: usar un ID temporal
                    $userId = 'TEMP_ADMIN_' . uniqid();
                }
            }
            $session->set('admin_user_id', $userId);
            $session->set('admin_login_time', time());
            $session->set('admin_login_token', bin2hex(random_bytes(16))); // Token de seguridad para evitar ataques de fijación de sesión
            $session->flash('debug_message', 'Sesión iniciada correctamente con ID: ' . $userId);

            // Configurar cookie de recordar si está marcado
            if ($remember) {
                $this->setRememberCookie($user, $userId);
            }

            // Log de actividad
            $logger = $this->getService('logger');
            $logger->info('Admin login successful', [
                'user_id' => $userId,
                'user_id_debug' => isset($user->id) ? 'ID is set: ' . $user->id : 'ID is not set',
                'user_id_from_array' => isset($userData['id']) ? 'ID from array: ' . $userData['id'] : 'ID not in array',
                'user_object' => get_class($user),
                'user_data' => json_encode($user->toArray()),
                'email' => $user->email,
                'ip' => $this->request->getClientIp(),
                'user_agent' => $this->request->getUserAgent()
            ]);

            // Redirigir al dashboard o URL solicitada
            $redirectUrl = $session->get('admin_redirect_after_login');
            $session->remove('admin_redirect_after_login');
            
            // Usar el método redirect() del controlador para intentar la redirección
            $logger->info('Attempting redirect to dashboard using controller method', [
                'user_id' => $userId
            ]);
            $this->redirect('admin/dashboard');
            
            // Como respaldo, forzar la redirección con cabecera HTTP explícita
            $redirectUrl = $this->url('admin/dashboard');
            $logger->info('Forcing redirect with HTTP header', [
                'url' => $redirectUrl,
                'user_id' => $userId
            ]);
            header('Location: ' . $redirectUrl);
            exit;
            
            // Último recurso: script de redirección en caso de que todo lo demás falle
            echo '<script>window.location.href = "' . $redirectUrl . '";</script>';
            exit;
            return;
        } catch (\Exception $e) {
            $logger = $this->getService('logger');
            $logger->error('Admin login error', [
                'error' => $e->getMessage(),
                'email' => $email ?? '',
                'ip' => $this->request->getClientIp()
            ]);

            $this->handleLoginError('Error interno del sistema');
        }
    }

    /**
     * Obtener estadísticas del sistema (copiado de DashboardController para uso directo)
     */
    private function getSystemStats()
    {
        // Devolver valores predeterminados para evitar cualquier consumo de memoria
        return [
            'users' => [
                'total' => 0,
                'active' => 0,
                'inactive' => 0,
                'admins' => 0
            ],
            'database' => [
                'size' => 'N/A',
                'tables' => 0
            ],
            'logs' => ['total' => 0, 'errors' => 0, 'files' => 0],
            'system' => [
                'php_version' => PHP_VERSION,
                'memory_usage' => 'N/A',
                'memory_peak' => 'N/A',
                'uptime' => 'N/A'
            ]
        ];
    }

    /**
     * Obtener actividad reciente (copiado de DashboardController para uso directo)
     */
    private function getRecentActivity()
    {
        // Omitir la obtención de logs recientes para evitar problemas de memoria
        return [];
    }

    /**
     * Obtener información del sistema (copiado de DashboardController para uso directo)
     */
    private function getSystemInfo()
    {
        // Devolver valores predeterminados para evitar consumo de memoria
        return [
            'framework_version' => '1.0.0',
            'php_version' => 'N/A',
            'server_software' => 'N/A',
            'document_root' => 'N/A',
            'server_name' => 'N/A',
            'server_port' => 'N/A',
            'loaded_extensions' => 0,
            'max_execution_time' => 'N/A',
            'memory_limit' => 'N/A',
            'upload_max_filesize' => 'N/A',
            'post_max_size' => 'N/A'
        ];
    }

    /**
     * Obtener tamaño de la base de datos (copiado de DashboardController para uso directo)
     */
    private function getDatabaseSize()
    {
        try {
            $database = $this->getService('database');
            $config = $this->getService('config');
            $dbName = $config['database']['default']['database'];
            
            $result = $database->fetchOne(
                "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb 
                 FROM information_schema.tables 
                 WHERE table_schema = ?",
                [$dbName]
            );
            
            return $result['size_mb'] ? $result['size_mb'] . ' MB' : '0 MB';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Obtener número de tablas (copiado de DashboardController para uso directo)
     */
    private function getTableCount()
    {
        try {
            $database = $this->getService('database');
            $config = $this->getService('config');
            $dbName = $config['database']['default']['database'];
            
            $result = $database->fetchOne(
                "SELECT COUNT(*) as table_count 
                 FROM information_schema.tables 
                 WHERE table_schema = ?",
                [$dbName]
            );
            
            return $result['table_count'] ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Obtener estadísticas de logs (copiado de DashboardController para uso directo)
     */
    private function getLogStats()
    {
        try {
            $logsPath = TUKUCHI_PATH . '/storage/logs';
            $logFiles = glob($logsPath . '/*.log*');
            
            $totalLogs = 0;
            $errorLogs = 0;
            $maxFileSize = 1024 * 1024; // 1MB máximo por archivo para evitar agotar memoria
            
            foreach ($logFiles as $file) {
                if (is_file($file) && filesize($file) <= $maxFileSize) {
                    $content = file_get_contents($file);
                    $totalLogs += substr_count($content, '] INFO:') + 
                                  substr_count($content, '] ERROR:') + 
                                  substr_count($content, '] WARNING:') + 
                                  substr_count($content, '] DEBUG:') + 
                                  substr_count($content, '] CRITICAL:');
                    $errorLogs += substr_count($content, '] ERROR:') + 
                                  substr_count($content, '] CRITICAL:');
                }
            }
            
            return [
                'total' => $totalLogs,
                'errors' => $errorLogs,
                'files' => count($logFiles)
            ];
        } catch (\Exception $e) {
            $logger = $this->getService('logger');
            $logger->error('Error getting log stats due to memory or other issues', [
                'error' => $e->getMessage()
            ]);
            return ['total' => 0, 'errors' => 0, 'files' => 0];
        }
    }

    /**
     * Obtener icono para nivel de log (copiado de DashboardController para uso directo)
     */
    private function getLogIcon($level)
    {
        switch (strtoupper($level)) {
            case 'ERROR':
            case 'CRITICAL':
                return 'bi-exclamation-triangle text-danger';
            case 'WARNING':
                return 'bi-exclamation-circle text-warning';
            case 'INFO':
                return 'bi-info-circle text-info';
            case 'DEBUG':
                return 'bi-bug text-secondary';
            default:
                return 'bi-circle text-muted';
        }
    }

    /**
     * Formatear bytes (copiado de DashboardController para uso directo)
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Obtener uptime del sistema (copiado de DashboardController para uso directo)
     */
    private function getSystemUptime()
    {
        // En un sistema real, esto obtendría el uptime del servidor
        $session = $this->getService('session');
        $loginTime = $session->get('admin_login_time', time());
        $uptime = time() - $loginTime;
        
        $hours = floor($uptime / 3600);
        $minutes = floor(($uptime % 3600) / 60);
        
        return "{$hours}h {$minutes}m";
    }

    /**
     * Obtener menú de administración (compatible con AdminController)
     */
    protected function getAdminMenu()
    {
        return parent::getAdminMenu();
    }

    /**
     * Verificar si es la ruta actual (compatible con AdminController)
     */
    protected function isCurrentRoute($route)
    {
        return parent::isCurrentRoute($route);
    }

    /**
     * Generar URL para admin (compatible con AdminController)
     */
    protected function url($path, $params = [])
    {
        return parent::url($path, $params);
    }

    /**
     * Renderizar vista de admin con layout (compatible con AdminController)
     */
    protected function renderWithLayout($template, $data = [], $layout = 'admin')
    {
        return parent::renderAdmin($template, $data);
    }

    /**
     * Cerrar sesión
     */
    public function logoutAction($params = [])
    {
        $session = $this->getService('session');
        $adminUserId = $session->get('admin_user_id');

        if ($adminUserId) {
            // Log de actividad
            $logger = $this->getService('logger');
            $logger->info('Admin logout', [
                'user_id' => $adminUserId,
                'ip' => $this->request->getClientIp()
            ]);
        }

        // Limpiar sesión
        $session->remove('admin_user_id');
        $session->remove('admin_login_time');

        // Eliminar cookie de recordar
        $this->response->deleteCookie('admin_remember_token');

        // Mensaje de éxito
        $session->flash('login_success', 'Sesión cerrada correctamente');

        $this->redirect('admin/auth', 'login');
    }

    /**
     * Manejar error de login
     */
    private function handleLoginError($message)
    {
        $session = $this->getService('session');
        $session->flash('login_error', $message);

        // Log del intento fallido
        $logger = $this->getService('logger');
        $logger->warning('Admin login failed', [
            'message' => $message,
            'email' => $this->getPost('email', ''),
            'ip' => $this->request->getClientIp(),
            'user_agent' => $this->request->getUserAgent()
        ]);

        $this->redirect('admin/auth', 'login');
    }

    /**
     * Configurar cookie de recordar
     */
    private function setRememberCookie($user, $userId)
    {
        $token = bin2hex(random_bytes(32));
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        $expiry = time() + (30 * 24 * 60 * 60); // 30 días

        // Guardar token en base de datos (necesitarías agregar esta columna)
        // $user->remember_token = $hashedToken;
        // $user->save();

        // Configurar cookie segura con HttpOnly, Secure y SameSite
        $this->response->setCookie('admin_remember_token', $token, $expiry, '/', '', true, true, 'Strict');
        
        $logger = $this->getService('logger');
        $logger->info('Remember me cookie set', [
            'user_id' => $userId,
            'ip' => $this->request->getClientIp()
        ]);
    }

    /**
     * Verificar cookie de recordar
     */
    public function checkRememberAction($params = [])
    {
        $token = $this->request->getCookie('admin_remember_token');
        
        if (!$token) {
            $logger = $this->getService('logger');
            $logger->info('No remember me cookie found', [
                'ip' => $this->request->getClientIp()
            ]);
            $this->redirect('admin/auth', 'login');
            return;
        }

        // Aquí verificarías el token contra la base de datos
        // Por ahora, redirigimos al login hasta que se implemente la lógica
        $logger = $this->getService('logger');
        $logger->info('Remember me cookie found but not implemented', [
            'ip' => $this->request->getClientIp()
        ]);
        $this->redirect('admin/auth', 'login');
    }
}
