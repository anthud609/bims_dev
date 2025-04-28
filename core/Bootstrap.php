<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Dotenv\Dotenv;
use Core\LoggingServiceProvider;
use Core\ErrorHandler;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
// init logger
LoggingServiceProvider::init($_ENV);

// register global error & exception handlers
ErrorHandler::init(LoggingServiceProvider::getLogger());
set_error_handler([ErrorHandler::class,'handleError']);
set_exception_handler([ErrorHandler::class,'handleException']);
register_shutdown_function([ErrorHandler::class,'handleShutdown']);

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
'host' => $_ENV['DB_HOST'],
'database' => $_ENV['DB_DATABASE'],
'username' => $_ENV['DB_USERNAME'],
'password' => $_ENV['DB_PASSWORD'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
