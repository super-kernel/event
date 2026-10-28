<?php

declare(strict_types=1);

namespace SuperKernel\Event\Tests\Architecture;

use FilesystemIterator;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SuperKernel\Contract\EventInterface;
use SuperKernel\Event\BeforeApplicationStart;
use Throwable;

use function dirname;
use function str_replace;
use function strlen;
use function substr;

use const DIRECTORY_SEPARATOR;

final class EventInterfaceTest extends TestCase
{
    /** @throws Throwable When fixture setup or the operation under test throws */
    public function testEveryProductionDeclarationIsAnImmutableFrameworkEvent(): void
    {
        $source = dirname(__DIR__, 2) . '/src';
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
        );
        /** @var list<class-string> $eventClasses */
        $eventClasses = [];

        foreach ($iterator as $file) {
            if (!$file->isFile() || 'php' !== $file->getExtension()) {
                continue;
            }

            $relativePath = substr($file->getPathname(), strlen($source) + 1, -4);
            $className = 'SuperKernel\\Event\\' . str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);
            $event = new ReflectionClass($className);

            self::assertTrue($event->isFinal(), $className . ' must be final.');
            self::assertTrue($event->isReadOnly(), $className . ' must be readonly.');
            self::assertTrue(
                $event->implementsInterface(EventInterface::class),
                $className . ' must implement ' . EventInterface::class . '.',
            );

            $eventClasses[] = $event->getName();
        }

        self::assertEqualsCanonicalizing([
            BeforeApplicationStart::class,
        ], $eventClasses);
    }
}
