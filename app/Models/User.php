<?php
/**
 * Framework Tukuchi - User Model
 * Modelo de ejemplo para usuarios
 */

namespace Tukuchi\App\Models;

use Tukuchi\Core\Model;

class User extends Model
{
    protected $table = 'users';
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'role'
    ];
    
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    /**
     * Buscar usuario por email
     */
    public static function findByEmail($email)
    {
        $users = static::where('email', $email);
        if (!empty($users)) {
            $userData = $users[0];
            $user = new self();
            foreach ($userData as $key => $value) {
                $user->$key = $value;
            }
            $user->exists = true;
            return $user;
        }
        return null;
    }

    /**
     * Verificar contraseña
     */
    public function verifyPassword($password)
    {
        return password_verify($password, $this->password);
    }

    /**
     * Establecer contraseña (hash automático)
     */
    public function setPassword($password)
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verificar si el usuario está activo
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Verificar si el usuario es administrador
     */
    public function isAdmin()
    {
        return $this->role === 'admin' || $this->role === 'super_admin';
    }

    /**
     * Verificar si el usuario es super administrador
     */
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    /**
     * Verificar permisos específicos
     */
    public function hasPermission($permission)
    {
        // Super admin tiene todos los permisos
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Admin tiene permisos básicos
        if ($this->isAdmin()) {
            $adminPermissions = [
                'view_dashboard',
                'manage_users',
                'view_logs',
                'manage_database',
                'view_settings'
            ];
            return in_array($permission, $adminPermissions);
        }

        return false;
    }

    /**
     * Obtener nombre completo
     */
    public function getFullName()
    {
        return $this->name;
    }

    /**
     * Obtener usuarios activos
     */
    public static function getActiveUsers()
    {
        return static::where('status', 'active');
    }

    /**
     * Obtener usuarios administradores
     */
    public static function getAdminUsers()
    {
        $admins = static::where('role', 'admin');
        $superAdmins = static::where('role', 'super_admin');
        return array_merge($admins, $superAdmins);
    }

    /**
     * Obtener rol formateado
     */
    public function getRoleLabel()
    {
        switch ($this->role) {
            case 'super_admin':
                return 'Super Administrador';
            case 'admin':
                return 'Administrador';
            case 'user':
            default:
                return 'Usuario';
        }
    }

    /**
     * Validar datos del usuario
     */
    public function validate()
    {
        $errors = [];

        // Validar nombre
        if (empty($this->name)) {
            $errors['name'] = 'El nombre es requerido';
        } elseif (strlen($this->name) < 2) {
            $errors['name'] = 'El nombre debe tener al menos 2 caracteres';
        }

        // Validar email
        if (empty($this->email)) {
            $errors['email'] = 'El email es requerido';
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El email no es válido';
        } else {
            // Verificar email único (solo si es nuevo usuario o cambió el email)
            $existingUser = static::findByEmail($this->email);
            if ($existingUser && (!$this->exists || $existingUser->id !== $this->id)) {
                $errors['email'] = 'Este email ya está registrado';
            }
        }

        // Validar contraseña (solo para nuevos usuarios)
        if (!$this->exists && empty($this->password)) {
            $errors['password'] = 'La contraseña es requerida';
        } elseif (!empty($this->password) && strlen($this->password) < 6) {
            $errors['password'] = 'La contraseña debe tener al menos 6 caracteres';
        }

        // Validar rol
        $validRoles = ['user', 'admin', 'super_admin'];
        if (!empty($this->role) && !in_array($this->role, $validRoles)) {
            $errors['role'] = 'Rol no válido';
        }

        return $errors;
    }

    /**
     * Guardar con validación
     */
    public function save()
    {
        $errors = $this->validate();
        
        if (!empty($errors)) {
            throw new \Exception('Errores de validación: ' . json_encode($errors));
        }

        return parent::save();
    }
}
