<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class DynamicContentValueCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $fieldType = $model->field->field_type ?? null;

        return match ($fieldType) {
            'date'      => $value ? Carbon::parse($value) : null,
            'checkbox'  => json_decode($value, true) ?? [],
            'list'      => json_decode($value, true) ?? [],
            'table'      => json_decode($value, true) ?? [],
            'media_image_multi' => json_decode($value, true) ?? [],
            'switch'    => (bool) $value,
            'custom_field' => \json_decode($value, true) ?? [],
            default     => $value,
        };
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $fieldType = $model->field->field_type ?? null;

        return match ($fieldType) {
            'date'      => $value instanceof \DateTimeInterface
                            ? $value->format('Y-m-d H:i:s')
                            : $value,
            'checkbox'  => json_encode($value),
            'list'      => json_encode($value),
            'table'      => json_encode($value),
            'media_image_multi' => json_encode(array_values(is_array($value) ? $value : [])),
            'switch'    => $value ? 1 : 0,
            'custom_field' => \json_encode($value),
            default     => $value,
        };
    }
}
