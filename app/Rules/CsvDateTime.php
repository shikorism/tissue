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

    const MINIMUM_TIMESTAMP = 946652400; // 2000-01-01 00:00:00 JST
    const MAXIMUM_TIMESTAMP = 4102412399; // 2099-12-31 23:59:59 JST

    /** @var string Validation error message */
    private $message = ':attributeの形式は "年/月/日 時:分" にしてください。';

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
            if (!$date) {
                continue;
            }

            $timestamp = (int) $date->format('U');
            if ($timestamp < self::MINIMUM_TIMESTAMP || self::MAXIMUM_TIMESTAMP < $timestamp) {
                $this->message = ':attributeは 2000/01/01 00:00 〜 2099/12/31 23:59 の間のみ対応しています。';

                return false;
            }

            $formatted = $date->format($format);
            if ($formatted === $value) {
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
        return $this->message;
    }
}
