<?php

declare(strict_types=1);

namespace Tests\FormatterUtilities;

use Datenkraft\MonologFormatter\FormatterUtilities\ObjectToArrayTransformer;
use Generator;
use Tests\Stubs\FormatterUtilities\AnotherTestException;
use Tests\Stubs\FormatterUtilities\SampleClass;
use Tests\Stubs\FormatterUtilities\TestErrorResponse;
use Tests\Stubs\FormatterUtilities\TestErrorWithoutProperties;
use Tests\Stubs\FormatterUtilities\TestErrorWithProperties;
use Tests\Stubs\FormatterUtilities\TestException;
use Tests\TestCase;

/**
 * @coversDefaultClass \Datenkraft\MonologFormatter\FormatterUtilities\ObjectToArrayTransformer
 */
class ObjectToArrayTransformerTest extends TestCase
{
    /**
     * @covers ::convertRecord
     * @covers ::arrayPropertyToArray
     * @covers ::objectToArray
     * @return void
     */
    public function testConvertRecord(): void
    {
        $object = new ObjectToArrayTransformer();
        $errorResponse = new TestErrorResponse();
        $errorResponse->setErrors(
            $this->objectProviderErrorArray(
                [
                    ['code' => 'EXAMPLE_CODE', 'message' => 'This is an example error message.'],
                ]
            )
        );
        $exception = new TestException($errorResponse);
        $record = ['context' => ['exception' => $exception]];
        $object->convertRecord($record);
        $expected = [
            'context' => [
                'exception' => [
                    'exception' => $exception,
                    'exceptionData' => [
                        'errorResponse' => [
                            'errors' => [
                                [
                                    'code' => 'EXAMPLE_CODE',
                                    'message' => 'This is an example error message.',
                                    'testArray' => ['test', 'test2'],
                                ],
                            ]
                        ],
                        'message' => 'Test Exception Message',
                        'code' => 409,
                        'file' => __FILE__,
                        'line' => 39,
                    ],
                ],
            ],
        ];

        $this->assertSame($expected, $record);
    }

    /**
     * @dataProvider dataProviderTestObjectToArray
     * @covers ::objectToArray
     * @covers ::arrayPropertyToArray
     * @param array $errors
     * @param array $expected
     * @return void
     */
    public function testObjectToArray(array $errors, array $expected): void
    {
        $errorResponse = new TestErrorResponse();
        $errorResponse->setErrors($this->objectProviderErrorArray($errors));
        $exception = new TestException($errorResponse);
        $expected['exception'] = $exception;
        $this->assertSame(
            $expected,
            (new ObjectToArrayTransformer())->objectToArray($exception)
        );
    }

    /**
     * Provides an array of objects
     * @param array $errors
     * @return array
     */
    public function objectProviderErrorArray(array $errors): array
    {
        $errorArray = [];
        foreach ($errors as $error) {
            if (isset($error['code']) && isset($error['message'])) {
                $newError = new TestErrorWithProperties();
                $newError->setCode($error['code']);
                $newError->setMessage($error['message']);
            } else {
                $newError = new TestErrorWithoutProperties();
            }
            $errorArray[] = $newError;
        }
        return $errorArray;
    }

    /**
     * @return Generator
     */
    public function dataProviderTestObjectToArray(): Generator
    {
        yield (
        [
            [
                ['code' => 'EXAMPLE_CODE', 'message' => 'This is an example error message.'],
            ],
            [
                'exception' => null,
                'exceptionData' => [
                    'errorResponse' => [
                        'errors' => [
                            [
                                'code' => 'EXAMPLE_CODE',
                                'message' => 'This is an example error message.',
                                'testArray' => ['test', 'test2'],
                            ],
                        ]
                    ],
                    'message' => 'Test Exception Message',
                    'code' => 409,
                    'file' => __FILE__,
                    'line' => 80,
                ],
            ],

        ]);
        yield (
        [
            [
                ['code' => 'EXAMPLE_CODE', 'message' => 'This is an example error message.'],
                ['code' => 'EXAMPLE_CODE', 'message' => 'This is a second example error message.'],
                ['code' => 'EXAMPLE_CODE', 'message' => 'This is a third example error message.'],
                [],
            ],
            [

                'exception' => null,
                'exceptionData' => [
                    'errorResponse' => [
                        'errors' => [
                            [
                                'code' => 'EXAMPLE_CODE',
                                'message' => 'This is an example error message.',
                                'testArray' => ['test', 'test2'],
                            ],
                            [
                                'code' => 'EXAMPLE_CODE',
                                'message' => 'This is a second example error message.',
                                'testArray' => ['test', 'test2'],
                            ],
                            [
                                'code' => 'EXAMPLE_CODE',
                                'message' => 'This is a third example error message.',
                                'testArray' => ['test', 'test2'],
                            ],
                            [],
                        ]
                    ],
                    'message' => 'Test Exception Message',
                    'code' => 409,
                    'file' => __FILE__,
                    'line' => 80,
                ],
            ],

        ]);
    }

    /**
     * @covers ::arrayPropertyToArray
     * @return void
     */
    public function testArrayPropertyToArray(): void
    {
        $this->assertSame(
            ['abc', ['def', 'ghi'], 'jkl'],
            (new ObjectToArrayTransformer())->arrayPropertyToArray(['abc', ['def', 'ghi'], 'jkl'])
        );
    }

    /**
     * @covers ::convertRecord
     * @covers ::arrayPropertyToArray
     * @covers ::objectToArray
     * @return void
     */
    public function testConvertRecordEndlessLoopPrevention(): void
    {
        $object = new ObjectToArrayTransformer();
        $testA = new SampleClass();
        $testB = new SampleClass();
        $testA->setReference($testB);
        $testB->setReference($testA);
        $exception = new AnotherTestException($testA);
        $record = ['context' => ['exception' => $exception]];
        $object->convertRecord($record);
        $expected = [
            'context' => [
                'exception' => [
                    'exception' => $exception,
                    'exceptionData' => [
                        'reference' => [
                            'reference' => [
                                'reference' => 'Already converted object'
                            ]
                        ],
                        'message' => 'Test Exception Message',
                        'code' => 409,
                        'file' => __FILE__,
                        'line' => 206
                    ]
                ]
            ]
        ];
        $this->assertSame($expected, $record);
    }

    /**
     * @covers ::scanForObjectsAndConvert
     * @return void
     */
    public function testScanForObjectsAndConvert(): void
    {
        $class1 = new SampleClass();
        $class2 = new SampleClass();
        $arr = [
            [$class1],
            $class2,
        ];
        $expected = [
            [[]],
            [],
        ];
        $object = $this->getMockBuilder(ObjectToArrayTransformer::class)
            ->onlyMethods(['objectToArray'])
            ->getMock();
        $object
            ->expects($this->once())
            ->method('')
            ->withConsecutive([$class1], [$class2])
            ->willReturnOnConsecutiveCalls([], []);

        $this->assertSame($expected, $object->scanForObjectsAndConvert($arr));
    }
}
