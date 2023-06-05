<?php

declare(strict_types=1);

namespace Tests\Stubs\FormatterUtilities;

use Exception;
use Throwable;

class HierarchicalTestException extends Exception
{
    private ?HierarchicalTestExceptionResponseFirstLevel $response;

    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param HierarchicalTestExceptionResponseFirstLevel|null $response
     */
    public function __construct(
        string $message = 'An API error occured',
        int $code = 0,
        Throwable $previous = null,
        HierarchicalTestExceptionResponseFirstLevel $response = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->setResponse($response);
    }

    /**
     * @return HierarchicalTestExceptionResponseFirstLevel|null
     */
    public function getResponse(): ?HierarchicalTestExceptionResponseFirstLevel
    {
        return $this->response;
    }

    /**
     * @param HierarchicalTestExceptionResponseFirstLevel|null $response
     * @return $this
     */
    public function setResponse(?HierarchicalTestExceptionResponseFirstLevel $response): self
    {
        $this->response = $response;
        return $this;
    }
}
