<?php

declare(strict_types=1);

namespace Mammatus\Kubernetes;

use Mammatus\ExitCode;
use Mammatus\Kubernetes\Events\Helm\Values;
use Psr\EventDispatcher\EventDispatcherInterface;

use function json_encode;

final readonly class Helm
{
    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function json(string ...$valuesFiles): ExitCode
    {
        $registry = new Values\Registry(Values\ValuesFile::createFromFile(...$valuesFiles));
        $this->eventDispatcher->dispatch(new Values($registry));

        echo json_encode($registry->get());

        return ExitCode::Success;
    }
}
