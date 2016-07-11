<?php
namespace OroCMS\Admin\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MigrationCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a module migration or copy migration files to the application.';

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
            return $this->migrateModule($module_name);
        }

        // do migration copy
        $path = realpath(__DIR__.'/../../../../src/migrations/');

        $this->laravel['files']->copyDirectory(
            $path,
            $this->laravel['path.database'].'/migrations/'
        );

        $this->info('Migrations published successfully.');
    }

    /**
     * Do module migration.
     */
    protected function migrateModule($name)
    {
        $module = $this->module->findOrFail($name);

        $this->call('migrate', [
            '--path' => $this->getPath($module),
            '--database' => $this->option('database'),
        ]);

        if ($this->option('seed')) {
            $this->call('module:seed', ['module' => $name]);
        }

        $this->info(ucwords($name) . ' module migration successful.');
    }

    /**
     * Get migration path for specific module.
     *
     * @param  \OroCMS\Admin\Module $module
     * @return string
     */
    protected function getPath($module)
    {
        $path = $module->getExtraPath(config('admin.modules.migration.path'));

        return str_replace(base_path(), '', $path);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['module', 'm', InputOption::VALUE_REQUIRED, 'Indicates which module to migrate.'],
            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run.'],
        ];
    }
}
