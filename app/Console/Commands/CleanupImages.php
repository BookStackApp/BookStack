<?php

namespace BookStack\Console\Commands;

use BookStack\Uploads\ImageService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Output\OutputInterface;

class CleanupImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:cleanup-images
                            {--a|all : Also delete images that are only used in old revisions}
                            {--f|force : Actually run the deletions, Defaults to a dry-run}
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
     *
     * @param \BookStack\Uploads\ImageService $imageService
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
            $this->warn("This operation is destructive and is not guaranteed to be fully accurate.\nEnsure you have a backup of your images.\n");
            $proceed = $this->confirm("Are you sure you want to proceed?");
            if (!$proceed) {
                return;
            }
        }

        $deleted = $this->imageService->deleteUnusedImages($checkRevisions, $dryRun);
        $deleteCount = count($deleted);

        if ($dryRun) {
            $this->comment('Dry run, no images have been deleted');
            $this->comment($deleteCount . ' images found that would have been deleted');
            $this->showDeletedImages($deleted);
            $this->comment('Run with -f or --force to perform deletions');

            return;
        }

        $this->showDeletedImages($deleted);
        $this->comment($deleteCount . ' images deleted');
    }

    protected function showDeletedImages($paths)
    {
        if ($this->getOutput()->getVerbosity() <= OutputInterface::VERBOSITY_NORMAL) {
            return;
        }
        if (count($paths) > 0) {
            $this->line('Images to delete:');
        }
        foreach ($paths as $path) {
            $this->line($path);
        }
    }
}
