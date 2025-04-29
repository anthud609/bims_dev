<?php
namespace Core;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;
use Monolog\LogRecord;

class LoggingServiceProvider
{
    protected static Logger $log;

    public static function init(array $env): void
    {
        $name      = $env['LOG_NAME'] ?? 'bims';
        $appEnv    = $env['APP_ENV']   ?? 'production';
        $debugFlag = filter_var($env['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);

        // Decide minimum level:
        // production & debug=false  → WARNING+    ← changed from NOTICE+
        // production & debug=true   → DEBUG+  (all)
        // development & debug=true   → DEBUG+  (all)
        // development & debug=false  → INFO+   (everything except DEBUG)
        if ($appEnv === 'production') {
            $minLevel = $debugFlag ? Logger::DEBUG : Logger::WARNING;  // ← here
        } else {
            $minLevel = $debugFlag ? Logger::DEBUG : Logger::INFO;
        }

        $logger = new Logger($name);
        $logger->pushProcessor(new UidProcessor(16));

        // Ensure logs/ exists next to your project root
        $projectRoot = dirname(__DIR__); // anthud609-bims_dev
        $logDir      = "{$projectRoot}/logs";
        if (! is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        // 1) Rotating file handler for all levels ≥ $minLevel
        $logger->pushHandler(new RotatingFileHandler(
            "{$logDir}/app.log",
            30,
            $minLevel
        ));

        // 2) If *not* production, also echo to stdout (so you see logs live)
        if ($appEnv !== 'production') {
            $logger->pushHandler(new StreamHandler('php://stdout', $minLevel));
        }

        // 3) Redaction processor (Monolog 3)
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
