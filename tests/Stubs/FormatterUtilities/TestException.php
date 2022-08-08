<?php

declare(strict_types=1);

namespace Tests\Stubs\FormatterUtilities;

use Exception;

/**
 * This class is not correctly formatted nor follow any coding standard.
 * This is intentional to reproduce the generated classes with no type hinting
 */
class TestException extends Exception
{
    private $errorResponse;
    private $trace;

    /**
     * @param TestErrorResponse $errorResponse
     */
    public function __construct(TestErrorResponse $errorResponse)
    {
        parent::__construct('Test Exception Message', 409);
        $this->errorResponse = $errorResponse;
        $this->trace = ['do not show this'];
    }

    /**
     * @return TestErrorResponse
     */
    public function getErrorResponse()
    {
        return $this->errorResponse;
    }

    /**
     * @return mixed
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param mixed $reference
     */
    public function setReference($reference): void
    {
        $this->reference = $reference;
    }
}
