<?php

declare(strict_types=1);

namespace Tests\Stubs\FormatterUtilities;

use Exception;

class AnotherTestException extends Exception
{
    private SampleClass $reference;

    /**
     * @inheritDoc
     */
    public function __construct(SampleClass $reference)
    {
        parent::__construct('Test Exception Message', 409);
        $this->reference = $reference;
    }

    /**
     * @return string|SampleClass
     */
    public function getReference(): SampleClass|string
    {
        return $this->reference;
    }

    /**
     * @param string|SampleClass $reference
     */
    public function setReference(SampleClass|string $reference): void
    {
        $this->reference = $reference;
    }
}
