<?php

namespace BookStack\Console\Commands;

use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Tools\PermissionsUpdater;
use Illuminate\Console\Command;

class CopyShelfPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:copy-shelf-permissions
                            {--a|all : Perform for all shelves in the system}
                            {--s|slug= : The slug for a shelf to target}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy shelf permissions to all child books';

    /**
     * Execute the console command.
     */
    public function handle(PermissionsUpdater $permissionsUpdater): int
    {
        $shelfSlug = $this->option('slug');
        $cascadeAll = $this->option('all');
        $shelves = null;

        if (!$cascadeAll && !$shelfSlug) {
            $this->error('Either a --slug or --all option must be provided.');

            return 1;
        }

        if ($cascadeAll) {
            $continue = $this->confirm(
                'Permission settings for all shelves will be cascaded. ' .
                        'Books assigned to multiple shelves will receive only the permissions of it\'s last processed shelf. ' .
                        'Are you sure you want to proceed?'
            );

            if (!$continue && !$this->hasOption('no-interaction')) {
                return 0;
            }

            $shelves = Bookshelf::query()->get(['id']);
        }

        if ($shelfSlug) {
            $shelves = Bookshelf::query()->where('slug', '=', $shelfSlug)->get(['id']);
            if ($shelves->count() === 0) {
                $this->info('No shelves found with the given slug.');
            }
        }

        foreach ($shelves as $shelf) {
            $permissionsUpdater->updateBookPermissionsFromShelf($shelf, false);
            $this->info('Copied permissions for shelf [' . $shelf->id . ']');
        }

        $this->info('Permissions copied for ' . $shelves->count() . ' shelves.');
        return 0;
    }
}
