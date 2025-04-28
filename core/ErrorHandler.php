<?php
namespace Core;

use Throwable;
use Monolog\Logger;

class ErrorHandler
{
    protected static Logger $log;

    public static function init(Logger $logger): void
    {
        self::$log = $logger;
    }

    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): void
    {
        // convert PHP errors to exceptions
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    public static function handleException(Throwable $e): void
    {
        self::$log->error($e->getMessage(), [
            'exception' => $e,
            'trace'     => $e->getTraceAsString(),
        ]);

        if (($_ENV['APP_ENV'] ?? '') === 'production') {
            http_response_code(500);
            echo file_get_contents(__DIR__ . '/../app/Views/errors/500.html');
        } else {
            // re-throw so you see it in dev
            throw $e;
        }
    }

    public static function handleShutdown(): void
    {
        $err = error_get_last();
        if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_COMPILE_ERROR], true)) {
            $msg = "{$err['message']} in {$err['file']}:{$err['line']}";
            self::$log->critical($msg, ['error' => $err]);
            http_response_code(500);
            echo "A fatal error occurred.";
        }
    }
}
