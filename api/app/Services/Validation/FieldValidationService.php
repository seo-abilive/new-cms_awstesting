<?php

namespace App\Services\Validation;

use App\Mod\ContentField\Domain\Models\ContentField;

class FieldValidationService
{
    /**
     * フィールドのバリデーションルールを追加
     */
    public function addValidationRules(
        ContentField $field,
        array &$rules,
        array &$message,
        ?string $validationKey = null,
        ?array $requestData = []
    ): void {
        $fieldId = $field->field_id;
        $fieldName = $field->name ?? $fieldId;
        $fieldType = $field->field_type;

        switch ($fieldType) {
            case 'custom_field':
                $customFieldFields = $field->customField->fields ?? collect([]);
                foreach ($customFieldFields as $childField) {
                    $prefix = $validationKey ? "{$validationKey}" : "{$fieldId}";
                    $this->addValidationRules($childField, $rules, $message, "{$prefix}.{$childField->field_id}.value");
                }
                break;

            case 'custom_block':
                if (!empty($requestData[$fieldId])) {
                    foreach ($requestData[$fieldId] ?? [] as $key => $value) {
                        $childField = ContentField::find($value['field_id']);
                        if ($childField) {
                            $this->addValidationRules($childField, $rules, $message, "{$fieldId}.{$key}.values.{$childField->field_id}");
                        }
                    }
                }
                break;

            default:
                $validationKey = $validationKey ?? $fieldId;
                $fieldRules = [];

                $isRequired = $field->is_required || $field->is_required === 1;
                if (!empty($field->is_required)) {
                    $fieldRules[] = 'required';
                    $message["{$validationKey}.required"] = "{$fieldName}は必須項目です。";
                }

                if (!empty($field->validates)) {
                    foreach ($field->validates as $validate) {
                        $type = $validate['type'] ?? '';
                        $pattern = $validate['pattern'] ?? '';
                        $errorMessage = $validate['error_message'] ?? '';

                        switch ($type) {
                            case 'email':
                                if (!$isRequired) {
                                    $fieldRules[] = 'nullable';
                                    $fieldRules[] = 'sometimes';
                                }
                                $fieldRules[] = 'email';
                                $message["{$validationKey}.email"] = $errorMessage;
                                break;
                            case 'phone':
                                if (!$isRequired) {
                                    $fieldRules[] = 'nullable';
                                    $fieldRules[] = 'sometimes';
                                }
                                $fieldRules[] = 'phone';
                                $message["{$validationKey}.phone"] = $errorMessage;
                                break;
                            case 'custom':
                                $patternWithDelimiter = $this->addDelimiterToPattern($pattern);
                                if (!$isRequired) {
                                    $fieldRules[] = 'nullable';
                                    $fieldRules[] = 'sometimes';
                                }
                                $fieldRules[] = 'regex:' . $patternWithDelimiter;
                                $message["{$validationKey}.regex"] = $errorMessage;
                                break;
                        }
                    }
                }

                if (!empty($fieldRules)) {
                    $rules[$validationKey] = $fieldRules;
                }
                break;
        }
    }

    protected function addDelimiterToPattern(string $pattern): string
    {
        $delimiter = '~';
        if (empty($pattern)) return $pattern;

        $delimiters = ['/', '#', '~', '%', '@'];
        foreach ($delimiters as $d) {
            if (strlen($pattern) >= 2 && $pattern[0] === $d && substr($pattern, -1) === $d) {
                return $pattern;
            }
        }

        return $delimiter . $pattern . $delimiter;
    }
}
