<?php

declare(strict_types=1);

namespace Tests;

use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

/**
 * Class TestCase
 * @package Tests
 */
abstract class TestCase extends \Monolog\Test\TestCase
{
    /**
     * @param callable $method
     * @param array|null $args
     * @return mixed
     * @throws ReflectionException
     * @noinspection PhpMissingParamTypeInspection
     */
    public function invokeMethod($method, array $args = null): mixed
    {
        list($object, $methodName) = $method;
        $reflection = new ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $args ?? []);
    }

    /**
     * @return array
     */
    public function dataProviderTrueFalse(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @param $object
     * @param string $property
     * @param mixed $value
     * @throws Exception
     */
    public function setProperty($object, string $property, mixed $value): void
    {
        try {
            $reflectionProperty = $this->getReflectionProperty($object, $property);
            $reflectionProperty->setValue($object, $value);
        } catch (Exception $e) {
            throw new Exception(
                "Unable to set property $property of object type " . get_class($object) .
                ': ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * @param $object
     * @param $property
     * @return ReflectionProperty
     * @throws ReflectionException
     */
    protected function getReflectionProperty($object, $property): ReflectionProperty
    {
        $reflection = new ReflectionClass($object);
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty;
    }

    /**
     * Can be used to split the values of an array into the format
     * as required for {@see \PHPUnit\Framework\MockObject\Builder\InvocationMocker::withConsecutive}
     * Usage: ->withConsecutive(...$this->splitForWithConsecutive($params))
     * @param array $params
     * @return array
     */
    protected function splitForWithConsecutive(array $params): array
    {
        return array_map(fn($param) => [$param], $params);
    }
}
