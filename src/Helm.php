<?php

declare(strict_types=1);

namespace Mammatus\Kubernetes;

use Mammatus\Kubernetes\Events\Helm\Values;
use Mammatus\Kubernetes\Events\Helm\Values\Registry;
use Psr\EventDispatcher\EventDispatcherInterface;

use function json_encode;

final readonly class Helm
{
    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function json(): int
    {
        $registry = new Registry();
        $this->eventDispatcher->dispatch(new Values($registry));

        echo json_encode($registry->get());

        return 0;
    }
}
