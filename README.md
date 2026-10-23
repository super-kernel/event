# SuperKernel Event

`super-kernel/event` defines the application startup event shared by SuperKernel runtimes and
integrations. It contains declarations only: event creation, dispatch timing, listener discovery, propagation, and
failure handling remain the responsibility of the runtime or component that publishes an event.

Every event lives directly under `SuperKernel\Event` and is a final readonly class implementing the methodless
`SuperKernel\Contract\EventInterface` marker. The package
depends only on `super-kernel/contract`; installing it does not select an event dispatcher implementation or change the
current bootstrap flow.

## Startup events

The package defines one event:

| Event | Boundary and payload |
| --- | --- |
| `BeforeApplicationStart` | The application has been created and is ready for component configuration before its factory returns it; `application()` returns the same application passed to the constructor. |

Creating the event performs no startup or dispatch. Framework's `ApplicationProvider` publishes
`BeforeApplicationStart` synchronously once for each application it creates, after installing the Symfony dispatcher
and before returning the application. Listener failures propagate before the factory returns; this boundary does not
call `ApplicationInterface::run()`.

Applications can define their own event types for additional lifecycle boundaries.

## Development

```bash
composer84 validate --strict
composer84 test
composer84 format:check
composer84 format
```
