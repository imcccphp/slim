<?php

namespace Imccc\Slim\Service;

class XDebugService
{
    public static function breakpoint($file, $line)
    {
        echo "断点达到 $file:$line\n";
        // 输出当前文件的代码片段
        echo "代码片段:\n";
        $lines = file($file);
        for ($i = max(0, $line - 3); $i < min(count($lines), $line + 2); $i++) {
            echo $lines[$i];
        }
        // 等待用户输入继续执行
        readline("按 Enter 键继续...");
    }

    public static function debug_backtrace()
    {
        // 输出当前调用栈的信息
        echo "调用堆栈:\n";
        $stack = debug_backtrace();
        foreach ($stack as $frame) {
            echo " - {$frame['function']}() 调用于: {$frame['file']}:{$frame['line']}\n";
        }
    }

    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        echo "错误: $errstr 在: $errfile 行: $errline\n";
        // 调用断点功能以便用户查看错误发生的位置
        self::breakpoint($errfile, $errline);
        // 终止脚本
        exit(1);
    }
}
