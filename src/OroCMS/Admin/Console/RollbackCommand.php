<?php
namespace OroCMS\Admin\Console;

use Schema;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class RollbackCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin:rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the module migration.';

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
            return $this->rollbackModule($module_name);
        }
    }

    /**
     * Do module migration.
     */
    protected function rollbackModule($name)
    {
        $module = $this->module->findOrFail($name);

        // get migration path
        $path = $this->getPath($module);

        if ($path = realpath(base_path($path))) {
            // get migration files
            $fs = new FileSystem;
            $files = $fs->glob($path . '/*.php');

            // disable foreign key checks
            Schema::disableForeignKeyConstraints();

            foreach ($files as $file) {
                if (preg_match('/(\d{4}_\d{2}_\d{2}_\d{6}_\w+)\.php/', $file, $match)) {
                    $class = $this->getMigrationClass($file);
                    if (!$class) {
                        continue;
                    }

                    // remove migration
                    if (app()['db']->table('migrations')->where('migration', $match[1])->delete()) {
                        include $file;

                        $migration = new $class();
                        $migration->down();
                    }
                }
            }

            // re-enable
            Schema::enableForeignKeyConstraints();            

            $this->info(ucwords($name) . ' module rollback complete.');
        }
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
            ['module', 'm', InputOption::VALUE_REQUIRED, 'Indicates which module to migrate.'],
        ];
    }

    /**
     * Get declared classes.
     */
    private function getMigrationClass($file)
    {
        $fp = fopen($file, 'r');
        $class = null;

        $i = 0;
        $buffer = '';
        while (!$class) {
            if (feof($fp)) {
                break;
            }

            $buffer .= fread($fp, 1024);
            if (preg_match('/class\s+(\w+)(.*)\{?/', $buffer, $matches)) {
                $class = $matches[1];
                break;
            }
        }

        return $class;
    }
}
