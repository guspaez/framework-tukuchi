<?php
/**
 * Framework Tukuchi - Admin Dashboard Controller
 * Controlador para el dashboard de administración
 */

namespace Tukuchi\App\Controllers\Admin;

use Tukuchi\Core\Controller;

class DashboardController extends AdminController
{
    /**
     * Mostrar dashboard de administración
     */
    public function indexAction($params = [])
    {
        // Obtener estadísticas del sistema
        $stats = $this->getSystemStats();
        
        // Obtener actividad reciente (logs)
        $recentActivity = $this->getRecentActivity();
        
        // Obtener información del sistema
        $systemInfo = $this->getSystemInfo();
        
        $data = [
            'title' => 'Dashboard - Administración',
            'stats' => $stats,
            'recent_activity' => $recentActivity,
            'system_info' => $systemInfo
        ];

        $this->renderAdmin('/dashboard/index', $data);
    }
    
    /**
     * Acción para probar el layout de administración
     */
    public function testLayoutAction($params = [])
    {
        $data = [
            'title' => 'Prueba de Layout - Administración'
        ];

        $this->renderAdmin('admin/test_layout', $data);
    }

    /**
     * Obtener estadísticas del sistema
     */
    protected function getSystemStats()
    {
        // Simular estadísticas (en una implementación real, obtendría datos de la base de datos)
        return [
            'users' => [
                'total' => 125,
                'active' => 98
            ],
            'database' => [
                'size' => '12.5 MB',
                'tables' => 8
            ],
            'logs' => [
                'total' => 543,
                'errors' => 12
            ],
            'system' => [
                'memory_usage' => $this->formatBytes(memory_get_usage()),
                'memory_peak' => $this->formatBytes(memory_get_peak_usage())
            ]
        ];
    }

    /**
     * Obtener actividad reciente
     */
    protected function getRecentActivity()
    {
        // Simular actividad reciente (en una implementación real, obtendría datos de logs)
        return [
            [
                'timestamp' => date('Y-m-d H:i:s', strtotime('-10 minutes')),
                'message' => 'Usuario "admin" ha iniciado sesión',
                'icon' => 'bi-person-check text-success',
                'details' => json_encode([
                    'ip' => '192.168.1.100',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ])
            ],
            [
                'timestamp' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'message' => 'Nuevo usuario registrado: "johndoe"',
                'icon' => 'bi-person-plus text-info',
                'details' => json_encode([
                    'ip' => '192.168.1.101',
                    'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36'
                ])
            ],
            [
                'timestamp' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'message' => 'Error al procesar solicitud: "Invalid input data"',
                'icon' => 'bi-exclamation-triangle text-danger',
                'details' => json_encode([
                    'ip' => '192.168.1.102',
                    'user_agent' => 'Mozilla/5.0 (Linux; Android 10; SM-G973F) AppleWebKit/537.36'
                ])
            ],
            [
                'timestamp' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'message' => 'Actualización de sistema completada',
                'icon' => 'bi-arrow-clockwise text-warning',
                'details' => json_encode([
                    'ip' => '127.0.0.1',
                    'user_agent' => 'Server Cron Job'
                ])
            ]
        ];
    }

    /**
     * Obtener información del sistema
     */
    protected function getSystemInfo()
    {
        return [
            'framework_version' => '1.0.0',
            'php_version' => phpversion(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido',
            'server_port' => $_SERVER['SERVER_PORT'] ?? '80',
            'loaded_extensions' => count(get_loaded_extensions()),
            'memory_limit' => ini_get('memory_limit'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'max_execution_time' => ini_get('max_execution_time')
        ];
    }

    /**
     * Formatear bytes a formato legible
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
