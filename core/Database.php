<?php
/**
 * Framework Tukuchi - Database
 * Gestión de conexiones y operaciones de base de datos
 */

namespace Tukuchi\Core;

use PDO;
use PDOException;

class Database
{
    private $connections = [];
    private $config;
    private $defaultConnection = 'default';

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Obtener conexión a la base de datos
     */
    public function getConnection($name = null)
    {
        $name = $name ?: $this->defaultConnection;

        if (!isset($this->connections[$name])) {
            $this->connections[$name] = $this->createConnection($name);
        }

        return $this->connections[$name];
    }

    /**
     * Crear nueva conexión
     */
    private function createConnection($name)
    {
        if (!isset($this->config[$name])) {
            throw new \Exception("Configuración de base de datos no encontrada: {$name}");
        }

        $config = $this->config[$name];
        
        try {
            $dsn = $this->buildDsn($config);
            $pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options'] ?? []
            );

            return $pdo;
        } catch (PDOException $e) {
            throw new \Exception("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }

    /**
     * Construir DSN
     */
    private function buildDsn($config)
    {
        $driver = $config['driver'];
        
        switch ($driver) {
            case 'mysql':
                return "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
            
            case 'pgsql':
                return "pgsql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
            
            case 'sqlite':
                return "sqlite:{$config['database']}";
            
            default:
                throw new \Exception("Driver de base de datos no soportado: {$driver}");
        }
    }

    /**
     * Ejecutar consulta SQL
     */
    public function query($sql, $params = [], $connection = null)
    {
        $pdo = $this->getConnection($connection);
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new \Exception("Error en consulta SQL: " . $e->getMessage() . " | SQL: {$sql}");
        }
    }

    /**
     * Obtener todos los registros
     */
    public function fetchAll($sql, $params = [], $connection = null)
    {
        $stmt = $this->query($sql, $params, $connection);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener un registro
     */
    public function fetchOne($sql, $params = [], $connection = null)
    {
        $stmt = $this->query($sql, $params, $connection);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener valor escalar
     */
    public function fetchColumn($sql, $params = [], $connection = null)
    {
        $stmt = $this->query($sql, $params, $connection);
        return $stmt->fetchColumn();
    }

    /**
     * Insertar registro
     */
    public function insert($table, $data, $connection = null)
    {
        $columns = array_keys($data);
        $placeholders = array_map(function($col) { return ':' . $col; }, $columns);
        
        $sql = "INSERT INTO {$table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $params = [];
        foreach ($data as $key => $value) {
            $params[':' . $key] = $value;
        }
        
        $this->query($sql, $params, $connection);
        return $this->getConnection($connection)->lastInsertId();
    }

    /**
     * Actualizar registros
     */
    public function update($table, $data, $where, $whereParams = [], $connection = null)
    {
        $setParts = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            $setParts[] = "{$key} = :set_{$key}";
            $params[':set_' . $key] = $value;
        }
        
        // Combinar parámetros de SET y WHERE
        $params = array_merge($params, $whereParams);
        
        $sql = "UPDATE {$table} SET " . implode(', ', $setParts) . " WHERE {$where}";
        
        $stmt = $this->query($sql, $params, $connection);
        return $stmt->rowCount();
    }

    /**
     * Eliminar registros
     */
    public function delete($table, $where, $params = [], $connection = null)
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->query($sql, $params, $connection);
        return $stmt->rowCount();
    }

    /**
     * Iniciar transacción
     */
    public function beginTransaction($connection = null)
    {
        return $this->getConnection($connection)->beginTransaction();
    }

    /**
     * Confirmar transacción
     */
    public function commit($connection = null)
    {
        return $this->getConnection($connection)->commit();
    }

    /**
     * Revertir transacción
     */
    public function rollback($connection = null)
    {
        return $this->getConnection($connection)->rollback();
    }

    /**
     * Verificar si existe una tabla
     */
    public function tableExists($table, $connection = null)
    {
        $pdo = $this->getConnection($connection);
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        
        switch ($driver) {
            case 'mysql':
                $sql = "SHOW TABLES LIKE :table";
                break;
            case 'pgsql':
                $sql = "SELECT tablename FROM pg_tables WHERE tablename = :table";
                break;
            case 'sqlite':
                $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name = :table";
                break;
            default:
                throw new \Exception("Verificación de tabla no soportada para driver: {$driver}");
        }
        
        $result = $this->fetchOne($sql, [':table' => $table], $connection);
        return !empty($result);
    }

    /**
     * Obtener esquema de tabla
     */
    public function getTableSchema($table, $connection = null)
    {
        $pdo = $this->getConnection($connection);
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        
        switch ($driver) {
            case 'mysql':
                $sql = "DESCRIBE {$table}";
                break;
            case 'pgsql':
                $sql = "SELECT column_name, data_type, is_nullable FROM information_schema.columns WHERE table_name = :table";
                return $this->fetchAll($sql, [':table' => $table], $connection);
            case 'sqlite':
                $sql = "PRAGMA table_info({$table})";
                break;
            default:
                throw new \Exception("Esquema de tabla no soportado para driver: {$driver}");
        }
        
        return $this->fetchAll($sql, [], $connection);
    }

    /**
     * Cerrar todas las conexiones
     */
    public function closeConnections()
    {
        $this->connections = [];
    }

    /**
     * Obtener información de la conexión
     */
    public function getConnectionInfo($connection = null)
    {
        $pdo = $this->getConnection($connection);
        
        return [
            'driver' => $pdo->getAttribute(PDO::ATTR_DRIVER_NAME),
            'version' => $pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
            'connection_status' => $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS)
        ];
    }
}