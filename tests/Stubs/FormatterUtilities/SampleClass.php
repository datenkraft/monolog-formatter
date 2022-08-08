<?php

declare(strict_types=1);

namespace Tests\Stubs\FormatterUtilities;

class SampleClass
{
    private SampleClass $reference;

    /**
     * @return SampleClass
     */
    public function getReference(): SampleClass
    {
        return $this->reference;
    }

    /**
     * @param SampleClass $reference
     */
    public function setReference(SampleClass $reference): void
    {
        $this->reference = $reference;
    }
}
