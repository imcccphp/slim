<?php
namespace Imccc\Slim\Service;

class LogService
{
    private $logFilePath;

    public function __construct($logFilePath)
    {
        $this->logFilePath = $logFilePath;
    }

    public function log($message)
    {
        $logMessage = date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;
        file_put_contents($this->logFilePath, $logMessage, FILE_APPEND);
    }
}
