<?php
/**
 * Framework Tukuchi - Create Admin User
 * Script para crear el primer usuario administrador
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
    // Verificar si ya existe un super admin
    $existingAdmin = User::where('role', 'super_admin');
    
    if (!empty($existingAdmin)) {
        echo "⚠️  Ya existe un Super Administrador en el sistema.\n";
        echo "Email: {$existingAdmin[0]->email}\n";
        echo "Nombre: {$existingAdmin[0]->name}\n\n";
        
        echo "¿Deseas crear otro administrador? (y/N): ";
        $response = trim(fgets(STDIN));
        
        if (strtolower($response) !== 'y') {
            echo "Operación cancelada.\n";
            exit(0);
        }
    }

    // Solicitar datos del administrador
    echo "📝 Ingresa los datos del nuevo administrador:\n";
    echo str_repeat('-', 40) . "\n";

    echo "Nombre completo: ";
    $name = trim(fgets(STDIN));

    echo "Email: ";
    $email = trim(fgets(STDIN));

    echo "Contraseña: ";
    $password = trim(fgets(STDIN));

    echo "Tipo de administrador (1=Admin, 2=Super Admin) [1]: ";
    $roleChoice = trim(fgets(STDIN));
    $role = ($roleChoice === '2') ? 'super_admin' : 'admin';

    // Validar datos básicos
    if (empty($name) || empty($email) || empty($password)) {
        echo "❌ Error: Todos los campos son requeridos.\n";
        exit(1);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "❌ Error: Email no válido.\n";
        exit(1);
    }

    if (strlen($password) < 6) {
        echo "❌ Error: La contraseña debe tener al menos 6 caracteres.\n";
        exit(1);
    }

    // Verificar si el email ya existe
    $existingUser = User::findByEmail($email);
    if ($existingUser) {
        echo "❌ Error: Ya existe un usuario con este email.\n";
        exit(1);
    }

    // Crear el usuario administrador
    $admin = new User();
    $admin->name = $name;
    $admin->email = $email;
    $admin->setPassword($password);
    $admin->status = 'active';
    $admin->role = $role;

    if ($admin->save()) {
        echo "\n✅ ¡Usuario administrador creado exitosamente!\n";
        echo str_repeat('-', 40) . "\n";
        echo "ID: {$admin->id}\n";
        echo "Nombre: {$admin->name}\n";
        echo "Email: {$admin->email}\n";
        echo "Rol: {$admin->getRoleLabel()}\n";
        echo "Estado: " . ($admin->isActive() ? 'Activo' : 'Inactivo') . "\n";
        echo "\n🔐 Ahora puedes acceder al panel de administración en:\n";
        echo "http://localhost/tukuchi/admin/auth/login\n";
    } else {
        echo "❌ Error al crear el usuario administrador.\n";
        exit(1);
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}