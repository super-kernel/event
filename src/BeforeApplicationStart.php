<?php

declare(strict_types=1);

namespace SuperKernel\Event;

use SuperKernel\Contract\ApplicationInterface;
use SuperKernel\Contract\EventInterface;

/**
 * Signals that an application has been created and is ready for component configuration before being returned.
 */
final readonly class BeforeApplicationStart implements EventInterface
{
    public function __construct(private ApplicationInterface $application)
    {
    }

    public function application(): ApplicationInterface
    {
        return $this->application;
    }
}
