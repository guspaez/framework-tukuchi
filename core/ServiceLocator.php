<?php
/**
 * Framework Tukuchi - Service Locator / Inyector de Dependencias
 * Corazón del framework para gestión de dependencias
 */

namespace Tukuchi\Core;

class ServiceLocator
{
    private $services = [];
    private $instances = [];

    /**
     * Registrar un servicio
     */
    public function register($name, $definition)
    {
        $this->services[$name] = $definition;
    }

    /**
     * Obtener un servicio
     */
    public function get($name)
    {
        // Si ya existe una instancia, devolverla (singleton)
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        // Si no existe el servicio, lanzar excepción
        if (!isset($this->services[$name])) {
            throw new \Exception("Servicio no registrado: {$name}");
        }

        $definition = $this->services[$name];

        // Si es un callable, ejecutarlo
        if (is_callable($definition)) {
            $instance = $definition();
        } else {
            $instance = $definition;
        }

        // Guardar instancia para reutilización
        $this->instances[$name] = $instance;

        return $instance;
    }

    /**
     * Verificar si un servicio existe
     */
    public function has($name)
    {
        return isset($this->services[$name]);
    }

    /**
     * Remover un servicio
     */
    public function remove($name)
    {
        unset($this->services[$name]);
        unset($this->instances[$name]);
    }

    /**
     * Obtener todos los servicios registrados
     */
    public function getRegisteredServices()
    {
        return array_keys($this->services);
    }
}