<?php
/**
 * Framework Tukuchi - Test Models
 * Script para probar los modelos ActiveRecord
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

echo "🐦 Framework Tukuchi - Probando Modelos ActiveRecord\n";
echo "===================================================\n\n";

try {
    // Prueba 1: Obtener todos los usuarios
    echo "📋 Prueba 1: Obtener todos los usuarios\n";
    echo str_repeat('-', 40) . "\n";
    
    $users = User::all();
    echo "✅ Se encontraron " . count($users) . " usuarios:\n";
    
    foreach ($users as $user) {
        echo "  - ID: {$user->id}, Nombre: {$user->name}, Email: {$user->email}, Estado: {$user->status}\n";
    }
    
    echo "\n";

    // Prueba 2: Buscar usuario por ID
    echo "📋 Prueba 2: Buscar usuario por ID\n";
    echo str_repeat('-', 40) . "\n";
    
    $user = User::find(1);
    if ($user) {
        echo "✅ Usuario encontrado:\n";
        echo "  - Nombre: {$user->name}\n";
        echo "  - Email: {$user->email}\n";
        echo "  - Estado: {$user->status}\n";
        echo "  - ¿Está activo? " . ($user->isActive() ? 'Sí' : 'No') . "\n";
    } else {
        echo "❌ Usuario no encontrado\n";
    }
    
    echo "\n";

    // Prueba 3: Buscar por email
    echo "📋 Prueba 3: Buscar por email\n";
    echo str_repeat('-', 40) . "\n";
    
    $user = User::findByEmail('juan@example.com');
    if ($user) {
        echo "✅ Usuario encontrado por email:\n";
        echo "  - Nombre: {$user->name}\n";
        echo "  - ID: {$user->id}\n";
    } else {
        echo "❌ Usuario no encontrado por email\n";
    }
    
    echo "\n";

    // Prueba 4: Buscar usuarios activos
    echo "📋 Prueba 4: Buscar usuarios activos\n";
    echo str_repeat('-', 40) . "\n";
    
    $activeUsers = User::getActiveUsers();
    echo "✅ Se encontraron " . count($activeUsers) . " usuarios activos:\n";
    
    foreach ($activeUsers as $user) {
        echo "  - {$user->name} ({$user->email})\n";
    }
    
    echo "\n";

    // Prueba 5: Crear nuevo usuario
    echo "📋 Prueba 5: Crear nuevo usuario\n";
    echo str_repeat('-', 40) . "\n";
    
    $newUser = new User();
    $newUser->name = 'Usuario de Prueba';
    $newUser->email = 'prueba@example.com';
    $newUser->setPassword('password123');
    $newUser->status = 'active';
    
    if ($newUser->save()) {
        echo "✅ Usuario creado exitosamente:\n";
        echo "  - ID: {$newUser->id}\n";
        echo "  - Nombre: {$newUser->name}\n";
        echo "  - Email: {$newUser->email}\n";
    } else {
        echo "❌ Error al crear usuario\n";
    }
    
    echo "\n";

    // Prueba 6: Actualizar usuario
    echo "📋 Prueba 6: Actualizar usuario\n";
    echo str_repeat('-', 40) . "\n";
    
    if ($newUser->exists) {
        $newUser->name = 'Usuario Actualizado';
        
        if ($newUser->save()) {
            echo "✅ Usuario actualizado exitosamente:\n";
            echo "  - Nuevo nombre: {$newUser->name}\n";
        } else {
            echo "��� Error al actualizar usuario\n";
        }
    }
    
    echo "\n";

    // Prueba 7: Verificar contraseña
    echo "📋 Prueba 7: Verificar contraseña\n";
    echo str_repeat('-', 40) . "\n";
    
    if ($newUser->verifyPassword('password123')) {
        echo "✅ Contraseña verificada correctamente\n";
    } else {
        echo "❌ Contraseña incorrecta\n";
    }
    
    echo "\n";

    // Prueba 8: Contar usuarios
    echo "📋 Prueba 8: Contar usuarios\n";
    echo str_repeat('-', 40) . "\n";
    
    $totalUsers = User::count();
    echo "✅ Total de usuarios en la base de datos: {$totalUsers}\n";
    
    echo "\n";

    // Prueba 9: Buscar con WHERE
    echo "📋 Prueba 9: Buscar con WHERE\n";
    echo str_repeat('-', 40) . "\n";
    
    $inactiveUsers = User::where('status', 'inactive');
    echo "✅ Usuarios inactivos encontrados: " . count($inactiveUsers) . "\n";
    
    foreach ($inactiveUsers as $user) {
        echo "  - {$user->name} ({$user->email})\n";
    }
    
    echo "\n";

    // Prueba 10: Eliminar usuario de prueba
    echo "📋 Prueba 10: Eliminar usuario de prueba\n";
    echo str_repeat('-', 40) . "\n";
    
    if ($newUser->delete()) {
        echo "✅ Usuario de prueba eliminado exitosamente\n";
    } else {
        echo "❌ Error al eliminar usuario de prueba\n";
    }

    echo "\n🎉 Todas las pruebas de modelos completadas!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}