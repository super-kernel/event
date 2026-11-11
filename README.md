<p align="right">
  <strong>English</strong> | <a href="README.zh-CN.md">中文文档</a>
</p>

# SuperKernel Event

`super-kernel/event` declares the application lifecycle events that SuperKernel publishes, so that components and applications can act when an application starts and when a project is built.

## Installation

```bash
composer require super-kernel/event
```

The package has no requirement beyond PHP. It only declares events: to receive them, an application also needs `super-kernel/event-dispatcher`, which registers listeners, and a publisher, which is `super-kernel/framework` for `BeforeApplicationStart` and `skernel build` for `ApplicationBuild`.

## What it provides

- `BeforeApplicationStart`, published after the application is created and before it is returned to run, carrying that application.
- `ApplicationBuild`, published once by `skernel build` so that components write the artifacts a packaged application loads.

It does not:

- dispatch events or discover listeners; `super-kernel/event-dispatcher` does;
- decide when an event is published; the publishing package does;
- declare events for other boundaries, such as after start or at stop; an application declares its own event classes for those.

## Quick start

In an application created from `super-kernel/skeleton`, this listener renames the console application before it runs. Save it as `src/Listener/NameTheApplication.php`:

```php
<?php

declare(strict_types=1);

namespace Src\Listener;

use SuperKernel\Attribute\Listener;
use SuperKernel\Contract\ListenerInterface;
use SuperKernel\Event\BeforeApplicationStart;
use Symfony\Component\Console\Application;

#[Listener]
final readonly class NameTheApplication implements ListenerInterface
{
    public function listen(): array
    {
        return [
            BeforeApplicationStart::class,
        ];
    }

    public function process(object $event): void
    {
        assert($event instanceof BeforeApplicationStart);

        $application = $event->application();

        if ($application instanceof Application) {
            $application->setName('Billing');
        }
    }
}
```

Build the project and ask the application for its name:

```bash
skernel build
php bin/bootstrap.php --version
```

The output starts with `Billing`.

## Public surface

| Name | Kind | Purpose |
| --- | --- | --- |
| `SuperKernel\Event\BeforeApplicationStart` | Event | Published after the application is created and before it is returned; `application()` returns that application. |
| `SuperKernel\Event\ApplicationBuild` | Event | Published once during `skernel build`, in the project's own process; it carries no payload. |

## Behavior to know

- Creating an event object publishes nothing and starts nothing.
- `super-kernel/framework` publishes `BeforeApplicationStart` once for each application it creates, synchronously. An exception from a listener propagates, and the application is not returned or run.
- `skernel build` publishes `ApplicationBuild` without creating or running the application. An exception from a listener fails the build.
- Listeners are discovered from the build metadata: run `skernel build` again after adding, removing, or changing a listener, or start with `skernel run`, which builds first.

## Documentation

The developer documentation describes both events, more listener examples, and how the packages that publish them work together: [SuperKernel Event](https://github.com/super-kernel/.github/blob/main/docs/components/event.md).

## License

Released under the MIT License. See [LICENSE](LICENSE).
