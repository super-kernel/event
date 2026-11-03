<p align="right">
  <strong>English</strong> | <a href="README.zh-CN.md">中文文档</a>
</p>

# SuperKernel Event

`super-kernel/event` defines the application lifecycle events shared by SuperKernel runtimes, tools, and integrations. It contains declarations only: event creation, dispatch timing, listener discovery, propagation, and failure handling remain the responsibility of the runtime or component that publishes an event.

Every event lives directly under `SuperKernel\Event` and is a final readonly class implementing the methodless `SuperKernel\Contract\EventInterface` marker. The package depends only on `super-kernel/contract`; installing it does not select an event dispatcher implementation or change the current bootstrap flow.

## Lifecycle events

The package defines two events:

| Event                    | Boundary and payload                                                                                                                                                             |
|--------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `BeforeApplicationStart` | The application has been created and is ready for component configuration before its factory returns it; `application()` returns the same application passed to the constructor. |
| `ApplicationBuild`       | A build host is writing the artifacts that restore-only hosts, such as a Phar, load instead of generating them; the event carries no payload.                                    |

Creating the event performs no startup or dispatch. Framework's `ApplicationProvider` publishes `BeforeApplicationStart` synchronously once for each application it creates, after installing the Symfony dispatcher and before returning the application. Listener failures propagate before the factory returns; this boundary does not call `ApplicationInterface::run()`.

SKERNEL's `build` command publishes `ApplicationBuild` once in the project's own process after creating its container, without creating or running the application. Listeners write their artifacts, such as prepared AOP proxies, so that a packaged application loads them instead of generating them. A listener failure fails the build.

```php
use SuperKernel\Attribute\Listener;
use SuperKernel\Contract\ListenerInterface;
use SuperKernel\Event\ApplicationBuild;

#[Listener]
final readonly class WriteBuildArtifacts implements ListenerInterface
{
    public function listen(): array
    {
        return [
            ApplicationBuild::class,
        ];
    }

    public function process(object $event): void
    {
        // Write the artifacts that restore-only hosts load.
    }
}
```

Applications can define their own event types for additional lifecycle boundaries.
