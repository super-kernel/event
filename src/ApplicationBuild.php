<?php

declare(strict_types=1);

namespace SuperKernel\Event;

use SuperKernel\Contract\EventInterface;

/**
 * Signals that a build host is writing the artifacts that restore-only hosts, such as a Phar, load instead of
 * generating them.
 */
final readonly class ApplicationBuild implements EventInterface
{
}
