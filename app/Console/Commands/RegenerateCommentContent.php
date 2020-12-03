<?php

namespace BookStack\Console\Commands;

use BookStack\Actions\Comment;
use BookStack\Actions\CommentRepo;
use Illuminate\Console\Command;

class RegenerateCommentContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:regenerate-comment-content {--database= : The database connection to use.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate the stored HTML of all comments';

    /**
     * @var CommentRepo
     */
    protected $commentRepo;

    /**
     * Create a new command instance.
     */
    public function __construct(CommentRepo $commentRepo)
    {
        $this->commentRepo = $commentRepo;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $connection = \DB::getDefaultConnection();
        if ($this->option('database') !== null) {
            \DB::setDefaultConnection($this->option('database'));
        }

        Comment::query()->chunk(100, function ($comments) {
            foreach ($comments as $comment) {
                $comment->html = $this->commentRepo->commentToHtml($comment->text);
                $comment->save();
            }
        });

        \DB::setDefaultConnection($connection);
        $this->comment('Comment HTML content has been regenerated');
    }
}
