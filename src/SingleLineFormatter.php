<?php

declare(strict_types=1);

namespace Datenkraft\MonologFormatter;

use Datenkraft\MonologFormatter\FormatterUtilities\ObjectToArrayTransformer;
use Datenkraft\MonologGkeFormatter\GkeFormatter;

/**
 * This formatter is optimized for Google Cloud Logging
 */
class SingleLineFormatter extends GkeFormatter
{
    /**
     * @inheritDoc
     */
    public function format(array $record): string
    {
        $transformer = new ObjectToArrayTransformer();
        $transformer->convertRecord($record);
        return parent::format($record);
    }
}
