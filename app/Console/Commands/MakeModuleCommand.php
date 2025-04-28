<?php
namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Core\Stub;

class MakeModuleCommand extends Command
{
    protected static $defaultName        = 'make:module';
    protected static $defaultDescription = 'Create a full module scaffold (dirs + Controller, Model, View, Routes)';

    protected function configure(): void
    {
        $this->addArgument('module', InputArgument::REQUIRED, 'Module name (e.g. employees)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $raw     = strtolower($input->getArgument('module'));
        $module  = ucfirst($raw);  // e.g. Employees
        $baseDir = __DIR__ . "/../../modules/{$module}";
        $fs      = new Filesystem();

        if ($fs->exists($baseDir)) {
            $output->writeln("<error>Module “{$module}” already exists.</error>");
            return Command::FAILURE;
        }

        // 1) Make dirs
        $dirs = ['Controllers','Models','Views','Routes'];
        foreach ($dirs as $dir) {
            $path = "{$baseDir}/{$dir}";
            $fs->mkdir($path, 0755);
            $output->writeln("📁 Created: {$path}");
        }

        // 2) README
        $readme = "{$baseDir}/README.md";
        $fs->dumpFile($readme,
            "# Module: {$module}\n\n" .
            "Auto-scaffolded module with Controllers, Models, Views, and Routes."
        );
        $output->writeln("📝 Created: {$readme}");

        // 3) Stub and write Controller
        $ctlStub = new Stub(__DIR__ . '/../../../stubs/controller.stub');
        $ctlCode = $ctlStub->render([
            'namespace' => "App\\Modules\\{$module}\\Controllers",
            'class'     => "{$module}Controller"
        ]);
        $ctlPath = "{$baseDir}/Controllers/{$module}Controller.php";
        $fs->dumpFile($ctlPath, $ctlCode);
        $output->writeln("✔️  Controller: {$ctlPath}");

        // 4) Stub and write Model
        $mdlStub = new Stub(__DIR__ . '/../../../stubs/model.stub');
        $mdlCode = $mdlStub->render([
            'namespace' => "App\\Modules\\{$module}\\Models",
            'class'     => $module
        ]);
        $mdlPath = "{$baseDir}/Models/{$module}.php";
        $fs->dumpFile($mdlPath, $mdlCode);
        $output->writeln("✔️  Model:      {$mdlPath}");

        // 5) Stub and write a default View (index.php)
        $viewStub = new Stub(__DIR__ . '/../../../stubs/view.stub');
        $viewCode = $viewStub->render([
            'module' => $module,
            'view'   => 'index'
        ]);
        $viewPath = "{$baseDir}/Views/index.php";
        $fs->dumpFile($viewPath, $viewCode);
        $output->writeln("✔️  View:       {$viewPath}");

        // 6) Routes/Web.php + Api.php
        $routesDir = "{$baseDir}/Routes";
        $webPhp = <<<PHP
<?php
// Web routes for {$module}
use App\Modules\\{$module}\\Controllers\\{$module}Controller;
return [
    // ['GET', '/{$raw}', [{$module}Controller::class,'index']],
];
PHP;
        $apiPhp = <<<PHP
<?php
// API routes for {$module}
use App\Modules\\{$module}\\Controllers\\{$module}Controller;
return [
    // ['GET', '/api/{$raw}', [{$module}Controller::class,'index']],
];
PHP;
        $fs->dumpFile("{$routesDir}/Web.php", $webPhp);
        $fs->dumpFile("{$routesDir}/Api.php", $apiPhp);
        $output->writeln("✔️  Routes:     {$routesDir}/Web.php, Api.php");

        $output->writeln("\n🎉 Module “{$module}” fully scaffolded!");
        return Command::SUCCESS;
    }
}
