<?php

namespace BookStack\Console\Commands;

use BookStack\Auth\Permissions\JointPermissionBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RegeneratePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:regenerate-permissions {--database= : The database connection to use.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate all system permissions';

    protected JointPermissionBuilder $permissionBuilder;

    /**
     * Create a new command instance.
     */
    public function __construct(JointPermissionBuilder $permissionBuilder)
    {
        $this->permissionBuilder = $permissionBuilder;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $connection = DB::getDefaultConnection();

        if ($this->hasOption('database')) {
            DB::setDefaultConnection($this->option('database'));
        }

        $this->permissionBuilder->buildJointPermissions();

        DB::setDefaultConnection($connection);
        $this->comment('Permissions regenerated');
    }
}
