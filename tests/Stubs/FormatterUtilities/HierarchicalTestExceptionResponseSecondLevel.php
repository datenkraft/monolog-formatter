<?php

declare(strict_types=1);

namespace Tests\Stubs\FormatterUtilities;

class HierarchicalTestExceptionResponseSecondLevel extends HierarchicalTestExceptionResponseFirstLevel
{
    /** @var string|null */
    private ?string $requestId = 'requestId';
}
