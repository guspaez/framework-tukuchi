<?php
/**
 * Framework Tukuchi - Configuración Principal
 * Parámetros globales y configuraciones del framework
 */

return [
    // Configuración de la aplicación
    'app' => [
        'name' => 'Framework Tukuchi',
        'version' => '1.0.0',
        'debug' => true, // Cambiar a false en producción
        'timezone' => 'America/Mexico_City',
        'charset' => 'UTF-8',
        'base_url' => 'http://localhost/tukuchi',
        'default_controller' => 'home',
        'default_action' => 'index'
    ],

    // Configuración de base de datos
    'database' => [
        'default' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => 3306,
            'database' => 'tukuchi_db',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        ]
    ],

    // Configuración de sesiones
    'session' => [
        'name' => 'TUKUCHI_SESSION',
        'lifetime' => 7200, // 2 horas
        'path' => '/',
        'domain' => '',
        'secure' => false, // Cambiar a true con HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ],

    // Configuración de seguridad
    'security' => [
        'csrf_protection' => true,
        'csrf_token_name' => '_token',
        'password_hash_algo' => PASSWORD_DEFAULT,
        'encryption_key' => 'tu-clave-secreta-aqui-cambiar-en-produccion'
    ],

    // Configuración de vistas
    'view' => [
        'template_extension' => '.php',
        'cache_enabled' => false,
        'cache_path' => TUKUCHI_PATH . '/storage/cache/views'
    ],

    // Configuración de archivos
    'upload' => [
        'max_size' => 10485760, // 10MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'],
        'upload_path' => TUKUCHI_PUBLIC_PATH . '/uploads'
    ],

    // Configuración de logs
    'logging' => [
        'enabled' => true,
        'level' => 'debug', // debug, info, warning, error
        'path' => TUKUCHI_PATH . '/storage/logs'
    ],

    // Configuración de correo (para futuras implementaciones)
    'mail' => [
        'driver' => 'smtp',
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'username' => '',
        'password' => '',
        'encryption' => 'tls',
        'from' => [
            'address' => 'noreply@tukuchi.com',
            'name' => 'Framework Tukuchi'
        ]
    ]
];