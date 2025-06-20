<?php
/**
 * Framework Tukuchi - Model Base
 * Clase base para modelos con funcionalidades ActiveRecord
 */

namespace Tukuchi\Core;

abstract class Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $guarded = ['id'];
    protected $timestamps = true;
    protected $dateFormat = 'Y-m-d H:i:s';
    
    protected static $database;
    protected $exists = false;
    protected $original = [];
    protected $attributes = [];

    public function __construct($attributes = [])
    {
        // Inicializar base de datos si no existe
        if (!static::$database) {
            $config = require TUKUCHI_CONFIG_PATH . '/app.php';
            static::$database = new Database($config['database']);
        }
        
        // Establecer nombre de tabla si no está definido
        if (!$this->table) {
            $this->table = $this->getTableName();
        }
        
        // Llenar atributos
        $this->fill($attributes);
    }

    /**
     * Método mágico para obtener propiedades
     */
    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Método mágico para establecer propiedades
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Verificar si una propiedad existe
     */
    public function __isset($name)
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Obtener instancia de base de datos
     */
    protected static function getDatabase()
    {
        if (!static::$database) {
            $config = require TUKUCHI_CONFIG_PATH . '/app.php';
            static::$database = new Database($config['database']);
        }
        return static::$database;
    }

    /**
     * Obtener nombre de tabla basado en el nombre de la clase
     */
    protected function getTableName()
    {
        $className = (new \ReflectionClass($this))->getShortName();
        // Convertir CamelCase a snake_case y pluralizar
        $tableName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $className));
        return $tableName . 's'; // Pluralización simple
    }

    /**
     * Llenar modelo con atributos
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $this->attributes[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * Verificar si un atributo es fillable
     */
    protected function isFillable($key)
    {
        if (in_array($key, $this->guarded)) {
            return false;
        }
        
        if (empty($this->fillable)) {
            return true;
        }
        
        return in_array($key, $this->fillable);
    }

    /**
     * Guardar modelo
     */
    public function save()
    {
        if ($this->exists) {
            return $this->performUpdate();
        } else {
            return $this->performInsert();
        }
    }

    /**
     * Realizar inserción
     */
    protected function performInsert()
    {
        $attributes = $this->getAttributesForInsert();
        
        if ($this->timestamps) {
            $now = date($this->dateFormat);
            $attributes['created_at'] = $now;
            $attributes['updated_at'] = $now;
        }
        
        $id = static::getDatabase()->insert($this->table, $attributes);
        
        if ($id) {
            $this->attributes[$this->primaryKey] = $id;
            $this->exists = true;
            $this->syncOriginal();
            return true;
        }
        
        return false;
    }

    /**
     * Realizar actualización
     */
    protected function performUpdate()
    {
        $attributes = $this->getAttributesForUpdate();
        
        if (empty($attributes)) {
            return true; // No hay cambios
        }
        
        if ($this->timestamps) {
            $attributes['updated_at'] = date($this->dateFormat);
        }
        
        $where = "{$this->primaryKey} = ?";
        $whereParams = [$this->attributes[$this->primaryKey]];
        
        $affected = static::getDatabase()->update($this->table, $attributes, $where, $whereParams);
        
        if ($affected > 0) {
            $this->syncOriginal();
            return true;
        }
        
        return false;
    }

    /**
     * Obtener atributos para inserción
     */
    protected function getAttributesForInsert()
    {
        $attributes = [];
        foreach ($this->attributes as $key => $value) {
            if ($key !== $this->primaryKey && $this->isFillable($key)) {
                $attributes[$key] = $value;
            }
        }
        return $attributes;
    }

    /**
     * Obtener atributos para actualización
     */
    protected function getAttributesForUpdate()
    {
        $attributes = [];
        foreach ($this->attributes as $key => $value) {
            if ($key !== $this->primaryKey && $this->isFillable($key)) {
                if (!isset($this->original[$key]) || $this->original[$key] !== $value) {
                    $attributes[$key] = $value;
                }
            }
        }
        return $attributes;
    }

    /**
     * Eliminar modelo
     */
    public function delete()
    {
        if (!$this->exists) {
            return false;
        }
        
        $where = "{$this->primaryKey} = ?";
        $params = [$this->attributes[$this->primaryKey]];
        
        $affected = static::getDatabase()->delete($this->table, $where, $params);
        
        if ($affected > 0) {
            $this->exists = false;
            return true;
        }
        
        return false;
    }

    /**
     * Buscar por ID
     */
    public static function find($id)
    {
        $instance = new static();
        
        $sql = "SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = ? LIMIT 1";
        $result = static::getDatabase()->fetchOne($sql, [$id]);
        
        if ($result) {
            $model = new static($result);
            $model->exists = true;
            $model->syncOriginal();
            return $model;
        }
        
        return null;
    }

    /**
     * Buscar por ID o lanzar excepción
     */
    public static function findOrFail($id)
    {
        $model = static::find($id);
        
        if (!$model) {
            throw new \Exception("Modelo no encontrado con ID: {$id}");
        }
        
        return $model;
    }

    /**
     * Obtener todos los registros
     */
    public static function all()
    {
        $instance = new static();
        
        $sql = "SELECT * FROM {$instance->table}";
        $results = static::getDatabase()->fetchAll($sql);
        
        $models = [];
        foreach ($results as $result) {
            $model = new static($result);
            $model->exists = true;
            $model->syncOriginal();
            $models[] = $model;
        }
        
        return $models;
    }

    /**
     * Buscar por atributos
     */
    public static function where($column, $operator, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $instance = new static();
        
        $sql = "SELECT * FROM {$instance->table} WHERE {$column} {$operator} ?";
        $results = static::getDatabase()->fetchAll($sql, [$value]);
        
        $models = [];
        foreach ($results as $result) {
            $model = new static($result);
            $model->exists = true;
            $model->syncOriginal();
            $models[] = $model;
        }
        
        return $models;
    }

    /**
     * Crear nuevo registro
     */
    public static function create(array $attributes)
    {
        $model = new static($attributes);
        
        if ($model->save()) {
            return $model;
        }
        
        return false;
    }

    /**
     * Contar registros
     */
    public static function count()
    {
        $instance = new static();
        
        $sql = "SELECT COUNT(*) FROM {$instance->table}";
        return (int) static::getDatabase()->fetchColumn($sql);
    }

    /**
     * Sincronizar atributos originales
     */
    protected function syncOriginal()
    {
        $this->original = $this->attributes;
    }

    /**
     * Verificar si el modelo ha sido modificado
     */
    public function isDirty($key = null)
    {
        if ($key !== null) {
            return isset($this->attributes[$key]) && 
                   (!isset($this->original[$key]) || $this->original[$key] !== $this->attributes[$key]);
        }
        
        foreach ($this->attributes as $key => $value) {
            if ($this->isDirty($key)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Convertir a array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Convertir a JSON
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }
}