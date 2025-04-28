<?php
// app/Console/Commands/MakeViewCommand.php
namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Core\Stub;

class MakeViewCommand extends Command
{
    protected static $defaultName        = 'make:view';
    protected static $defaultDescription = 'Create a new view file in a module';

    protected function configure()
    {
        $this
            ->addArgument('module', InputArgument::REQUIRED, 'Module name (e.g. jobs)')
            ->addArgument('view',   InputArgument::REQUIRED, 'View name (e.g. index)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $module = ucfirst($input->getArgument('module'));  // e.g. Jobs
        $view   = $input->getArgument('view');             // e.g. index
        $fs     = new Filesystem();

        // 1) ensure Views folder exists
        $dir = __DIR__ . "/../../modules/{$module}/Views";
        if (! $fs->exists($dir)) {
            $fs->mkdir($dir, 0755);
        }

        // 2) render stub
        $stub   = new Stub(__DIR__ . '/../../../stubs/view.stub');
        $code   = $stub->render([
            'module' => $module,
            'view'   => $view,
        ]);

        // 3) write file (with .php extension)
        $file = "{$dir}/{$view}.php";
        if ($fs->exists($file)) {
            $output->writeln("<error>View {$view}.php already exists in module {$module}!</error>");
            return Command::FAILURE;
        }

        $fs->dumpFile($file, $code);
        $output->writeln("<info>Created view:</info> {$file}");
        return Command::SUCCESS;
    }
}
