<?php

namespace BookStack\Console\Commands;

use BookStack\Services\PermissionService;
use Illuminate\Console\Command;

class RegeneratePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:regen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate all system permissions';

    /**
     * The service to handle the permission system.
     *
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * Create a new command instance.
     *
     * @param PermissionService $permissionService
     */
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->permissionService->buildJointPermissions();
    }
}
