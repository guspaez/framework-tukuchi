<?php
/**
 * Framework Tukuchi - Admin Logs Controller
 * Controlador para la gestión de logs del sistema en el panel de administración
 */

namespace Tukuchi\App\Controllers\Admin;

use Tukuchi\Core\Controller;

class LogsController extends AdminController
{
    /**
     * Mostrar lista de logs del sistema
     */
    public function indexAction($params = [])
    {
        $data = [
            'title' => 'Logs del Sistema - Administración',
            'logs' => $this->getLogFiles(),
            'selected_log_content' => '',
            'selected_log_name' => ''
        ];

        // Si se selecciona un archivo de log específico, mostrar su contenido
        $logFile = $this->getGet('file', '');
        if ($logFile && $this->isValidLogFile($logFile)) {
            $data['selected_log_content'] = $this->getLogContent($logFile);
            $data['selected_log_name'] = $logFile;
        }

        $this->renderAdmin('admin/logs/index', $data);
    }

    /**
     * Obtener lista de archivos de log
     */
    private function getLogFiles()
    {
        $logsPath = TUKUCHI_PATH . '/storage/logs';
        $logFiles = glob($logsPath . '/*.log*');
        $files = [];
        
        foreach ($logFiles as $file) {
            if (is_file($file)) {
                $fileName = basename($file);
                $files[] = [
                    'name' => $fileName,
                    'path' => $file,
                    'size' => $this->formatBytes(filesize($file)),
                    'modified' => date('d/m/Y H:i:s', filemtime($file))
                ];
            }
        }
        
        // Ordenar por fecha de modificación, más reciente primero
        usort($files, function($a, $b) {
            return strtotime($b['modified']) - strtotime($a['modified']);
        });
        
        return $files;
    }

    /**
     * Validar si el archivo de log es seguro para leer
     */
    private function isValidLogFile($fileName)
    {
        $logsPath = TUKUCHI_PATH . '/storage/logs';
        $fullPath = realpath($logsPath . '/' . $fileName);
        
        // Asegurarse de que el archivo está dentro del directorio de logs
        return $fullPath && strpos($fullPath, realpath($logsPath)) === 0 && is_file($fullPath);
    }

    /**
     * Obtener contenido del archivo de log
     */
    private function getLogContent($fileName)
    {
        $logsPath = TUKUCHI_PATH . '/storage/logs';
        $fullPath = $logsPath . '/' . $fileName;
        
        if (!$this->isValidLogFile($fileName)) {
            return "Error: Archivo no válido o acceso denegado.";
        }
        
        $maxSize = 1024 * 1024; // 1MB máximo para evitar problemas de memoria
        
        if (filesize($fullPath) > $maxSize) {
            return "Advertencia: El archivo es demasiado grande para mostrarlo completo. Mostrando solo las últimas líneas.\n\n" . 
                   $this->getLastLines($fullPath, 100);
        }
        
        $content = file_get_contents($fullPath);
        if ($content === false) {
            return "Error: No se pudo leer el archivo de log.";
        }
        
        return $content;
    }

    /**
     * Obtener las últimas líneas de un archivo grande
     */
    private function getLastLines($filePath, $lines = 100)
    {
        $content = "";
        $fp = fopen($filePath, "r");
        if ($fp === false) {
            return "Error: No se pudo abrir el archivo.";
        }
        
        $pos = -2;
        $t = " ";
        $lineCount = 0;
        
        while ($lineCount < $lines) {
            $t = " ";
            while ($t != "\n") {
                if (fseek($fp, $pos, SEEK_END) == -1) {
                    rewind($fp);
                    $content = fread($fp, ftell($fp));
                    fclose($fp);
                    return $content;
                }
                $t = fgetc($fp);
                $pos--;
            }
            $lineCount++;
            $content = fgets($fp) . $content;
        }
        
        fclose($fp);
        return $content;
    }

    /**
     * Formatear bytes a tamaño legible
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
     * Descargar archivo de log
     */
    public function downloadAction($params = [])
    {
        $fileName = $this->getGet('file', '');
        if (!$fileName || !$this->isValidLogFile($fileName)) {
            $this->renderAdmin('admin/errors/404', [
                'title' => 'Archivo no encontrado',
                'message' => 'El archivo de log solicitado no existe o no tienes permisos para acceder a él.'
            ]);
            return;
        }
        
        $logsPath = TUKUCHI_PATH . '/storage/logs';
        $fullPath = $logsPath . '/' . $fileName;
        
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($fullPath));
        
        readfile($fullPath);
        exit;
    }

    /**
     * Eliminar archivo de log
     */
    public function deleteAction($params = [])
    {
        if (!$this->isPost()) {
            $this->redirect('admin/logs');
            return;
        }
        
        $fileName = $this->getPost('file', '');
        if (!$fileName || !$this->isValidLogFile($fileName)) {
            $session = $this->getService('session');
            $session->flash('error', 'Archivo no válido o no tienes permisos para eliminarlo.');
            $this->redirect('admin/logs');
            return;
        }
        
        $logsPath = TUKUCHI_PATH . '/storage/logs';
        $fullPath = $logsPath . '/' . $fileName;
        
        if (unlink($fullPath)) {
            $logger = $this->getService('logger');
            $logger->info('Log file deleted by admin', [
                'file' => $fileName,
                'user_id' => $this->adminUser->id,
                'ip' => $this->request->getClientIp()
            ]);
            
            $session = $this->getService('session');
            $session->flash('success', 'Archivo de log eliminado correctamente.');
        } else {
            $session = $this->getService('session');
            $session->flash('error', 'Error al eliminar el archivo de log.');
        }
        
        $this->redirect('admin/logs');
    }
}
