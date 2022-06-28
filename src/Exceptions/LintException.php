<?php

namespace Lukasss93\Larex\Exceptions;

use Exception;
use Throwable;

class LintException extends Exception
{
    /** @var string[] */
    protected array $errors;

    public function __construct(string $message = '', array $errors = [], int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    /**
     * Get errors.
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
