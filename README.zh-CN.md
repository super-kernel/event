<p align="right">
  <a href="README.md">English</a> | <strong>中文文档</strong>
</p>

# SuperKernel Event

`super-kernel/event` 定义 SuperKernel 各运行时、工具和集成共享的应用生命周期事件。它只包含声明：事件的创建、分发时机、监听器发现、传播和失败处理，都由发布事件的运行时或组件负责。

每个事件都直接位于 `SuperKernel\Event` 下，是实现无方法标记接口 `SuperKernel\Contract\EventInterface` 的 final readonly 类。本包只依赖 `super-kernel/contract`；安装它不会选定事件分发器实现，也不会改变现有的启动流程。

## 生命周期事件

本包定义了两个事件：

| 事件                     | 边界与载荷                                                                                   |
|--------------------------|----------------------------------------------------------------------------------------------|
| `BeforeApplicationStart` | 应用已创建，在其工厂返回之前可供组件进行配置；`application()` 返回传给构造函数的同一个应用。 |
| `ApplicationBuild`       | 构建主机正在生成仅恢复主机（例如 Phar）直接加载、无需再生成的产物；该事件不携带载荷。        |

创建事件不会触发启动或分发。Framework 的 `ApplicationProvider` 为它创建的每个应用同步发布一次 `BeforeApplicationStart`，时机在安装 Symfony 调度器之后、返回应用之前。监听器的失败会在工厂返回之前向外传播；这一边界不会调用 `ApplicationInterface::run()`。

SKernel 的 `build` 命令在项目自己的进程中创建容器后发布一次 `ApplicationBuild`，不会创建或运行应用。监听器在此写入各自的产物（例如预先准备好的 AOP 代理），使打包后的应用直接加载它们，而不必再生成。任何监听器失败都会使构建失败。

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

应用可以为其他生命周期边界定义自己的事件类型。
