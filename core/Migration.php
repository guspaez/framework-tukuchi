<?php
/**
 * Framework Tukuchi - Migration
 * Sistema de migraciones para base de datos
 */

namespace Tukuchi\Core;

abstract class Migration
{
    protected $database;
    protected $schema;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->schema = new Schema($database);
    }

    /**
     * Ejecutar migración
     */
    abstract public function up();

    /**
     * Revertir migración
     */
    abstract public function down();

    /**
     * Obtener nombre de la migración
     */
    public function getName()
    {
        return get_class($this);
    }
}

/**
 * Clase Schema para construcción de tablas
 */
class Schema
{
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * Crear tabla
     */
    public function create($table, callable $callback)
    {
        $blueprint = new Blueprint($table, $this->database);
        $callback($blueprint);
        $blueprint->build();
    }

    /**
     * Modificar tabla
     */
    public function table($table, callable $callback)
    {
        $blueprint = new Blueprint($table, $this->database, 'alter');
        $callback($blueprint);
        $blueprint->build();
    }

    /**
     * Eliminar tabla
     */
    public function drop($table)
    {
        $sql = "DROP TABLE IF EXISTS {$table}";
        $this->database->query($sql);
    }

    /**
     * Verificar si tabla existe
     */
    public function hasTable($table)
    {
        return $this->database->tableExists($table);
    }
}

/**
 * Clase Blueprint para definición de estructura de tablas
 */
class Blueprint
{
    private $table;
    private $database;
    private $columns = [];
    private $indexes = [];
    private $action;

    public function __construct($table, Database $database, $action = 'create')
    {
        $this->table = $table;
        $this->database = $database;
        $this->action = $action;
    }

    /**
     * Columna ID auto-incremental
     */
    public function id($name = 'id')
    {
        return $this->bigIncrements($name);
    }

    /**
     * Columna big integer auto-incremental
     */
    public function bigIncrements($name)
    {
        $this->columns[] = [
            'name' => $name,
            'type' => 'BIGINT',
            'auto_increment' => true,
            'primary' => true,
            'unsigned' => true,
            'nullable' => false
        ];
        return $this;
    }

    /**
     * Columna string/varchar
     */
    public function string($name, $length = 255)
    {
        $this->columns[] = [
            'name' => $name,
            'type' => 'VARCHAR',
            'length' => $length,
            'nullable' => true
        ];
        return $this;
    }

    /**
     * Columna text
     */
    public function text($name)
    {
        $this->columns[] = [
            'name' => $name,
            'type' => 'TEXT',
            'nullable' => true
        ];
        return $this;
    }

    /**
     * Columna integer
     */
    public function integer($name)
    {
        $this->columns[] = [
            'name' => $name,
            'type' => 'INT',
            'nullable' => true
        ];
        return $this;
    }

    /**
     * Columna boolean
     */
    public function boolean($name)
    {
        $this->columns[] = [
            'name' => $name,
            'type' => 'TINYINT',
            'length' => 1,
            'default' => 0,
            'nullable' => true
        ];
        return $this;
    }

    /**
     * Columna timestamp
     */
    public function timestamp($name)
    {
        $this->columns[] = [
            'name' => $name,
            'type' => 'TIMESTAMP',
            'nullable' => true
        ];
        return $this;
    }

    /**
     * Columnas de timestamps (created_at, updated_at)
     */
    public function timestamps()
    {
        $this->timestamp('created_at')->nullable();
        $this->timestamp('updated_at')->nullable();
        return $this;
    }

    /**
     * Hacer columna no nullable
     */
    public function nullable($nullable = true)
    {
        if (!empty($this->columns)) {
            $lastIndex = count($this->columns) - 1;
            $this->columns[$lastIndex]['nullable'] = $nullable;
        }
        return $this;
    }

