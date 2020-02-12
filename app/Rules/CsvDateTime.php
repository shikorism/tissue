<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * CSVインポート機能の日時バリデーションルール
 * @package App\Rules
 */
class CsvDateTime implements Rule
{
    const VALID_FORMATS = [
        'Y/m/d H:i:s',
        'Y/n/j G:i:s',
        'Y/m/d H:i',
        'Y/n/j G:i',
    ];

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
        // この辺の実装の元ネタは、LaravelのValidatesAttributes#validateDateFormat()

        if (!is_string($value)) {
            return false;
        }

        foreach (self::VALID_FORMATS as $format) {
            $date = \DateTime::createFromFormat('!' . $format, $value);

            if ($date && $date->format($format) === $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute の形式は "年/月/日 時:分" にしてください。';
    }
}
