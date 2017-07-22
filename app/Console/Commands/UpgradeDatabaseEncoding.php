<?php

namespace BookStack\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpgradeDatabaseEncoding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:db-utf8mb4 {--database= : The database connection to use.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate SQL commands to upgrade the database to UTF8mb4';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $connection = DB::getDefaultConnection();
        if ($this->option('database') !== null) {
            DB::setDefaultConnection($this->option('database'));
        }

        $database = DB::getDatabaseName();
        $tables = DB::select('SHOW TABLES');
        $this->line('ALTER DATABASE `'.$database.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
        $this->line('USE `'.$database.'`;');
        $key = 'Tables_in_' . $database;
        foreach ($tables as $table) {
            $tableName = $table->$key;
            $this->line('ALTER TABLE `'.$tableName.'` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
        }

        DB::setDefaultConnection($connection);
    }
}
