<?php

declare(strict_types=1);

namespace Tests\Stubs\FormatterUtilities;

/**
 * This class is not correctly formatted nor follow any coding standard.
 * This is intentional to reproduce the generated classes with no type hinting
 */
class TestErrorWithProperties
{
    /**
     * Code
     *
     * @var string
     */
    protected $code;
    /**
     * Message
     *
     * @var string
     */
    protected $message;

    protected $testArray;

    public function __construct()
    {
        $this->testArray = ['test', 'test2'];
    }


    /**
     * Code
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }
    /**
     * Code
     *
     * @param string $code
     *
     * @return self
     */
    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }
    /**
     * Message
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
    /**
     * Message
     *
     * @param string $message
     *
     * @return self
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }
}
