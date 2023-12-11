<?php

namespace BookStack\Console\Commands;

use BookStack\Uploads\ImageService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Output\OutputInterface;

class CleanupImagesCommand extends Command
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

    /**
     * Execute the console command.
     */
    public function handle(ImageService $imageService): int
    {
        $checkRevisions = !$this->option('all');
        $dryRun = !$this->option('force');

        if (!$dryRun) {
            $this->warn("This operation is destructive and is not guaranteed to be fully accurate.\nEnsure you have a backup of your images.\n");
            $proceed = !$this->input->isInteractive() || $this->confirm("Are you sure you want to proceed?");
            if (!$proceed) {
                return 0;
            }
        }

        $deleted = $imageService->deleteUnusedImages($checkRevisions, $dryRun);
        $deleteCount = count($deleted);

        if ($dryRun) {
            $this->comment('Dry run, no images have been deleted');
            $this->comment($deleteCount . ' image(s) found that would have been deleted');
            $this->showDeletedImages($deleted);
            $this->comment('Run with -f or --force to perform deletions');

            return 0;
        }

        $this->showDeletedImages($deleted);
        $this->comment("{$deleteCount} image(s) deleted");

        return 0;
    }

    protected function showDeletedImages($paths): void
    {
        if ($this->getOutput()->getVerbosity() <= OutputInterface::VERBOSITY_NORMAL) {
            return;
        }

        if (count($paths) > 0) {
            $this->line('Image(s) to delete:');
        }

        foreach ($paths as $path) {
            $this->line($path);
        }
    }
}
