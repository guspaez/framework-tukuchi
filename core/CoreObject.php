<?php
/**
 * Framework Tukuchi - CoreObject
 * Clase base para todos los objetos del sistema
 * Implementa accesores transparentes y gestión de eventos
 */

namespace Tukuchi\Core;

class CoreObject
{
    protected $properties = [];
    protected $events = [];

    /**
     * Método mágico para obtener propiedades
     */
    public function __get($name)
    {
        if (isset($this->properties[$name])) {
            return $this->properties[$name];
        }
        
        // Buscar método getter específico
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        return null;
    }

    /**
     * Método mágico para establecer propiedades
     */
    public function __set($name, $value)
    {
        // Buscar método setter específico
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter)) {
            return $this->$setter($value);
        }

        $this->properties[$name] = $value;
        
        // Disparar evento de cambio de propiedad
        $this->fireEvent('propertyChanged', ['property' => $name, 'value' => $value]);
    }

    /**
     * Verificar si una propiedad existe
     */
    public function __isset($name)
    {
        return isset($this->properties[$name]) || method_exists($this, 'get' . ucfirst($name));
    }

    /**
     * Eliminar una propiedad
     */
    public function __unset($name)
    {
        unset($this->properties[$name]);
    }

    /**
     * Obtener todas las propiedades
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Establecer múltiples propiedades
     */
    public function setProperties(array $properties)
    {
        foreach ($properties as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * Registrar un evento
     */
    public function on($event, $callback)
    {
        if (!isset($this->events[$event])) {
            $this->events[$event] = [];
        }
        $this->events[$event][] = $callback;
    }

    /**
     * Disparar un evento
     */
    protected function fireEvent($event, $data = [])
    {
        if (isset($this->events[$event])) {
            foreach ($this->events[$event] as $callback) {
                if (is_callable($callback)) {
                    call_user_func($callback, $data);
                }
            }
        }
    }

    /**
     * Convertir objeto a array
     */
    public function toArray()
    {
        return $this->properties;
    }

    /**
     * Convertir objeto a JSON
     */
    public function toJson()
    {
        return json_encode($this->properties);
    }
}