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
            '{"a":{"bool":true,"bal":false}}',
            [
                Values::class => [
                    static function (Values $values): void {
                        $values->registry->add(
                            'a',
                            ['bool' => true, 'bal' => false],
                        );
                    },
                ],
            ],
        ];

        yield 'two' => [
            '{"a":{"bool":true,"bal":false},"b":{"bool":true,"bal":false}}',
            [
                Values::class => [
                    static function (Values $values): void {
                        $values->registry->add(
                            'a',
                            ['bool' => true, 'bal' => false],
                        );
                    },
                    static function (Values $values): void {
                        $values->registry->add(
                            'b',
                            ['bool' => true, 'bal' => false],
                        );
                    },
                ],
            ],
        ];
    }
}
