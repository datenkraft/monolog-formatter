<?php

declare(strict_types=1);

namespace Datenkraft\MonologFormatter;

use Datenkraft\MonologFormatter\FormatterUtilities\ObjectToArrayTransformer;
use Monolog\Formatter\JsonFormatter;

/**
 * This formatter can be used for local logging
 */
class MultiLineFormatter extends JsonFormatter
{
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
    public function format(array $record): string
    {
        $transformer = new ObjectToArrayTransformer();
        $transformer->convertRecord($record);
        return date('c') . ': ' . parent::format($record);
    }
}
