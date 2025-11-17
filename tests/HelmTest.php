<?php

declare(strict_types=1);

namespace Mammatus\Tests\Kubernetes;

use Mammatus\Kubernetes\Events\Helm\Values;
use Mammatus\Kubernetes\Helm;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\AsyncTestUtilities\AsyncTestCase;
use WyriHaximus\Broadcast\ArrayListenerProvider;
use WyriHaximus\Broadcast\Dispatcher;

final class HelmTest extends AsyncTestCase
{
    /** @param array<string, array<int, callable>> $listeners */
    #[Test]
    #[DataProvider('valuesProvider')]
    public function values(string $expectedOutput, array $listeners): void
    {
        self::expectOutputString($expectedOutput);

        $dispatcher = Dispatcher::createFromListenerProvider(new ArrayListenerProvider($listeners));

        $exitCode = new Helm($dispatcher)->json();

        self::assertSame(0, $exitCode->value);
    }

    /** @return iterable<array<string|array<class-string, array<callable>>>> */
    public static function valuesProvider(): iterable
    {
        yield 'nothing' => [
            '[]',
            [],
        ];

        yield 'one' => [
            '{"deployments":{"a":{"name":"a","command":"mammatus","arguments":{"bool":"bal"},"addOns":[]}}}',
            [
                Values::class => [
                    static function (Values $values): void {
                        $values->add(
                            new Values\Registry\Deployment(
                                'a',
                                'mammatus',
                                ['bool' => 'bal'],
                                [],
                            ),
                        );
                    },
                ],
            ],
        ];

        yield 'two' => [
            '{"deployments":{"a":{"name":"a","command":"mammatus","arguments":{"bool":"bal"},"addOns":[]},"b":{"name":"b","command":"mammatus","arguments":{"bool":"bal"},"addOns":[]}}}',
            [
                Values::class => [
                    static function (Values $values): void {
                        $values->add(
                            new Values\Registry\Deployment(
                                'a',
                                'mammatus',
                                ['bool' => 'bal'],
                                [],
                            ),
                        );
                    },
                    static function (Values $values): void {
                        $values->add(
                            new Values\Registry\Deployment(
                                'b',
                                'mammatus',
                                ['bool' => 'bal'],
                                [],
                            ),
                        );
                    },
                ],
            ],
        ];
    }
}
