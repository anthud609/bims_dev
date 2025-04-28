<?php
// app/Console/Commands/MakeControllerCommand.php
namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Core\Stub;

class MakeControllerCommand extends Command
{
    protected static $defaultName = 'make:controller';
    protected static $defaultDescription = 'Create a new controller class in a module';

    protected function configure()
    {
        $this
            ->addArgument('module', InputArgument::REQUIRED, 'Module name (e.g. auth)')
            ->addArgument('name',   InputArgument::REQUIRED, 'Controller name (e.g. User)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $module    = ucfirst($input->getArgument('module'));
        $classBase = ucfirst($input->getArgument('name'));
        $class     = $classBase . 'Controller';
        $fs        = new Filesystem();

        // target dir: app/modules/Auth/Controllers
        $dir = __DIR__ . "/../../modules/{$module}/Controllers";
        if (! $fs->exists($dir)) {
            $fs->mkdir($dir, 0755);
        }

        // render stub
        $stub      = new Stub(__DIR__ . '/../../../stubs/controller.stub');
        $namespace = "App\\Modules\\{$module}\\Controllers";
        $code      = $stub->render([
            'namespace' => $namespace,
            'class'     => $class,
        ]);

        $file = "{$dir}/{$class}.php";
        if ($fs->exists($file)) {
            $output->writeln("<error>{$class} already exists in module {$module}!</error>");
            return Command::FAILURE;
        }

        $fs->dumpFile($file, $code);
        $output->writeln("<info>Created:</info> {$file}");
        return Command::SUCCESS;
    }
}
