    /**
     * Verificar si existe una tabla
     */
    public function tableExists($table, $connection = null)
    {
        $pdo = $this->getConnection($connection);
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        
        try {
            switch ($driver) {
                case 'mysql':
                    $sql = "SHOW TABLES LIKE ?";
                    $result = $this->fetchOne($sql, [$table], $connection);
                    return !empty($result);
                    
                case 'pgsql':
                    $sql = "SELECT tablename FROM pg_tables WHERE tablename = ?";
                    $result = $this->fetchOne($sql, [$table], $connection);
                    return !empty($result);
                    
                case 'sqlite':
                    $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name = ?";
                    $result = $this->fetchOne($sql, [$table], $connection);
                    return !empty($result);
                    
                default:
                    throw new \Exception("Verificación de tabla no soportada para driver: {$driver}");
            }
        } catch (\Exception $e) {
            // Si hay error, asumir que la tabla no existe
            return false;
        }
    }