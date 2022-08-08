<?php

declare(strict_types=1);

namespace Tests\Stubs\FormatterUtilities;

/**
 * This class is not correctly formatted nor follow any coding standard.
 * This is intentional to reproduce the generated classes with no type hinting
 */
class TestErrorResponse
{
    /**
     * errors
     *
     */
    protected $errors;
    /**
     * errors
     *
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * errors
     *
     * @param array $errors
     * @return self
     */
    public function setErrors(array $errors): self
    {
        $this->errors = $errors;
        return $this;
    }
}
