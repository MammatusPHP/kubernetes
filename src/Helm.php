<?php

declare(strict_types=1);

namespace Mammatus\Kubernetes;

use Mammatus\ExitCode;
use Mammatus\Kubernetes\Events\Helm\Values;
use Psr\EventDispatcher\EventDispatcherInterface;

use function json_encode;

/** @api */
final readonly class Helm
{
    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function json(string ...$valuesFiles): ExitCode
    {
        $values = Values::createFromFile(...$valuesFiles);
        $this->eventDispatcher->dispatch($values);

        echo json_encode($values->get());

        return ExitCode::Success;
    }
}
