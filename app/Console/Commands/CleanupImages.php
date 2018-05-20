<?php

namespace BookStack\Console\Commands;

use BookStack\Services\ImageService;
use Illuminate\Console\Command;

class CleanupImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:cleanup-images
                            {--a|all : Include images that are used in page revisions}
                            {--f|force : Actually run the deletions}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup images and drawings';


    protected $imageService;

    /**
     * Create a new command instance.
     * @param ImageService $imageService
     */
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $checkRevisions = $this->option('all') ? false : true;
        $dryRun = $this->option('force') ? false : true;

        if (!$dryRun) {
            $proceed = $this->confirm('This operation is destructive and is not guaranteed to be fully accurate. Ensure you have a backup of your images. Are you sure you want to proceed?');
            if (!$proceed) {
                return;
            }
        }

        $deleteCount = $this->imageService->deleteUnusedImages($checkRevisions, ['gallery', 'drawio'], $dryRun);

        if ($dryRun) {
            $this->comment('Dry run, No images have been deleted');
            $this->comment($deleteCount . ' images found that would have been deleted');
            $this->comment('Run with -f or --force to perform deletions');
            return;
        }

        $this->comment($deleteCount . ' images deleted');
    }
}
