<?php

namespace App\Parser\SearchQuery;

use App\Rules\CsvDateTime;

class Expression
{
    public const VALID_TARGETS = [
        'date',
        'since',
        'until',
        'link',
        'url',
        'note',
        'tag',
        'user',
        'is', // -> boolean target
    ];

    public const BOOLEAN_TARGETS = [
        'sensitive',
    ];

    public const DATE_TARGETS = [
        'date',
        'since',
        'until',
    ];

    private const TARGET_VALIDATORS = [
        'date' => 'validateDateTarget',
        'since' => 'validateDateTarget',
        'until' => 'validateDateTarget',
        'is' => 'validateBooleanTarget',
    ];

    /** @var bool */
    public $negative = false;

    /** @var string|null */
    public $target = null;

    /** @var string|null */
    public $keyword = null;

    /**
     * @throws InvalidExpressionException
     */
    public function validate()
    {
        if (array_search($this->target, self::VALID_TARGETS, true) === false) {
            throw new InvalidExpressionException("Target `{$this->target}` is invalid.");
        }

        if (isset(self::TARGET_VALIDATORS[$this->target])) {
            $this->{self::TARGET_VALIDATORS[$this->target]}();
        }
    }

    private function validateDateTarget()
    {
        foreach (['Y-m-d', 'Y/m/d', 'Y-n-j', 'Y/n/j'] as $format) {
            $date = \DateTime::createFromFormat('!' . $format, $this->keyword);
            if (!$date) {
                continue;
            }

            $timestamp = (int) $date->format('U');
            if ($timestamp < CsvDateTime::MINIMUM_TIMESTAMP || CsvDateTime::MAXIMUM_TIMESTAMP < $timestamp) {
                throw new InvalidExpressionException("Date target `{$this->keyword}` is out of range.");
            }

            $formatted = $date->format($format);
            if ($formatted === $this->keyword) {
                return;
            }
        }

        throw new InvalidExpressionException("Date target `{$this->keyword}` is invalid format.");
    }

    private function validateBooleanTarget()
    {
        if (array_search($this->keyword, self::BOOLEAN_TARGETS, true) === false) {
            throw new InvalidExpressionException("Boolean target `{$this->keyword}` is invalid.");
        }
    }
}
