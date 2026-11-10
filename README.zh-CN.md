<p align="right">
  <a href="README.md">English</a> | <strong>中文文档</strong>
</p>

# SuperKernel Event

`super-kernel/event` 声明 SuperKernel 发布的应用生命周期事件，使组件和应用能够在应用启动时和项目构建时执行操作。

## 安装

```bash
composer require super-kernel/event
```

本包除 PHP 外没有其他要求。它只声明事件：要接收这些事件，应用还需要注册监听器的 `super-kernel/event-dispatcher`，以及一个发布者：`BeforeApplicationStart` 由 `super-kernel/framework` 发布，`ApplicationBuild` 由 `skernel build` 发布。

## 提供的能力

- `BeforeApplicationStart`：在应用创建之后、返回去运行之前发布，携带该应用。
- `ApplicationBuild`：由 `skernel build` 发布一次，使组件写出打包后的应用所加载的产物。

它不会：

- 分发事件或发现监听器，这由 `super-kernel/event-dispatcher` 负责；
- 决定事件何时发布，这由发布事件的包负责；
- 为其他边界（例如启动之后或停止时）声明事件；应用为这些边界声明自己的事件类。

## 快速开始

在由 `super-kernel/skeleton` 创建的应用中，下面的监听器会在控制台应用运行之前为它改名。将它保存为 `src/Listener/NameTheApplication.php`：

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

构建项目，然后查询应用的名称：

```bash
skernel build
php bin/bootstrap.php --version
```

输出以 `Billing` 开头。

## 公开接口

| 名称 | 类别 | 用途 |
| --- | --- | --- |
| `SuperKernel\Event\BeforeApplicationStart` | 事件 | 在应用创建之后、返回之前发布；`application()` 返回该应用。 |
| `SuperKernel\Event\ApplicationBuild` | 事件 | 在 `skernel build` 期间于项目自己的进程中发布一次；不携带载荷。 |

## 需要了解的行为

- 创建事件对象不会发布任何内容，也不会启动任何内容。
- `super-kernel/framework` 为它创建的每个应用同步发布一次 `BeforeApplicationStart`。监听器抛出的异常会向外传播，应用不会被返回，也不会运行。
- `skernel build` 发布 `ApplicationBuild` 时不会创建或运行应用。监听器抛出的异常会使构建失败。
- 监听器从构建元数据中发现：添加、删除或修改监听器之后，请重新运行 `skernel build`，或者用会先构建的 `skernel run` 启动。

## 文档

开发者文档介绍了这两个事件、更多监听器示例，以及发布它们的各个包如何协同工作：[SuperKernel Event](https://github.com/super-kernel/.github/blob/main/docs/components/event.zh-CN.md)。

## 许可证

基于 MIT 许可证发布，详见 [LICENSE](LICENSE)。