    /**
     * Establecer valor por defecto
     */
    public function default($value)
    {
        if (!empty($this->columns)) {
            $lastIndex = count($this->columns) - 1;
            $this->columns[$lastIndex]['default'] = $value;
        }
        return $this;
    }

    /**
     * Hacer columna única
     */
    public function unique()
    {
        if (!empty($this->columns)) {
            $lastIndex = count($this->columns) - 1;
            $columnName = $this->columns[$lastIndex]['name'];
            $this->indexes[] = [
                'type' => 'UNIQUE',
                'columns' => [$columnName],
                'name' => "unique_{$this->table}_{$columnName}"
            ];
        }
        return $this;
    }

    /**
     * Agregar índice
     */
    public function index($columns, $name = null)
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }
        
        $indexName = $name ?: 'idx_' . $this->table . '_' . implode('_', $columns);
        
        $this->indexes[] = [
            'type' => 'INDEX',
            'columns' => $columns,
            'name' => $indexName
        ];
        return $this;
    }

    /**
     * Construir y ejecutar SQL
     */
    public function build()
    {
        if ($this->action === 'create') {
            $this->buildCreateTable();
        } elseif ($this->action === 'alter') {
            $this->buildAlterTable();
        }
    }

    /**
     * Construir CREATE TABLE
     */
    private function buildCreateTable()
    {
        $sql = "CREATE TABLE {$this->table} (\n";
        
        $columnDefinitions = [];
        $primaryKeys = [];
        
        foreach ($this->columns as $column) {
            $definition = "  {$column['name']} {$column['type']}";
            
            if (isset($column['length'])) {
                $definition .= "({$column['length']})";
            }
            
            if (isset($column['unsigned']) && $column['unsigned']) {
                $definition .= " UNSIGNED";
            }
            
            if (!$column['nullable']) {
                $definition .= " NOT NULL";
            }
            
            if (isset($column['auto_increment']) && $column['auto_increment']) {
                $definition .= " AUTO_INCREMENT";
            }
            
            if (isset($column['default'])) {
                $definition .= " DEFAULT " . $this->formatDefaultValue($column['default']);
            }
            
            $columnDefinitions[] = $definition;
            
            if (isset($column['primary']) && $column['primary']) {
                $primaryKeys[] = $column['name'];
            }
        }
        
        if (!empty($primaryKeys)) {
            $columnDefinitions[] = "  PRIMARY KEY (" . implode(', ', $primaryKeys) . ")";
        }
        
        $sql .= implode(",\n", $columnDefinitions);
        $sql .= "\n)";
        
        $this->database->query($sql);
        
        // Crear índices
        foreach ($this->indexes as $index) {
            $this->createIndex($index);
        }
    }

    /**
     * Construir ALTER TABLE
     */
    private function buildAlterTable()
    {
        foreach ($this->columns as $column) {
            $sql = "ALTER TABLE {$this->table} ADD COLUMN {$column['name']} {$column['type']}";
            
            if (isset($column['length'])) {
                $sql .= "({$column['length']})";
            }
            
            if (!$column['nullable']) {
                $sql .= " NOT NULL";
            }
            
            if (isset($column['default'])) {
                $sql .= " DEFAULT " . $this->formatDefaultValue($column['default']);
            }
            
            $this->database->query($sql);
        }
        
        // Crear índices
        foreach ($this->indexes as $index) {
            $this->createIndex($index);
        }
    }

    /**
     * Crear índice
     */
    private function createIndex($index)
    {
        $sql = "CREATE {$index['type']} {$index['name']} ON {$this->table} (" . implode(', ', $index['columns']) . ")";
        $this->database->query($sql);
    }

    /**
     * Formatear valor por defecto
     */
    private function formatDefaultValue($value)
    {
        if (is_string($value)) {
            return "'{$value}'";
        } elseif (is_bool($value)) {
            return $value ? '1' : '0';
        } elseif (is_null($value)) {
            return 'NULL';
        }
        
        return $value;
    }
}