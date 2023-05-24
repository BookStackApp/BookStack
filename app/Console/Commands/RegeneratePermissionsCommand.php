<?php

namespace BookStack\Console\Commands;

use BookStack\Permissions\JointPermissionBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RegeneratePermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:regenerate-permissions 
                            {--database= : The database connection to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate all system permissions';

    /**
     * Execute the console command.
     */
    public function handle(JointPermissionBuilder $permissionBuilder): int
    {
        $connection = DB::getDefaultConnection();

        if ($this->option('database')) {
            DB::setDefaultConnection($this->option('database'));
        }

        $permissionBuilder->rebuildForAll();

        DB::setDefaultConnection($connection);
        $this->comment('Permissions regenerated');

        return 0;
    }
}
