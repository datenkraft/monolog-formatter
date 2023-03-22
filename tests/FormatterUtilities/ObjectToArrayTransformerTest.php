<?php

declare(strict_types=1);

namespace Tests\FormatterUtilities;

use Datenkraft\MonologFormatter\FormatterUtilities\ObjectToArrayTransformer;
use Generator;
use Tests\Stubs\FormatterUtilities\AnotherTestException;
use Tests\Stubs\FormatterUtilities\HierarchicalTestException;
use Tests\Stubs\FormatterUtilities\HierarchicalTestExceptionResponseThirdLevel;
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
    protected ObjectToArrayTransformer $object;

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
                        'line' => 43,
                        'string' => '',
                        'previous' => null,
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
                    'line' => 86,
                    'string' => '',
                    'previous' => null,
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
                    'line' => 86,
                    'string' => '',
                    'previous' => null,
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
                        'line' => 216,
                        'string' => '',
                        'previous' => null,
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
            ->expects($this->exactly(2))
            ->method('objectToArray')
            ->withConsecutive([$class1], [$class2])
            ->willReturnOnConsecutiveCalls([], []);

        $this->assertSame($expected, $object->scanForObjectsAndConvert($arr));
    }

    /**
     * @return void
     */
    public function testHierarchicalExceptionProperties(): void
    {
        $response = new HierarchicalTestExceptionResponseThirdLevel();
        $exception = new HierarchicalTestException('Example message of hierarchical exceptions', response: $response);
        $this->object = new ObjectToArrayTransformer();
        $expected = [
            'exception' => $exception,
            'exceptionData' => [
                'response' => [
                    'pageInfo' => null,
                    'requestId' => 'requestId',
                    'reasonPhrase' => 'reasonPhrase',
                    'statusCode' => 400,
                ],
                'message' => 'Example message of hierarchical exceptions',
                'code' => 0,
                'file' => '/home/app/gg-monolog-formatter/tests/FormatterUtilities/ObjectToArrayTransformerTest.php',
                'line' => 276,
                'string' => '',
                'previous' => null,
            ]
        ];
        $this->assertSame($expected, $this->object->objectToArray($exception));
    }
}
