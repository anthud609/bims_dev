<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Dotenv\Dotenv;
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();


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
