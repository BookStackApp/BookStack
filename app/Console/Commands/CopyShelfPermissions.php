<?php

namespace BookStack\Console\Commands;

use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Repos\BookshelfRepo;
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
     * @var BookshelfRepo
     */
    protected $bookshelfRepo;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(BookshelfRepo $repo)
    {
        $this->bookshelfRepo = $repo;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $shelfSlug = $this->option('slug');
        $cascadeAll = $this->option('all');
        $shelves = null;

        if (!$cascadeAll && !$shelfSlug) {
            $this->error('Either a --slug or --all option must be provided.');
            return;
        }

        if ($cascadeAll) {
            $continue = $this->confirm(
                'Permission settings for all shelves will be cascaded. '.
                        'Books assigned to multiple shelves will receive only the permissions of it\'s last processed shelf. '.
                        'Are you sure you want to proceed?'
            );

            if (!$continue && !$this->hasOption('no-interaction')) {
                return;
            }

            $shelves = Bookshelf::query()->get(['id', 'restricted']);
        }

        if ($shelfSlug) {
            $shelves = Bookshelf::query()->where('slug', '=', $shelfSlug)->get(['id', 'restricted']);
            if ($shelves->count() === 0) {
                $this->info('No shelves found with the given slug.');
            }
        }

        foreach ($shelves as $shelf) {
            $this->bookshelfRepo->copyDownPermissions($shelf, false);
            $this->info('Copied permissions for shelf [' . $shelf->id . ']');
        }

        $this->info('Permissions copied for ' . $shelves->count() . ' shelves.');
    }
}
