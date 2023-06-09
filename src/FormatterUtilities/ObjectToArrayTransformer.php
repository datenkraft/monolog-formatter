<?php

declare(strict_types=1);

namespace Datenkraft\MonologFormatter\FormatterUtilities;

use Exception;
use Monolog\LogRecord;
use ReflectionClass;

/**
 * Service class for transforming e.g. generated exceptions from PHP Jane - in general any object
 * Class ObjectToArrayTransformer
 */
class ObjectToArrayTransformer
{
    private array $convertedObjects;

    /**
     * Convert a record which is given to a monolog formatter
     * @param LogRecord $record
     * @return void
     */
    public function convertRecord(LogRecord &$record): void
    {
        if (isset($record->context) && is_array($record->context)) {
            $context = $this->scanForObjectsAndConvert($record->context);
            $record = new LogRecord(
                $record->datetime,
                $record->channel,
                $record->level,
                $record->message,
                $context,
                $record->extra,
                $record->formatted
            );
        }
    }

    /**
     * Scans an array recursive for objects
     * @param array $array
     * @return array
     */
    public function scanForObjectsAndConvert(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_object($value)) {
                $array[$key] = $this->objectToArray($value);
            } elseif (is_array($value)) {
                $array[$key] = $this->scanForObjectsAndConvert($value);
            }
        }
        return $array;
    }

    /**
     * Recursive serialization for any object
     * NOTICE: This method will reflect all properties and include them in the return value!
     * @param object $obj
     * @return array
     */
    public function objectToArray(object $obj): array
    {
        $this->convertedObjects[] = spl_object_hash($obj);
        $dataArray = $obj instanceof Exception ? ['exception' => $obj] : [];
        $propertyArray = [];
        $reflection = new ReflectionClass($obj);
        do {
            foreach ($reflection->getProperties() as $property) {
                $property->setAccessible(true);
                // uninitialized properties and stack traces from exceptions are ignored
                if (!$property->isInitialized($obj)) {
                    continue;
                }
                if (isset($dataArray['exception']) && $property->getName() === 'trace') {
                    continue;
                }
                // here would be the place to add conditions for blacklisting properties

                // avoid overwriting with value from parent classes
                if (!isset($propertyArray[$property->getName()])) {
                    $propertyArray[$property->getName()] = $property->getValue($obj);
                }
            }
        } while ($reflection = $reflection->getParentClass());

        if (isset($dataArray['exception'])) {
            $dataArray['exceptionData'] = $this->arrayPropertyToArray($propertyArray);
        } else {
            $dataArray = $this->arrayPropertyToArray($propertyArray);
        }
        return $dataArray;
    }

    /**
     * Loop through given array and convert content if necessary recursively
     * @param array $input
     * @return array
     */
    public function arrayPropertyToArray(array $input): array
    {
        $returnArray = [];
        foreach ($input as $key => $item) {
            if (is_object($item)) {
                $returnArray[$key] = in_array(
                    spl_object_hash($item),
                    $this->convertedObjects
                ) ? 'Already converted object' : $this->objectToArray($item);
            } elseif (is_array($item)) {
                $returnArray[$key] = $this->arrayPropertyToArray($item);
            } else {
                $returnArray[$key] = $item;
            }
        }
        return $returnArray;
    }
}
