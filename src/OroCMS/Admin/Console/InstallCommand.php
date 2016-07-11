<?php
namespace OroCMS\Admin\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the OroCMS admin package or modules.';

    /**
     * @var OroCMS\Admin\Repositories\ModuleRepository
     */
    protected $module;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->module = $this->laravel['modules'];

        $module_name = $this->option('module');
        if ($module_name) {
            return $this->installModule($module_name);
        }

        // install main package
        $this->installPackage();
    }

    /**
     * Install the package.
     */
    protected function installPackage()
    {
        // do data migration
        $this->call('admin:migrate');
        $this->call('migrate');

        // seeding
        $this->call('admin:seed');

        // publish providers/assets
        $this->call('vendor:publish', [
            '--provider' => 'OroCMS\Admin\AdminServiceProvider',
            ['--tag' => ['config', 'assets']],
        ]);

        $this->call('optimize');

        #
        # copy resource files
        #
        $filesystem = $this->laravel['files'];
        $resources = [
            '.bowerrc',
            'bower.json',
            'package.json',
            'gulpfile.js'
        ];
        foreach ($resources as $resource) {
            $sourcePath = __DIR__ .'/../../../../'. $resource;
            $destinationPath = base_path() .'/'. $resource;

            if ($filesystem->copy($sourcePath, $destinationPath)) {
                $this->line("<info>Resource file copied</info>: {$resource}");
            }
            else {
                $this->error($this->error);
            }
        }

        // generate keys
        $this->call('key:generate');

        $this->info('Run "bower install", "npm install" to download required components.');
    }

    /**
     * Install the package.
     */
    protected function installModule($name)
    {
        $filesystem = $this->laravel['files'];

        $module = $this->module->findOrFail($name);
        $name = $module->getLowerName();

        $default_locale = config('admin.modules.lang.default_locale');
        $sourcePath = $module->getExtraPath(implode('/', [config('admin.modules.lang.path'), $default_locale]));

        if (!$filesystem->isDirectory($sourcePath)) {
            return;
        }

        $destinationPath = $default_locale ?
            base_path("resources/lang/{$default_locale}") :
            base_path("resources/lang/{$name}");

        if (!$filesystem->isDirectory($destinationPath)) {
            $filesystem->makeDirectory($destinationPath, 0775, true);
        }

        if ($filesystem->copyDirectory($sourcePath, $destinationPath)) {
            $this->line("<info>Module language published</info>: {$module->getStudlyName()}");
        }
        else {
            $this->error($this->error);
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['module', 'm', InputOption::VALUE_REQUIRED, 'Indicates which module to install.'],
        ];
    }
}
