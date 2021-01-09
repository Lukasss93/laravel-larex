<?php

namespace Lukasss93\Larex\Exceptions;

use Exception;
use Throwable;

class LintException extends Exception
{
    private $errors;

    public function __construct($message = "", $errors = [], $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    /**
     * Get errors
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}