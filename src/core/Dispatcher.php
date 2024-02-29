<?php
declare (strict_types = 1);

namespace Imccc\Slim\Core;

use Psr\Http\Message\RequestInterface;

class Dispatcher
{
    protected $routeParser;

    public function __construct(RouteParser $routeParser)
    {
        $this->routeParser = $routeParser;
        $this->dispatch($this->routeParser());
    }

    public function dispatch(RequestInterface $request)
    {
        // 解析路由
        $routeInfo = $this->routeParser->parse($request);

        if ($routeInfo !== null) {
            $mvcArray = $routeInfo;

            // 执行控制器方法
            $response = call_user_func_array([$mvcArray['controller'], $mvcArray['method']], $mvcArray['params']);

            // 发送响应
            foreach ($response->getHeaders() as $header => $values) {
                foreach ($values as $value) {
                    header(sprintf('%s: %s', $header, $value), false);
                }
            }
            http_response_code($response->getStatusCode());
            echo $response->getBody();
        } else {
            echo '未找到匹配的路由';
        }
    }
}
