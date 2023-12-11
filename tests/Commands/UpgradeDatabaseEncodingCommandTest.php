<?php

namespace Tests\Commands;

use Tests\TestCase;

class UpgradeDatabaseEncodingCommandTest extends TestCase
{
    public function test_command_outputs_sql()
    {
        $this->artisan('bookstack:db-utf8mb4')
            ->expectsOutputToContain('ALTER DATABASE')
            ->expectsOutputToContain('ALTER TABLE `users` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
    }
}
