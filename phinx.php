<?php
require_once __DIR__ . '/vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__)->load();

return [
  'paths' => [
    'migrations' => 'database/migrations',
    'seeds'      => 'database/seeds',
  ],
  'environments' => [
    'default_migration_table' => 'phinxlog',
    'default_environment'     => 'development',
    'production' => [ /* your prod creds */ ],
    'development'=> [
      'adapter' => 'mysql',
      'host'    => $_ENV['DB_HOST'],
      'name'    => $_ENV['DB_DATABASE'],
      'user'    => $_ENV['DB_USERNAME'],
      'pass'    => $_ENV['DB_PASSWORD'],
      'port'    => '3306',
      'charset' => 'utf8',
    ],
    'testing' => [ /* optional */ ]
  ],
  'version_order' => 'creation'
];
