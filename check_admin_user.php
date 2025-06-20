<?php
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

echo "=== Verificando Usuario Administrador ===" . PHP_EOL;

$user = User::findByEmail('admin@tukuchi.com');

if ($user) {
    echo "✅ Usuario encontrado:" . PHP_EOL;
    echo "ID: " . $user->id . PHP_EOL;
    echo "Email: " . $user->email . PHP_EOL;
    echo "Name: " . $user->name . PHP_EOL;
    echo "Role: " . $user->role . PHP_EOL;
    echo "Status: " . $user->status . PHP_EOL;
    echo "Password hash: " . substr($user->password, 0, 30) . "..." . PHP_EOL;
    echo "Is Admin: " . ($user->isAdmin() ? 'SÍ' : 'NO') . PHP_EOL;
    echo "Is Active: " . ($user->isActive() ? 'SÍ' : 'NO') . PHP_EOL;
    
    // Verificar contraseña
    echo PHP_EOL . "=== Verificando Contraseña ===" . PHP_EOL;
    $testPassword = 'admin123';
    echo "Probando contraseña: " . $testPassword . PHP_EOL;
    echo "Verificación: " . ($user->verifyPassword($testPassword) ? 'CORRECTA' : 'INCORRECTA') . PHP_EOL;
    
} else {
    echo "❌ Usuario NO encontrado" . PHP_EOL;
    
    // Buscar todos los usuarios para debug
    echo PHP_EOL . "=== Usuarios en la base de datos ===" . PHP_EOL;
    $users = User::all();
    if (empty($users)) {
        echo "No hay usuarios en la base de datos" . PHP_EOL;
    } else {
        foreach ($users as $u) {
            echo "- ID: {$u->id}, Email: {$u->email}, Role: {$u->role}" . PHP_EOL;
        }
    }
}