<?php
/**
 * Framework Tukuchi - Test Validation
 * Script para probar el sistema de validación
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

use Tukuchi\Core\Validator;

echo "🐦 Framework Tukuchi - Probando Sistema de Validación\n";
echo "====================================================\n\n";

// Datos de prueba válidos
echo "📋 Prueba 1: Datos válidos\n";
echo str_repeat('-', 30) . "\n";

$validData = [
    'name' => 'Juan Pérez',
    'email' => 'juan@example.com',
    'password' => '123456',
    'password_confirmation' => '123456',
    'age' => 25,
    'status' => 'active'
];

$validator = Validator::make($validData, [
    'name' => 'required|min:2|max:50|alpha',
    'email' => 'required|email',
    'password' => 'required|min:6|confirmed',
    'age' => 'required|numeric|between:18,65',
    'status' => 'required|in:active,inactive'
]);

if ($validator->validate()) {
    echo "✅ Validación exitosa - Todos los datos son válidos\n";
} else {
    echo "❌ Validación falló:\n";
    foreach ($validator->errors() as $field => $errors) {
        foreach ($errors as $error) {
            echo "  - {$error}\n";
        }
    }
}

echo "\n";

// Datos de prueba inválidos
echo "📋 Prueba 2: Datos inválidos\n";
echo str_repeat('-', 30) . "\n";

$invalidData = [
    'name' => 'J',  // Muy corto
    'email' => 'email-invalido',  // Email inválido
    'password' => '123',  // Muy corto
    'password_confirmation' => '456',  // No coincide
    'age' => 17,  // Menor al mínimo
    'status' => 'unknown'  // No está en la lista
];

$validator2 = Validator::make($invalidData, [
    'name' => 'required|min:2|max:50|alpha',
    'email' => 'required|email',
    'password' => 'required|min:6|confirmed',
    'age' => 'required|numeric|between:18,65',
    'status' => 'required|in:active,inactive'
], [
    'name.min' => 'El nombre debe tener al menos 2 caracteres',
    'email.email' => 'Por favor ingresa un email válido',
    'password.min' => 'La contraseña debe tener al menos 6 caracteres',
    'password.confirmed' => 'Las contraseñas no coinciden'
]);

if ($validator2->validate()) {
    echo "✅ Validación exitosa\n";
} else {
    echo "❌ Errores encontrados:\n";
    foreach ($validator2->errors() as $field => $errors) {
        foreach ($errors as $error) {
            echo "  - {$error}\n";
        }
    }
}

echo "\n";

// Prueba de reglas específicas
echo "📋 Prueba 3: Reglas específicas\n";
echo str_repeat('-', 30) . "\n";

$testData = [
    'url' => 'https://example.com',
    'alpha_text' => 'Solo Letras',
    'alpha_num_text' => 'Letras123',
    'date' => '2024-12-25',
    'number' => '42'
];

$validator3 = Validator::make($testData, [
    'url' => 'required|url',
    'alpha_text' => 'required|alpha',
    'alpha_num_text' => 'required|alpha_num',
    'date' => 'required|date',
    'number' => 'required|integer'
]);

if ($validator3->validate()) {
    echo "✅ Todas las reglas específicas pasaron\n";
} else {
    echo "❌ Errores en reglas específicas:\n";
    foreach ($validator3->errors() as $field => $errors) {
        foreach ($errors as $error) {
            echo "  - {$error}\n";
        }
    }
}

echo "\n";

// Mostrar métodos útiles del validador
echo "📋 Métodos útiles del validador:\n";
echo str_repeat('-', 30) . "\n";

if ($validator2->fails()) {
    echo "• Primer error del campo 'name': " . $validator2->first('name') . "\n";
    echo "• Todos los errores como array: " . json_encode($validator2->all()) . "\n";
    echo "• ¿Hay errores? " . ($validator2->fails() ? 'Sí' : 'No') . "\n";
}

echo "\n🎉 Pruebas de validación completadas!\n";