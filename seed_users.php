<?php
/**
 * Framework Tukuchi - Seed Users
 * Script para crear usuarios de prueba
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

// Crear instancia de base de datos
$database = new Tukuchi\Core\Database($config['database']);

echo "🐦 Framework Tukuchi - Creando usuarios de prueba\n";
echo "================================================\n\n";

try {
    // Usuarios de prueba
    $users = [
        [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => password_hash('123456', PASSWORD_DEFAULT),
            'status' => 'active'
        ],
        [
            'name' => 'María García',
            'email' => 'maria@example.com',
            'password' => password_hash('123456', PASSWORD_DEFAULT),
            'status' => 'active'
        ],
        [
            'name' => 'Carlos López',
            'email' => 'carlos@example.com',
            'password' => password_hash('123456', PASSWORD_DEFAULT),
            'status' => 'inactive'
        ],
        [
            'name' => 'Ana Martínez',
            'email' => 'ana@example.com',
            'password' => password_hash('123456', PASSWORD_DEFAULT),
            'status' => 'active'
        ],
        [
            'name' => 'Luis Rodríguez',
            'email' => 'luis@example.com',
            'password' => password_hash('123456', PASSWORD_DEFAULT),
            'status' => 'active'
        ]
    ];

    foreach ($users as $userData) {
        // Verificar si el usuario ya existe
        $existing = $database->fetchOne(
            "SELECT id FROM users WHERE email = ?", 
            [$userData['email']]
        );

        if (!$existing) {
            $userData['created_at'] = date('Y-m-d H:i:s');
            $userData['updated_at'] = date('Y-m-d H:i:s');
            
            $id = $database->insert('users', $userData);
            echo "✅ Usuario creado: {$userData['name']} (ID: {$id})\n";
        } else {
            echo "⚠️  Usuario ya existe: {$userData['name']}\n";
        }
    }

    echo "\n🎉 Proceso completado!\n";
    echo "Usuarios creados con contraseña: 123456\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}