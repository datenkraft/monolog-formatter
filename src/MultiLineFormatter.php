<?php

declare(strict_types=1);

namespace Datenkraft\MonologFormatter;

use Datenkraft\MonologFormatter\FormatterUtilities\ObjectToArrayTransformer;
use Monolog\Formatter\JsonFormatter;
use Monolog\LogRecord;

/**
 * This formatter can be used for local logging
 */
class MultiLineFormatter extends JsonFormatter
{
    /**
     * @param self::BATCH_MODE_* $batchMode
     *
     * @throws \RuntimeException If the function json_encode does not exist
     */
    public function __construct(
        int $batchMode = self::BATCH_MODE_JSON,
        bool $appendNewline = true,
        bool $ignoreEmptyContextAndExtra = false,
        bool $includeStacktraces = false
    ) {
        parent::__construct($batchMode, $appendNewline, $ignoreEmptyContextAndExtra, $includeStacktraces);
        $this->addJsonEncodeOption(JSON_PRETTY_PRINT);
    }

    /**
     * @inheritDoc
     */
    public function format(LogRecord $record): string
    {
        $transformer = new ObjectToArrayTransformer();
        $transformer->convertRecord($record);
        return date('c') . ': ' . parent::format($record);
    }
}
