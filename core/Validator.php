<?php
/**
 * Framework Tukuchi - Validator
 * Sistema de validación avanzado para formularios
 */

namespace Tukuchi\Core;

class Validator
{
    private $data = [];
    private $rules = [];
    private $messages = [];
    private $errors = [];
    private $customMessages = [];

    public function __construct(array $data, array $rules, array $messages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->customMessages = $messages;
        $this->setDefaultMessages();
    }

    /**
     * Establecer mensajes por defecto
     */
    private function setDefaultMessages()
    {
        $this->messages = [
            'required' => 'El campo {field} es requerido.',
            'email' => 'El campo {field} debe ser un email válido.',
            'min' => 'El campo {field} debe tener al menos {min} caracteres.',
            'max' => 'El campo {field} no puede tener más de {max} caracteres.',
            'numeric' => 'El campo {field} debe ser numérico.',
            'integer' => 'El campo {field} debe ser un número entero.',
            'alpha' => 'El campo {field} solo puede contener letras.',
            'alpha_num' => 'El campo {field} solo puede contener letras y números.',
            'url' => 'El campo {field} debe ser una URL válida.',
            'confirmed' => 'La confirmación de {field} no coincide.',
            'unique' => 'El valor del campo {field} ya existe.',
            'exists' => 'El valor seleccionado en {field} no es válido.',
            'in' => 'El valor seleccionado en {field} no es válido.',
            'not_in' => 'El valor seleccionado en {field} no es válido.',
            'regex' => 'El formato del campo {field} no es válido.',
            'date' => 'El campo {field} debe ser una fecha válida.',
            'before' => 'El campo {field} debe ser una fecha anterior a {date}.',
            'after' => 'El campo {field} debe ser una fecha posterior a {date}.',
            'same' => 'El campo {field} debe coincidir con {other}.',
            'different' => 'El campo {field} debe ser diferente de {other}.',
            'file' => 'El campo {field} debe ser un archivo.',
            'image' => 'El campo {field} debe ser una imagen.',
            'mimes' => 'El campo {field} debe ser un archivo de tipo: {types}.',
            'size' => 'El campo {field} debe pesar {size}KB.',
            'between' => 'El campo {field} debe estar entre {min} y {max}.',
        ];
    }

    /**
     * Validar datos
     */
    public function validate()
    {
        foreach ($this->rules as $field => $rules) {
            $this->validateField($field, $rules);
        }

        return empty($this->errors);
    }

    /**
     * Validar un campo específico
     */
    private function validateField($field, $rules)
    {
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }

        $value = $this->getValue($field);

