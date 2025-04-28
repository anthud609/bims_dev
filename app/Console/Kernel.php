<?php
// app/Console/Kernel.php
namespace App\Console;

use Symfony\Component\Console\Application;
use App\Console\Commands\MakeControllerCommand;
use App\Console\Commands\MakeModelCommand;
use App\Console\Commands\MakeViewCommand;
use App\Console\Commands\MakeModuleCommand;

class Kernel
{
    public static function run()
    {
        $app = new Application('MyApp Console', '1.0');

        // register your generators
        $app->add(new MakeControllerCommand());
        $app->add(new MakeModelCommand());
        $app->add(new MakeViewCommand());
        $app->add(new MakeModuleCommand());

        $app->run();
    }
}
