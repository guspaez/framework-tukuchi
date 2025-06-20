<?php
/**
 * Framework Tukuchi - Create Admin User (Simple)
 * Script para crear el primer usuario administrador automáticamente
 */

// Definir constantes del framework
define('TUKUCHI_PATH', __DIR__);
define('TUKUCHI_CORE_PATH', TUKUCHI_PATH . '/core');
define('TUKUCHI_APP_PATH', TUKUCHI_PATH . '/app');
define('TUKUCHI_CONFIG_PATH', TUKUCHI_PATH . '/config');
define('TUKUCHI_PUBLIC_PATH', TUKUCHI_PATH . '/public');

// Autoloader del framework
require_once TUKUCHI_CORE_PATH . '/Autoloader.php';

// Inicializar el autoloader
Tukuchi\Core\Autoloader::register();

// Cargar configuración
$config = require_once TUKUCHI_CONFIG_PATH . '/app.php';

use Tukuchi\App\Models\User;

echo "🐦 Framework Tukuchi - Crear Usuario Administrador\n";
echo "=================================================\n\n";

try {
    // Datos del administrador por defecto
    $adminData = [
        'name' => 'Administrador Tukuchi',
        'email' => 'admin@tukuchi.com',
        'password' => 'admin123',
        'role' => 'super_admin'
    ];

    // Verificar si ya existe
    $existingUser = User::findByEmail($adminData['email']);
    if ($existingUser) {
        echo "⚠️  El usuario administrador ya existe:\n";
        echo "Email: {$existingUser->email}\n";
        echo "Nombre: {$existingUser->name}\n";
        echo "Rol: {$existingUser->getRoleLabel()}\n\n";
        
        // Actualizar a super_admin si no lo es
        if (!$existingUser->isSuperAdmin()) {
            $existingUser->role = 'super_admin';
            $existingUser->save();
            echo "✅ Usuario actualizado a Super Administrador.\n";
        }
        
        echo "🔐 Credenciales de acceso:\n";
        echo "Email: {$existingUser->email}\n";
        echo "Contraseña: admin123 (si no la has cambiado)\n";
        exit(0);
    }

    // Crear el usuario administrador
    $admin = new User();
    $admin->name = $adminData['name'];
    $admin->email = $adminData['email'];
    $admin->setPassword($adminData['password']);
    $admin->status = 'active';
    $admin->role = $adminData['role'];

    if ($admin->save()) {
        echo "✅ ¡Usuario administrador creado exitosamente!\n";
        echo str_repeat('-', 50) . "\n";
        echo "ID: {$admin->id}\n";
        echo "Nombre: {$admin->name}\n";
        echo "Email: {$admin->email}\n";
        echo "Rol: {$admin->getRoleLabel()}\n";
        echo "Estado: " . ($admin->isActive() ? 'Activo' : 'Inactivo') . "\n";
        echo "\n🔐 Credenciales de acceso:\n";
        echo "Email: {$admin->email}\n";
        echo "Contraseña: {$adminData['password']}\n";
        echo "\n🌐 Panel de administración:\n";
        echo "http://localhost/tukuchi/admin/auth/login\n";
    } else {
        echo "❌ Error al crear el usuario administrador.\n";
        exit(1);
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}