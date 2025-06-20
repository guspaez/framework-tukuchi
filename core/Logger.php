<?php
/**
 * Framework Tukuchi - Logger
 * Sistema de logging para el framework
 */

namespace Tukuchi\Core;

class Logger
{
    const DEBUG = 'debug';
    const INFO = 'info';
    const WARNING = 'warning';
    const ERROR = 'error';
    const CRITICAL = 'critical';

    private $logPath;
    private $logLevel;
    private $dateFormat;
    private $maxFileSize;
    private $maxFiles;

    private $levels = [
        self::DEBUG => 0,
        self::INFO => 1,
        self::WARNING => 2,
        self::ERROR => 3,
        self::CRITICAL => 4
    ];

    public function __construct($config = [])
    {
        $this->logPath = $config['path'] ?? TUKUCHI_PATH . '/storage/logs';
        $this->logLevel = $config['level'] ?? self::INFO;
        $this->dateFormat = $config['date_format'] ?? 'Y-m-d H:i:s';
        $this->maxFileSize = $config['max_file_size'] ?? 10485760; // 10MB
        $this->maxFiles = $config['max_files'] ?? 5;

        // Crear directorio de logs si no existe
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }
    }

    /**
     * Log de debug
     */
    public function debug($message, array $context = [])
    {
        $this->log(self::DEBUG, $message, $context);
    }

    /**
     * Log de información
     */
    public function info($message, array $context = [])
    {
        $this->log(self::INFO, $message, $context);
    }

    /**
     * Log de advertencia
     */
    public function warning($message, array $context = [])
    {
        $this->log(self::WARNING, $message, $context);
    }

    /**
     * Log de error
     */
    public function error($message, array $context = [])
    {
        $this->log(self::ERROR, $message, $context);
    }

    /**
     * Log crítico
     */
    public function critical($message, array $context = [])
    {
        $this->log(self::CRITICAL, $message, $context);
    }

    /**
     * Log genérico
     */
    public function log($level, $message, array $context = [])
    {
        // Verificar si el nivel debe ser loggeado
        if (!$this->shouldLog($level)) {
            return;
        }

        $logEntry = $this->formatLogEntry($level, $message, $context);
        $this->writeLog($logEntry);
    }

    /**
     * Verificar si debe loggear el nivel
     */
    private function shouldLog($level)
    {
        return $this->levels[$level] >= $this->levels[$this->logLevel];
    }

    /**
     * Formatear entrada de log
     */
    private function formatLogEntry($level, $message, array $context = [])
    {
        $timestamp = date($this->dateFormat);
        $levelUpper = strtoupper($level);
        
        // Interpolar contexto en el mensaje
        $message = $this->interpolate($message, $context);
        
        // Obtener información adicional
        $extra = $this->getExtraInfo();
        
        $logEntry = "[{$timestamp}] {$levelUpper}: {$message}";
        
        if (!empty($context)) {
            $logEntry .= ' ' . json_encode($context, JSON_UNESCAPED_UNICODE);
        }
        
        if (!empty($extra)) {
            $logEntry .= ' ' . json_encode($extra, JSON_UNESCAPED_UNICODE);
        }
        
        return $logEntry . PHP_EOL;
    }

    /**
     * Interpolar contexto en mensaje
     */
    private function interpolate($message, array $context = [])
    {
        $replace = [];
        
        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }
        
        return strtr($message, $replace);
    }

    /**
     * Obtener información adicional
     */
    private function getExtraInfo()
    {
        $extra = [];
        
        // Información de la petición HTTP
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $extra['method'] = $_SERVER['REQUEST_METHOD'];
            $extra['uri'] = $_SERVER['REQUEST_URI'] ?? '';
            $extra['ip'] = $this->getClientIp();
            $extra['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        }
        
        // Información de memoria
        $extra['memory_usage'] = memory_get_usage(true);
        $extra['memory_peak'] = memory_get_peak_usage(true);
        
        return $extra;
    }

    /**
     * Obtener IP del cliente
     */
    private function getClientIp()
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
     * Escribir log al archivo
     */
    private function writeLog($logEntry)
    {
        $logFile = $this->getLogFile();
        
        // Rotar archivo si es necesario
        $this->rotateLogIfNeeded($logFile);
        
        // Escribir al archivo
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Obtener archivo de log actual
     */
    private function getLogFile()
    {
        $date = date('Y-m-d');
        return $this->logPath . "/tukuchi-{$date}.log";
    }

    /**
     * Rotar log si es necesario
     */
    private function rotateLogIfNeeded($logFile)
    {
        if (!file_exists($logFile)) {
            return;
        }
        
        if (filesize($logFile) >= $this->maxFileSize) {
            $this->rotateLog($logFile);
        }
    }

    /**
     * Rotar archivo de log
     */
    private function rotateLog($logFile)
    {
        $pathInfo = pathinfo($logFile);
        $baseName = $pathInfo['filename'];
        $extension = $pathInfo['extension'];
        $directory = $pathInfo['dirname'];
        
        // Mover archivos existentes
        for ($i = $this->maxFiles - 1; $i >= 1; $i--) {
            $oldFile = "{$directory}/{$baseName}.{$i}.{$extension}";
            $newFile = "{$directory}/{$baseName}." . ($i + 1) . ".{$extension}";
            
            if (file_exists($oldFile)) {
                if ($i + 1 > $this->maxFiles) {
                    unlink($oldFile); // Eliminar archivo más antiguo
                } else {
                    rename($oldFile, $newFile);
                }
            }
        }
        
        // Mover archivo actual
        $rotatedFile = "{$directory}/{$baseName}.1.{$extension}";
        rename($logFile, $rotatedFile);
    }

    /**
     * Limpiar logs antiguos
     */
    public function cleanOldLogs($days = 30)
    {
        $files = glob($this->logPath . '/*.log*');
        $cutoffTime = time() - ($days * 24 * 60 * 60);
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoffTime) {
                unlink($file);
            }
        }
    }

    /**
     * Obtener logs recientes
     */
    public function getRecentLogs($lines = 100)
    {
        $logFile = $this->getLogFile();
        
        if (!file_exists($logFile)) {
            return [];
        }
        
        $logs = [];
        $handle = fopen($logFile, 'r');
        
        if ($handle) {
            // Leer desde el final del archivo
            fseek($handle, -1, SEEK_END);
            $lineCount = 0;
            $pos = ftell($handle);
            $line = '';
            
            while ($pos >= 0 && $lineCount < $lines) {
                $char = fgetc($handle);
                
                if ($char === "\n" || $pos === 0) {
                    if ($line !== '') {
                        array_unshift($logs, trim($line));
                        $lineCount++;
                    }
                    $line = '';
                } else {
                    $line = $char . $line;
                }
                
                fseek($handle, --$pos);
            }
            
            fclose($handle);
        }
        
        return $logs;
    }

    /**
     * Log de excepción
     */
    public function logException(\Exception $exception, $level = self::ERROR)
    {
        $context = [
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ];
        
        $this->log($level, $exception->getMessage(), $context);
    }
}