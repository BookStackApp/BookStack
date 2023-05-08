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
                            {newUrl : URL to use as the replacement}
                            {--force : Force the operation to run, ignoring confirmations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find and replace the given URLs in your BookStack database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Connection $db)
    {
        $oldUrl = str_replace("'", '', $this->argument('oldUrl'));
        $newUrl = str_replace("'", '', $this->argument('newUrl'));

        $urlPattern = '/https?:\/\/(.+)/';
        if (!preg_match($urlPattern, $oldUrl) || !preg_match($urlPattern, $newUrl)) {
            $this->error('The given urls are expected to be full urls starting with http:// or https://');

            return 1;
        }

        if (!$this->checkUserOkayToProceed($oldUrl, $newUrl)) {
            return 1;
        }

        $columnsToUpdateByTable = [
            'attachments' => ['path'],
            'pages'       => ['html', 'text', 'markdown'],
            'images'      => ['url'],
            'settings'    => ['value'],
            'comments'    => ['html', 'text'],
        ];

        foreach ($columnsToUpdateByTable as $table => $columns) {
            foreach ($columns as $column) {
                $changeCount = $this->replaceValueInTable($db, $table, $column, $oldUrl, $newUrl);
                $this->info("Updated {$changeCount} rows in {$table}->{$column}");
            }
        }

        $jsonColumnsToUpdateByTable = [
            'settings' => ['value'],
        ];

        foreach ($jsonColumnsToUpdateByTable as $table => $columns) {
            foreach ($columns as $column) {
                $oldJson = trim(json_encode($oldUrl), '"');
                $newJson = trim(json_encode($newUrl), '"');
                $changeCount = $this->replaceValueInTable($db, $table, $column, $oldJson, $newJson);
                $this->info("Updated {$changeCount} JSON encoded rows in {$table}->{$column}");
            }
        }

        $this->info('URL update procedure complete.');
        $this->info('============================================================================');
        $this->info('Be sure to run "php artisan cache:clear" to clear any old URLs in the cache.');
        $this->info('============================================================================');

        return 0;
    }

    /**
     * Perform a find+replace operations in the provided table and column.
     * Returns the count of rows changed.
     */
    protected function replaceValueInTable(
        Connection $db,
        string $table,
        string $column,
        string $oldUrl,
        string $newUrl
    ): int {
        $oldQuoted = $db->getPdo()->quote($oldUrl);
        $newQuoted = $db->getPdo()->quote($newUrl);

        return $db->table($table)->update([
            $column => $db->raw("REPLACE({$column}, {$oldQuoted}, {$newQuoted})"),
        ]);
    }

    /**
     * Warn the user of the dangers of this operation.
     * Returns a boolean indicating if they've accepted the warnings.
     */
    protected function checkUserOkayToProceed(string $oldUrl, string $newUrl): bool
    {
        if ($this->option('force')) {
            return true;
        }

        $dangerWarning = "This will search for \"{$oldUrl}\" in your database and replace it with  \"{$newUrl}\".\n";
        $dangerWarning .= 'Are you sure you want to proceed?';
        $backupConfirmation = 'This operation could cause issues if used incorrectly. Have you made a backup of your existing database?';

        return $this->confirm($dangerWarning) && $this->confirm($backupConfirmation);
    }
}
