<?php
namespace Imccc\Slim\Core;

class Router
{
    //定义路由规则数组
    protected $routes = [];

    //定义要匹配的路由规则
    protected $patterns = [
        ':any' => '[^/]+',
        ':num' => '[0-9]+',
        ':all' => '.*',
    ];

    //定义请求类型
    protected $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];

    //定义请求方法
    protected $callable;

    //定义回调函数
    public function __call($method, $params)
    {
        if (in_array(strtoupper($method), $this->methods)) {
            //调用map函数
            $this->map(strtoupper($method), $params[0], $params[1]);
        }
    }

    /*
    添加路由规则
    @param string $method
    @param string $route
    @param string $target
     */
    public function map($method, $route, $target)
    {
        //替换掉匹配的字符
        $route = strtr($route, $this->patterns);

        //把请求方法添加到数组
        $this->routes[$method][$route] = $target;
    }

    /*
    匹配路由
    @param string $path
    @return bool
     */
    public function match($request)
    {
        $path_info = $request->getUri()->getPath();
        $method = $request->getMethod();

        //普通模式
        if (isset($this->routes[$method][$path_info])) {
            //找到了匹配的路由
            $target = $this->routes[$method][$path_info];
        } else {
            //混杂模式
            foreach ($this->routes[$method] as $route => $target) {
                //用正则去匹配
                $pattern = "#^$route$#";
                if (preg_match($pattern, $path_info, $params)) {
                    $target = $this->routes[$method][$route];
                    break;
                }
            }
        }

        //如果存在匹配到的路由，就把回调函数赋给$callable
        if (isset($target)) {
            if (is_string($target)) {
                //把字符串分割成数组
                $target = explode('@', $target, 2);

                //实例化类
                $controller = new $target[0];
                $function = $target[1];

                //把匹配到的参数放入$params
                array_shift($params);

                $this->callable = function () use ($controller, $function, $params, $request) {
                    return call_user_func_array([$controller, $function], array_merge(array($request), $params));
                };
            } else {
                $this->callable = $target;
            }
        }
    }

    /*
    运行程序
    @param object\Psr\Http\Message\RequestInterface
    @param object\Psr\Http\Message\ResponseInterface
     */
    public function run(RequestInterface $request, ResponseInterface $response)
    {
        //匹配路由
        $this->match($request);

        if (is_callable($this->callable)) {
            //调用回调函数
            return call_user_func($this->callable, $response);
        } else {
            //未找到路由
            $response->getBody()->write('Error: 404 Not Found');
            return $response;
        }
    }
}

//示例
// pathinfo模式
$router = new Imccc\Slim\Router();
$router->get('/index/index', 'IndexController@index');
$router->post('/index/index', 'IndexController@index');

// 混杂模式
$router = new Imccc\Slim\Router();
$router->get('/:any/index', 'IndexController@index');
$router->post('/:any/index', 'IndexController@index');

// 普通模式
$router = new Imccc\Slim\Router();
$router->get('index.php/index/index', 'IndexController@index');
$router->post('index.php/index/index', 'IndexController@index');
