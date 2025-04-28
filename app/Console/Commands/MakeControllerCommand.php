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
    protected static $defaultDescription = 'Create a new controller class';

    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Controller name (e.g. User)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name      = $input->getArgument('name');
        $className = ucfirst($name) . 'Controller';
        $fs        = new Filesystem();

        // ensure target dir exists
        $dir = __DIR__ . '/../../Controllers';
        if (! $fs->exists($dir)) {
            $fs->mkdir($dir, 0755);
        }

        $stub   = new Stub(__DIR__ . '/../../../stubs/controller.stub');
        $code   = $stub->render(['name' => ucfirst($name)]);
        $file   = "$dir/{$className}.php";

        if ($fs->exists($file)) {
            $output->writeln("<error>$className already exists!</error>");
            return Command::FAILURE;
        }

        $fs->dumpFile($file, $code);
        $output->writeln("<info>Created controller:</info> $file");
        return Command::SUCCESS;
    }
}
