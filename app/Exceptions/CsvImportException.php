<?php

namespace App\Exceptions;

use Throwable;

class CsvImportException extends \RuntimeException
{
    /** @var string[] */
    private $errors;

    /**
     * CsvImportException constructor.
     * @param string[] $errors
     */
    public function __construct(...$errors)
    {
        parent::__construct(array_first($errors));
        $this->errors = $errors;
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
