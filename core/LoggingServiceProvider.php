<?php
namespace Core;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Processor\UidProcessor;
use Monolog\LogRecord;

class LoggingServiceProvider
{
    protected static Logger $log;

    public static function init(array $env): void
    {
        $logger = new Logger($env['LOG_NAME'] ?? 'bims');

        // add a unique request ID
        $logger->pushProcessor(new UidProcessor(16));

        // ensure our logs/ folder exists
        $projectRoot = dirname(__DIR__, 2);        // from core/ to project root
        $logDir      = "{$projectRoot}/logs";
        if (! is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        $logFile = "{$logDir}/app.log";

        // 1) File handler: DEBUG+ (always)
        $logger->pushHandler(
            new RotatingFileHandler(
                $logFile,
                30,           // keep 30 days
                Logger::DEBUG // change to WARNING if you only want prod-level in dev
            )
        );

        // 2) STDOUT handler: dev only
        if (($env['APP_ENV'] ?? 'development') !== 'production') {
            $logger->pushHandler(
                new StreamHandler('php://stdout', Logger::DEBUG)
            );
        }

        // 3) Redaction (Monolog 3)
        $logger->pushProcessor(function(LogRecord $rec): LogRecord {
            $ctx = $rec->context;
            if (isset($ctx['password'])) {
                $ctx['password'] = '[REDACTED]';
            }
            if (isset($ctx['ssn'])) {
                $ctx['ssn'] = '[REDACTED]';
            }
            return $rec->with(context: $ctx);
        });

        self::$log = $logger;
    }

    public static function getLogger(): Logger
    {
        return self::$log;
    }
}
