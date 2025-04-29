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
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    public static function handleException(Throwable $e): void
    {
        // 1) Always log
        self::$log->error($e->getMessage(), [
            'exception' => $e,
            'trace'     => $e->getTraceAsString(),
        ]);

        $appEnv    = $_ENV['APP_ENV']   ?? 'production';
        $debugFlag = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($appEnv !== 'production') {
            // **Development**: show full details via the PHP template
            $devPage = __DIR__ . '/../app/Views/errors/500.dev.php';
            if (is_readable($devPage)) {
                // make exception available to the template
                $exception = $e;
                require $devPage;
                return;
            }
        }

        // **Production** (or if dev‐template missing): safe static HTML
        http_response_code(500);
        $safePage = __DIR__ . '/../app/Views/errors/500.html';
        if (is_readable($safePage)) {
            echo file_get_contents($safePage);
        } else {
            echo '<h1>500 — Internal Server Error</h1>';
            echo '<p>Something went wrong. Please try again later.</p>';
        }
    }

    public static function handleShutdown(): void
    {
        $err = error_get_last();
        if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_COMPILE_ERROR], true)) {
            $msg = "{$err['message']} in {$err['file']}:{$err['line']}";
            self::$log->critical($msg, ['error' => $err]);
            // we’re already in a fatal state—just echo minimal text
            http_response_code(500);
            echo '<h1>500 — Fatal Error</h1>';
        }
    }
}
