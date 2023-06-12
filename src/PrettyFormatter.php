<?php

declare(strict_types=1);

namespace Datenkraft\MonologFormatter;

use Datenkraft\MonologFormatter\FormatterUtilities\ObjectToArrayTransformer;
use Monolog\Formatter\JsonFormatter;
use Monolog\LogRecord;

/**
 * This formatter can be used for local logging
 */
class PrettyFormatter extends JsonFormatter
{
    protected function normalizeRecord(LogRecord $record): array
    {
        $this->addJsonEncodeOption(JSON_PRETTY_PRINT);
        $normalized = parent::normalizeRecord($record);

        $transformer = new ObjectToArrayTransformer();
        $normalized['context'] = $transformer->convertContext($record->context);

        return $normalized;
    }

    /**
     * @inheritDoc
     */
    public function format(LogRecord $record): string
    {
        return date('c') . ': ' . parent::format($record);
    }
}
