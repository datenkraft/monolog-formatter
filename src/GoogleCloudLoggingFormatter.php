<?php

declare(strict_types=1);

namespace Datenkraft\MonologFormatter;

use Datenkraft\MonologFormatter\FormatterUtilities\ObjectToArrayTransformer;
use DateTimeInterface;
use Monolog\Formatter\JsonFormatter;
use Monolog\LogRecord;

/**
 * This formatter is optimized for Google Cloud Logging
 * Based on https://github.com/Seldaek/monolog/blob/main/src/Monolog/Formatter/GoogleCloudLoggingFormatter.php
 */
class GoogleCloudLoggingFormatter extends JsonFormatter
{
    protected function normalizeRecord(LogRecord $record): array
    {
        $normalized = parent::normalizeRecord($record);

        // Re-key level for GCP logging
        $normalized['severity'] = $normalized['level_name'];
        $normalized['time'] = $record->datetime->format(DateTimeInterface::RFC3339_EXTENDED);

        // Remove keys that are not used by GCP
        unset($normalized['level'], $normalized['level_name'], $normalized['datetime']);

        $transformer = new ObjectToArrayTransformer();
        $normalized['context'] = $transformer->convertContext($record->context);

        return $normalized;
    }
}
