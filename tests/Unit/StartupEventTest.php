<?php

declare(strict_types=1);

namespace SuperKernel\Event\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SuperKernel\Contract\ApplicationInterface;
use SuperKernel\Event\BeforeApplicationStart;

final class StartupEventTest extends TestCase
{
    public function testApplicationEventExposesTheApplicationWithoutRunningIt(): void
    {
        $application = $this->createMock(ApplicationInterface::class);
        $application->expects(self::never())->method('run');

        self::assertSame($application, new BeforeApplicationStart($application)->application());
    }
}
