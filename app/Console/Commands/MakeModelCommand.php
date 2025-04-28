<?php
// app/Console/Commands/MakeModelCommand.php

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;       // ← this one
use Symfony\Component\Console\Output\OutputInterface;     // ← and this one
use Symfony\Component\Filesystem\Filesystem;
use Core\Stub;

class MakeModelCommand extends Command
{
    protected static $defaultName        = 'make:model';
    protected static $defaultDescription = 'Create a new Eloquent model in a module';

    protected function configure()
    {
        $this
            ->addArgument('module', InputArgument::REQUIRED, 'Module name (e.g. employees)')
            ->addArgument('name',   InputArgument::REQUIRED, 'Model name (e.g. User)');
    }

    protected function execute(InputInterface $in, OutputInterface $out)
    {
        $module = ucfirst($in->getArgument('module'));      // Employees
        $class  = ucfirst($in->getArgument('name'));        // User
        $dir    = __DIR__ . "/../../modules/{$module}/Models";
        $fs     = new Filesystem();

        // make the folder if needed
        $fs->mkdir($dir, 0755);

        $stub      = new Stub(__DIR__ . '/../../../stubs/model.stub');
        $namespace = "App\\Modules\\{$module}\\Models";
        $code      = $stub->render([
            'namespace' => $namespace,
            'class'     => $class,
        ]);

        $file = "{$dir}/{$class}.php";
        if ($fs->exists($file)) {
            $out->writeln("<error>{$class} already exists in module {$module}!</error>");
            return Command::FAILURE;
        }

        $fs->dumpFile($file, $code);
        $out->writeln("<info>Created model:</info> {$file}");
        return Command::SUCCESS;
    }
}
