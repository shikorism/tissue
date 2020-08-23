<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FuzzyBoolean implements Rule
{
    public static function isTruthy($value): bool
    {
        if ($value === 1 || $value === '1') {
            return true;
        }

        $lower = strtolower((string)$value);

        return $lower === 'true';
    }

    public static function isFalsy($value): bool
    {
        if ($value === null || $value === '' || $value === 0 || $value === '0') {
            return true;
        }

        $lower = strtolower((string)$value);

        return $lower === 'false';
    }

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return self::isTruthy($value) || self::isFalsy($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.boolean');
    }
}
