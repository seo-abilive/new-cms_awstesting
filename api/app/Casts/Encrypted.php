<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Crypt;

class Encrypted implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        if ($value === null) return null;

        return Crypt::decryptString($value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if ($value === null) return null;

        return Crypt::encryptString($value);
    }
}
