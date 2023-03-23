<?php

declare(strict_types=1);

namespace Tests\Stubs\FormatterUtilities;

class HierarchicalTestExceptionResponseThirdLevel extends HierarchicalTestExceptionResponseSecondLevel
{
    /** @var object|null */
    private ?object $pageInfo = null;
}