        foreach ($rules as $rule) {
            $this->applyRule($field, $value, $rule);
        }
    }

    /**
     * Obtener valor del campo
     */
    private function getValue($field)
    {
        return $this->data[$field] ?? null;
    }

    /**
     * Aplicar regla de validación
     */
    private function applyRule($field, $value, $rule)
    {
        if (strpos($rule, ':') !== false) {
            [$ruleName, $parameters] = explode(':', $rule, 2);
            $parameters = explode(',', $parameters);
        } else {
            $ruleName = $rule;
            $parameters = [];
        }

        $method = 'validate' . ucfirst($ruleName);

        if (method_exists($this, $method)) {
            $passes = $this->$method($field, $value, $parameters);

            if (!$passes) {
                $this->addError($field, $ruleName, $parameters);
            }
        }
    }

    /**
     * Agregar error
     */
    private function addError($field, $rule, $parameters = [])
    {
        $message = $this->getMessage($field, $rule, $parameters);
        
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        
        $this->errors[$field][] = $message;
    }

    /**
     * Obtener mensaje de error
     */
    private function getMessage($field, $rule, $parameters = [])
    {
        $key = "{$field}.{$rule}";
        
        if (isset($this->customMessages[$key])) {
            $message = $this->customMessages[$key];
        } elseif (isset($this->customMessages[$rule])) {
            $message = $this->customMessages[$rule];
        } else {
            $message = $this->messages[$rule] ?? 'El campo {field} no es válido.';
        }

        // Reemplazar placeholders
        $replacements = array_merge([
            'field' => $this->getFieldName($field),
        ], $this->getParameterReplacements($rule, $parameters));

        foreach ($replacements as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }

        return $message;
    }

    /**
     * Obtener nombre del campo
     */
    private function getFieldName($field)
    {
        // Convertir snake_case a formato legible
        return ucfirst(str_replace('_', ' ', $field));
    }

    /**
     * Obtener reemplazos de parámetros
     */
    private function getParameterReplacements($rule, $parameters)
    {
        $replacements = [];

        switch ($rule) {
            case 'min':
                $replacements['min'] = $parameters[0] ?? '';
                break;
            case 'max':
                $replacements['max'] = $parameters[0] ?? '';
                break;
            case 'between':
                $replacements['min'] = $parameters[0] ?? '';
                $replacements['max'] = $parameters[1] ?? '';
                break;
            case 'mimes':
                $replacements['types'] = implode(', ', $parameters);
                break;
            case 'size':
                $replacements['size'] = $parameters[0] ?? '';
                break;
            case 'same':
            case 'different':
                $replacements['other'] = $this->getFieldName($parameters[0] ?? '');
                break;
            case 'before':
            case 'after':
                $replacements['date'] = $parameters[0] ?? '';
                break;
        }

        return $replacements;
    }

    // Reglas de validación

    /**
     * Validar campo requerido
     */
    protected function validateRequired($field, $value, $parameters)
    {
        if (is_null($value)) {
            return false;
        }

        if (is_string($value) && trim($value) === '') {
            return false;
        }

        if (is_array($value) && empty($value)) {
            return false;
        }

        return true;
    }

    /**
     * Validar email
     */
    protected function validateEmail($field, $value, $parameters)
    {
        if (is_null($value) || $value === '') {
            return true;
        }

        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validar longitud mínima
     */
    protected function validateMin($field, $value, $parameters)
    {
        if (is_null($value) || $value === '') {
            return true;
        }

        $min = (int) $parameters[0];
        return mb_strlen($value) >= $min;
    }

    /**
     * Validar longitud máxima
     */
    protected function validateMax($field, $value, $parameters)
    {
        if (is_null($value) || $value === '') {
            return true;
        }

        $max = (int) $parameters[0];
        return mb_strlen($value) <= $max;
    }

    /**
     * Validar numérico
     */
    protected function validateNumeric($field, $value, $parameters)
    {
        if (is_null($value) || $value === '') {
            return true;
        }

        return is_numeric($value);
    }

    /**
     * Validar entero
     */
    protected function validateInteger($field, $value, $parameters)
    {
        if (is_null($value) || $value === '') {
            return true;
        }

        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Validar solo letras
     */
    protected function validateAlpha($field, $value, $parameters)
    {
        if (is_null($value) || $value === '') {
            return true;
        }

        return preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $value);
    }

    /**
     * Validar letras y números
     */
    protected function validateAlphaNum($field, $value, $parameters)
    {
        if (is_null($value) || $value === '') {
            return true;
        }

        return preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/', $value);
    }

    /**
     * Validar URL
     */
    protected function validateUrl($field, $value, $parameters)
    {
        if (is_null($value) || $value === '') {
            return true;
        }

        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validar confirmación
     */
    protected function validateConfirmed($field, $value, $parameters)
    {
        $confirmationField = $field . '_confirmation';
        $confirmationValue = $this->getValue($confirmationField);

        return $value === $confirmationValue;
    }

    /**
     * Validar que esté en lista
     */
    protected function validateIn($field, $value, $parameters)
    {
        if (is_null($value) || $value === '') {
            return true;
        }

        return in_array($value, $parameters);
    }

    /**
     * Validar que NO esté en lista
     */
    protected function validateNotIn($field, $value, $parameters)
    {
        if (is_null($value) || $value === '') {
            return true;
        }

        return !in_array($value, $parameters);
    }

    /**
     * Validar expresión regular
     */
    protected function validateRegex($field, $value, $parameters)
    {
        if (is_null($value) || $value === '') {
            return true;
        }

        return preg_match($parameters[0], $value);
    }

    /**
     * Validar fecha
     */
    protected function validateDate($field, $value, $parameters)
    {
        if (is_null($value) || $value === '') {
            return true;
        }

        return strtotime($value) !== false;
    }

    /**
     * Validar entre valores
     */
    protected function validateBetween($field, $value, $parameters)
    {
        if (is_null($value) || $value === '') {
            return true;
        }

        $min = $parameters[0];
        $max = $parameters[1];

        if (is_numeric($value)) {
            return $value >= $min && $value <= $max;
        }

        $length = mb_strlen($value);
        return $length >= $min && $length <= $max;
    }

    /**
     * Validar que sea igual a otro campo
     */
    protected function validateSame($field, $value, $parameters)
    {
        $otherField = $parameters[0];
        $otherValue = $this->getValue($otherField);

        return $value === $otherValue;
    }

    /**
     * Validar que sea diferente a otro campo
     */
    protected function validateDifferent($field, $value, $parameters)
    {
        $otherField = $parameters[0];
        $otherValue = $this->getValue($otherField);

        return $value !== $otherValue;
    }

    /**
     * Obtener errores
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * Verificar si hay errores
     */
    public function fails()
    {
        return !empty($this->errors);
    }

    /**
     * Obtener primer error de un campo
     */
    public function first($field)
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Obtener todos los errores como array plano
     */
    public function all()
    {
        $all = [];
        foreach ($this->errors as $field => $errors) {
            $all = array_merge($all, $errors);
        }
        return $all;
    }

    /**
     * Crear validador estático
     */
    public static function make(array $data, array $rules, array $messages = [])
    {
        return new static($data, $rules, $messages);
    }
}