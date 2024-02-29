<?php
/**
 * @desc    : 容器类
 * @author  : sam
 * @email   : sam@imccc.cc
 * @date    : 2024/2/26 19:20
 * @version : 1.0.0
 * @license : MIT
 */

namespace Imccc\Slim\Core;

use Exception;
use ReflectionClass;

class Container
{
    private $bindings = [];

    /**
     * 绑定接口或抽象类到具体实现类
     *
     * @param string $abstract 接口或抽象类名
     * @param mixed $concrete 具体实现类名、闭包或实例
     * @param bool $shared 是否共享实例
     * @return void
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {
        // 如果没有指定具体实现类，则默认与接口名一致
        if ($concrete === null) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared' => $shared,
            'instance' => null,
        ];
    }

    /**
     * 获取绑定的实例
     *
     * @param string $abstract 接口或抽象类名
     * @return mixed 具体实现类的实例
     * @throws Exception 当绑定不存在时抛出异常
     */
    public function make($abstract)
    {
        if (!isset($this->bindings[$abstract])) {
            throw new Exception("Service '$abstract' not found.");
        }

        $binding = $this->bindings[$abstract];

        // 如果共享实例且已经存在，则直接返回
        if ($binding['shared'] && $binding['instance'] !== null) {
            return $binding['instance'];
        }

        // 如果具体实现类是可实例化的，则创建新的实例
        if ($this->isInstantiable($binding['concrete'])) {
            $instance = $this->build($binding['concrete']);
        } else {
            // 否则递归调用 make 方法
            $instance = $this->make($binding['concrete']);
        }

        // 如果是共享实例，则保存到 bindings 中
        if ($binding['shared']) {
            $this->bindings[$abstract]['instance'] = $instance;
        }

        return $instance;
    }

    /**
     * 检查具体实现类是否可实例化
     *
     * @param mixed $concrete 具体实现类名、闭包或实例
     * @return bool
     */
    protected function isInstantiable($concrete)
    {
        return !($concrete instanceof Closure || is_string($concrete) && class_exists($concrete));
    }

    /**
     * 创建具体实现类的新实例
     *
     * @param mixed $concrete 具体实现类名、闭包或实例
     * @return mixed 具体实现类的实例
     * @throws Exception 当实例化失败时抛出异常
     */
    protected function build($concrete)
    {
        // 如果是闭包，则调用闭包
        if ($concrete instanceof Closure) {
            return $concrete($this);
        }

        // 否则尝试实例化具体实现类
        $reflector = new ReflectionClass($concrete);

        // 检查是否可实例化
        if (!$reflector->isInstantiable()) {
            throw new Exception("Target [$concrete] is not instantiable.");
        }

        // 获取构造函数参数
        $constructor = $reflector->getConstructor();

        // 如果没有构造函数，则直接实例化
        if ($constructor === null) {
            return new $concrete;
        }

        // 否则递归调用 make 方法获取构造函数参数的实例
        $parameters = $constructor->getParameters();
        $dependencies = $this->getDependencies($parameters);

        // 创建实例并传入依赖
        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * 获取构造函数参数的实例
     *
     * @param array $parameters 构造函数参数列表
     * @return array 构造函数参数的实例列表
     * @throws Exception 当无法解析依赖时抛出异常
     */
    protected function getDependencies($parameters)
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();

            // 如果参数不是类类型，则无法解析依赖
            if ($dependency === null) {
                throw new Exception("Unable to resolve dependency '{$parameter->getName()}'.");
            }

            // 递归调用 make 方法获取依赖的实例
            $dependencies[] = $this->make($dependency->name);
        }

        return $dependencies;
    }
}

/**
 * 示例


### 示例1：绑定接口到具体实现类

```php
// 定义接口
interface LoggerInterface {
public function log($message);
}

// 定义具体实现类
class FileLogger implements LoggerInterface {
public function log($message) {
file_put_contents('log.txt', $message . PHP_EOL, FILE_APPEND);
}
}

// 实例化容器
$container = new Container();

// 绑定接口到具体实现类
$container->bind('LoggerInterface', 'FileLogger');

// 使用示例
$logger = $container->make('LoggerInterface');
$logger->log('This is a log message.');
```

### 示例2：绑定闭包

```php
// 实例化容器
$container = new Container();

// 绑定闭包
$container->bind('api_key', function () {
return 'your_api_key';
});

// 使用示例
$apiKey = $container->make('api_key');
echo $apiKey; // 输出：your_api_key
```

### 示例3：绑定服务实例

```php
// 实例化服务实例
$logger = new FileLogger();

// 实例化容器
$container = new Container();

// 绑定服务实例
$container->bind('logger', $logger, true);

// 使用示例
$logger = $container->make('logger');
$logger->log('This is a log message.');
```

### 示例4：绑定容器实例

```php
// 实例化容器
$container = new Container();

// 绑定容器实例
$container->bind('app_container', $container);

// 使用示例
$appContainer = $container->make('app_container');
var_dump($appContainer === $container); // 输出：bool(true)
```

### 示例5：绑定类

```php
// 定义数据库连接类
class DatabaseConnection {
// 实现数据库连接逻辑
}

// 实例化容器
$container = new Container();

// 绑定类
$container->bind('Database', 'DatabaseConnection');

// 使用示例
$database = $container->make('Database');
```

这些示例展示了如何使用容器来绑定接口、闭包、服务、容器和类，并且如何使用容器获取这些绑定的实例。
 */
