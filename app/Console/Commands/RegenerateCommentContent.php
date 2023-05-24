<?php

namespace BookStack\Console\Commands;

use BookStack\Activity\CommentRepo;
use BookStack\Activity\Models\Comment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RegenerateCommentContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:regenerate-comment-content
                            {--database= : The database connection to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate the stored HTML of all comments';

    /**
     * Execute the console command.
     */
    public function handle(CommentRepo $commentRepo): int
    {
        $connection = DB::getDefaultConnection();
        if ($this->option('database') !== null) {
            DB::setDefaultConnection($this->option('database'));
        }

        Comment::query()->chunk(100, function ($comments) use ($commentRepo) {
            foreach ($comments as $comment) {
                $comment->html = $commentRepo->commentToHtml($comment->text);
                $comment->save();
            }
        });

        DB::setDefaultConnection($connection);
        $this->comment('Comment HTML content has been regenerated');

        return 0;
    }
}
