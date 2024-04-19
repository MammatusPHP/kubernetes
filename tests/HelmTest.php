<?php

declare(strict_types=1);

namespace Mammatus\Tests\Kubernetes;

use Mammatus\Kubernetes\Events\Helm\Values;
use Mammatus\Kubernetes\Helm;
use WyriHaximus\AsyncTestUtilities\AsyncTestCase;
use WyriHaximus\Broadcast\ArrayListenerProvider;
use WyriHaximus\Broadcast\Dispatcher;

final class HelmTest extends AsyncTestCase
{
    /**
     * @param array<class-string, array<callable>> $listeners
     *
     * @test
     * @dataProvider valuesProvider
     */
    public function values(string $expectedOutput, array $listeners): void
    {
        self::expectOutputString($expectedOutput);

        $dispatcher = Dispatcher::createFromListenerProvider(new ArrayListenerProvider($listeners));

        $exitCode = (new Helm($dispatcher))->json();

        self::assertSame(0, $exitCode);
    }

    /** @return iterable<array<string|array<class-string, array<callable>>>> */
    public static function valuesProvider(): iterable
    {
        yield 'nothing' => [
            '[]',
            [],
        ];

        yield 'one' => [
            '{"a":[true,false]}',
            [
                Values::class => [
                    static function (Values $values): void {
                        $values->registry->add(
                            'a',
                            [true, false],
                        );
                    },
                ],
            ],
        ];

        yield 'two' => [
            '{"a":[true,false],"b":[true,false]}',
            [
                Values::class => [
                    static function (Values $values): void {
                        $values->registry->add(
                            'a',
                            [true, false],
                        );
                    },
                    static function (Values $values): void {
                        $values->registry->add(
                            'b',
                            [true, false],
                        );
                    },
                ],
            ],
        ];
    }
}
