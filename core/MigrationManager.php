<?php
/**
 * Framework Tukuchi - Migration Manager
 * Gestor para ejecutar y controlar migraciones
 */

namespace Tukuchi\Core;

class MigrationManager
{
    private $database;
    private $migrationsPath;
    private $migrationsTable = 'migrations';

    public function __construct(Database $database, $migrationsPath = null)
    {
        $this->database = $database;
        $this->migrationsPath = $migrationsPath ?: TUKUCHI_PATH . '/database/migrations';
        
        $this->ensureMigrationsTable();
    }

    /**
     * Asegurar que existe la tabla de migraciones
     */
    private function ensureMigrationsTable()
    {
        try {
            // Intentar crear la tabla (si ya existe, no pasará nada)
            $sql = "CREATE TABLE IF NOT EXISTS {$this->migrationsTable} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INT NOT NULL,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $this->database->query($sql);
        } catch (\Exception $e) {
            // Si hay error, la tabla probablemente ya existe
        }
    }

    /**
     * Ejecutar migraciones pendientes
     */
    public function migrate()
    {
        $pendingMigrations = $this->getPendingMigrations();
        
        if (empty($pendingMigrations)) {
            echo "No hay migraciones pendientes.\n";
            return;
        }

        $batch = $this->getNextBatchNumber();
        
        foreach ($pendingMigrations as $migrationFile) {
            $this->executeMigration($migrationFile, $batch);
        }
        
        echo "Ejecutadas " . count($pendingMigrations) . " migraciones.\n";
    }

    /**
     * Obtener migraciones pendientes
     */
    private function getPendingMigrations()
    {
        $allMigrations = $this->getAllMigrationFiles();
        $executedMigrations = $this->getExecutedMigrations();
        
        return array_diff($allMigrations, $executedMigrations);
    }

    /**
     * Obtener todos los archivos de migración
     */
    private function getAllMigrationFiles()
    {
        if (!is_dir($this->migrationsPath)) {
            return [];
        }
        
        $files = scandir($this->migrationsPath);
        $migrations = [];
        
        foreach ($files as $file) {
            if (preg_match('/^\d{4}_\d{2}_\d{2}_\d{6}_.*\.php$/', $file)) {
                $migrations[] = pathinfo($file, PATHINFO_FILENAME);
            }
        }
        
        sort($migrations);
        return $migrations;
    }

    /**
     * Obtener migraciones ejecutadas
     */
    private function getExecutedMigrations()
    {
        try {
            $sql = "SELECT migration FROM {$this->migrationsTable} ORDER BY id";
            $results = $this->database->fetchAll($sql);
            return array_column($results, 'migration');
        } catch (\Exception $e) {
            // Si la tabla no existe, no hay migraciones ejecutadas
            return [];
        }
    }

    /**
     * Obtener siguiente número de lote
     */
    private function getNextBatchNumber()
    {
        try {
            $sql = "SELECT MAX(batch) as max_batch FROM {$this->migrationsTable}";
            $result = $this->database->fetchOne($sql);
            return ($result['max_batch'] ?? 0) + 1;
        } catch (\Exception $e) {
            return 1;
        }
    }

    /**
     * Ejecutar una migración
     */
    private function executeMigration($migrationName, $batch)
    {
        $migrationFile = $this->migrationsPath . '/' . $migrationName . '.php';
        
        if (!file_exists($migrationFile)) {
            throw new \Exception("Archivo de migración no encontrado: {$migrationFile}");
        }

        // Incluir archivo de migración
        require_once $migrationFile;
        
        // Obtener nombre de clase de migración
        $className = $this->getMigrationClassName($migrationName);
        
        if (!class_exists($className)) {
            throw new \Exception("Clase de migración no encontrada: {$className}");
        }

        try {
            // Ejecutar migración
            $migration = new $className($this->database);
            $migration->up();
            
            // Registrar migración ejecutada
            $this->database->insert($this->migrationsTable, [
                'migration' => $migrationName,
                'batch' => $batch
            ]);
            
            echo "Migración ejecutada: {$migrationName}\n";
            
        } catch (\Exception $e) {
            throw new \Exception("Error ejecutando migración {$migrationName}: " . $e->getMessage());
        }
    }

    /**
     * Obtener nombre de clase de migración
     */
    private function getMigrationClassName($migrationName)
    {
        // Extraer nombre de clase del nombre de archivo
        // Ejemplo: 2024_01_01_000000_create_users_table -> CreateUsersTable
        $parts = explode('_', $migrationName);
        $nameParts = array_slice($parts, 4); // Omitir timestamp
        
        $className = '';
        foreach ($nameParts as $part) {
            $className .= ucfirst($part);
        }
        
        return $className;
    }

    /**
     * Crear nueva migración
     */
    public function makeMigration($name)
    {
        if (!is_dir($this->migrationsPath)) {
            mkdir($this->migrationsPath, 0755, true);
        }
        
        $timestamp = date('Y_m_d_His');
        $fileName = $timestamp . '_' . $name . '.php';
        $filePath = $this->migrationsPath . '/' . $fileName;
        
        $className = $this->getMigrationClassName($timestamp . '_' . $name);
        
        $template = $this->getMigrationTemplate($className);
        
        file_put_contents($filePath, $template);
        
        echo "Migración creada: {$fileName}\n";
        return $filePath;
    }

    /**
     * Obtener plantilla de migración
     */
    private function getMigrationTemplate($className)
    {
        return "<?php

use Tukuchi\Core\Migration;

class {$className} extends Migration
{
    /**
     * Ejecutar migración
     */
    public function up()
    {
        // Ejemplo: crear tabla
        \$sql = \"CREATE TABLE IF NOT EXISTS example_table (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci\";
        
        \$this->database->query(\$sql);
    }

    /**
     * Revertir migración
     */
    public function down()
    {
        \$sql = \"DROP TABLE IF EXISTS example_table\";
        \$this->database->query(\$sql);
    }
}
";
    }

    /**
     * Obtener estado de migraciones
     */
    public function status()
    {
        $allMigrations = $this->getAllMigrationFiles();
        $executedMigrations = $this->getExecutedMigrations();
        
        echo "Estado de migraciones:\n";
        echo str_repeat('-', 50) . "\n";
        
        foreach ($allMigrations as $migration) {
            $status = in_array($migration, $executedMigrations) ? 'Ejecutada' : 'Pendiente';
            echo sprintf("%-40s %s\n", $migration, $status);
        }
        
        echo str_repeat('-', 50) . "\n";
        echo "Total: " . count($allMigrations) . " migraciones\n";
        echo "Ejecutadas: " . count($executedMigrations) . "\n";
        echo "Pendientes: " . (count($allMigrations) - count($executedMigrations)) . "\n";
    }
}