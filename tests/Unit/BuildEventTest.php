<?php

declare(strict_types=1);

namespace SuperKernel\Event\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SuperKernel\Event\ApplicationBuild;

use function get_object_vars;

final class BuildEventTest extends TestCase
{
    public function testBuildEventCarriesNoPayload(): void
    {
        self::assertSame([], get_object_vars(new ApplicationBuild()));
    }
}
