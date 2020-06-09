<?php

namespace BookStack\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Connection;

class UpdateUrl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:update-url
                            {oldUrl : URL to replace}
                            {newUrl : URL to use as the replacement}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find and replace the given URLs in your BookStack database';

    protected $db;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $oldUrl = str_replace("'", '', $this->argument('oldUrl'));
        $newUrl = str_replace("'", '', $this->argument('newUrl'));

        $urlPattern = '/https?:\/\/(.+)/';
        if (!preg_match($urlPattern, $oldUrl) || !preg_match($urlPattern, $newUrl)) {
            $this->error("The given urls are expected to be full urls starting with http:// or https://");
            return 1;
        }

        if (!$this->checkUserOkayToProceed($oldUrl, $newUrl)) {
            return 1;
        }

        $columnsToUpdateByTable = [
            "attachments" => ["path"],
            "pages" => ["html", "text", "markdown"],
            "images" => ["url"],
            "comments" => ["html", "text"],
        ];

        foreach ($columnsToUpdateByTable as $table => $columns) {
            foreach ($columns as $column) {
                $changeCount = $this->db->table($table)->update([
                    $column => $this->db->raw("REPLACE({$column}, '{$oldUrl}', '{$newUrl}')")
                ]);
                $this->info("Updated {$changeCount} rows in {$table}->{$column}");
            }
        }

        $this->info("URL update procedure complete.");
        return 0;
    }

    /**
     * Warn the user of the dangers of this operation.
     * Returns a boolean indicating if they've accepted the warnings.
     */
    protected function checkUserOkayToProceed(string $oldUrl, string $newUrl): bool
    {
        $dangerWarning = "This will search for \"{$oldUrl}\" in your database and replace it with  \"{$newUrl}\".\n";
        $dangerWarning .= "Are you sure you want to proceed?";
        $backupConfirmation = "This operation could cause issues if used incorrectly. Have you made a backup of your existing database?";

        return $this->confirm($dangerWarning) && $this->confirm($backupConfirmation);
    }
}
