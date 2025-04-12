<?php
namespace System\Validation;

use System\Database;

/**
 * Sistema de validación de formularios
 * 
 * Proporciona una forma flexible y extensible de validar datos de entrada
 * con soporte para múltiples reglas de validación y mensajes personalizados.
 */
class Validator {
    private array $data;
    private array $rules;
    private array $messages;
    private array $errors = [];
    private $db = null;

    /**
     * @param array $data Datos a validar
     * @param array $rules Reglas de validación
     * @param array $messages Mensajes de error personalizados
     */
    public function __construct(array $data, array $rules, array $messages = []) {
        $this->data = $data;
        $this->rules = $rules;
        $this->messages = $messages;
        $this->db = Database::getConnection();
    }

    /**
     * Ejecuta la validación
     * 
     * @return bool true si la validación es exitosa, false si hay errores
     */
    public function validate(): bool {
        foreach ($this->rules as $field => $fieldRules) {
            $rules = explode('|', $fieldRules);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                if (str_contains($rule, ':')) {
                    [$ruleName, $parameter] = explode(':', $rule, 2);
                } else {
                    $ruleName = $rule;
                    $parameter = null;
                }

                $method = 'validate' . ucfirst($ruleName);
                if (method_exists($this, $method)) {
                    if (!$this->$method($field, $value, $parameter)) {
                        $this->addError($field, $ruleName, $parameter);
                    }
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * Obtiene los errores de validación
     */
    public function getErrors(): array {
        return $this->errors;
    }

    /**
     * Valida que un campo sea requerido
     */
    protected function validateRequired(string $field, $value, $parameter = null): bool {
        if (is_array($value)) {
            return count($value) > 0;
        }
        return $value !== null && $value !== '';
    }

    /**
     * Valida que un campo sea un email válido
     */
    protected function validateEmail(string $field, $value, $parameter = null): bool {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valida la longitud mínima de un campo
     */
    protected function validateMin(string $field, $value, $parameter): bool {
        return mb_strlen($value) >= (int)$parameter;
    }

    /**
     * Valida la longitud máxima de un campo
     */
    protected function validateMax(string $field, $value, $parameter): bool {
        return mb_strlen($value) <= (int)$parameter;
    }

    /**
     * Valida que un campo sea único en la base de datos
     */
    protected function validateUnique(string $field, $value, $parameter): bool {
        [$table, $column] = explode(',', $parameter);
        $sql = "SELECT COUNT(*) FROM $table WHERE $column = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$value]);
        $result = $stmt->fetchColumn();

        return (int)$result === 0;
    }

    /**
     * Valida que un campo coincida con otro
     */
    protected function validateConfirmed(string $field, $value, $parameter = null): bool {
        return $value === ($this->data[$field . '_confirmation'] ?? null);
    }

    /**
     * Agrega un error de validación
     */
    protected function addError(string $field, string $rule, ?string $parameter): void {
        $message = $this->messages["$field.$rule"] ?? $this->getDefaultMessage($field, $rule, $parameter);
        $this->errors[$field][] = $message;
    }

    /**
     * Obtiene el mensaje de error predeterminado para una regla
     */
    protected function getDefaultMessage(string $field, string $rule, ?string $parameter): string {
        $fieldName = ucfirst(str_replace('_', ' ', $field));

        return match ($rule) {
            'required' => "$fieldName es requerido",
            'email' => "$fieldName debe ser un email válido",
            'min' => "$fieldName debe tener al menos $parameter caracteres",
            'max' => "$fieldName no debe exceder $parameter caracteres",
            'unique' => "$fieldName ya está en uso",
            'confirmed' => "$fieldName no coincide con la confirmación",
            default => "$fieldName es inválido"
        };
    }
}