<?php

namespace BookStack\Console\Commands;

use BookStack\Services\RestrictionService;
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
     * @var RestrictionService
     */
    protected $restrictionService;

    /**
     * Create a new command instance.
     *
     * @param RestrictionService $restrictionService
     */
    public function __construct(RestrictionService $restrictionService)
    {
        $this->restrictionService = $restrictionService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->restrictionService->buildEntityPermissions();
    }
}
