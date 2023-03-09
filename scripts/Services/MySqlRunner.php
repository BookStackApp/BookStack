<?php

namespace Cli\Services;

use Exception;

class MySqlRunner
{
    public function __construct(
        protected string $host,
        protected string $user,
        protected string $password,
        protected string $database,
        protected int $port = 3306
    ) {
    }

    /**
     * @throws Exception
     */
    public function ensureOptionsSet(): void
    {
        $options = ['host', 'user', 'password', 'database'];
        foreach ($options as $option) {
            if (!$this->$option) {
                throw new Exception("Could not find a valid value for the \"{$option}\" database option.");
            }
        }
    }

    public function testConnection(): bool
    {
        $output = (new ProgramRunner('mysql', '/usr/bin/mysql'))
            ->withTimeout(240)
            ->withIdleTimeout(5)
            ->runCapturingStdErr([
                '-h', $this->host,
                '-P', $this->port,
                '-u', $this->user,
                '-p' . $this->password,
                $this->database,
                '-e', "show tables;"
            ]);

        return !$output;
    }

    public function importSqlFile(string $sqlFilePath): void
    {
        $output = (new ProgramRunner('mysql', '/usr/bin/mysql'))
            ->withTimeout(240)
            ->withIdleTimeout(5)
            ->runCapturingStdErr([
                '-h', $this->host,
                '-P', $this->port,
                '-u', $this->user,
                '-p' . $this->password,
                $this->database,
                '<', $sqlFilePath
            ]);

        if ($output) {
            throw new Exception("Failed mysql file import with errors:\n" . $output);
        }
    }

    public function runDumpToFile(string $filePath): void
    {
        $file = fopen($filePath, 'w');
        $errors = "";
        $hasOutput = false;

        try {
            (new ProgramRunner('mysqldump', '/usr/bin/mysqldump'))
                ->withTimeout(240)
                ->withIdleTimeout(15)
                ->runWithoutOutputCallbacks([
                    '-h', $this->host,
                    '-P', $this->port,
                    '-u', $this->user,
                    '-p' . $this->password,
                    '--single-transaction',
                    '--no-tablespaces',
                    $this->database,
                ], function ($data) use (&$file, &$hasOutput) {
                    fwrite($file, $data);
                    $hasOutput = true;
                }, function ($error) use (&$errors) {
                    $errors .= $error . "\n";
                });
        } catch (\Exception $exception) {
            fclose($file);
            if ($exception instanceof ProcessTimedOutException) {
                if (!$hasOutput) {
                    throw new Exception("mysqldump operation timed-out.\nNo data has been received so the connection to your database may have failed.");
                } else {
                    throw new Exception("mysqldump operation timed-out after data was received.");
                }
            }
            throw new Exception($exception->getMessage());
        }

        fclose($file);

        if ($errors) {
            throw new Exception("Failed mysqldump with errors:\n" . $errors);
        }
    }

    public static function fromEnvOptions(array $env): static
    {
        $host = ($env['DB_HOST'] ?? '');
        $username = ($env['DB_USERNAME'] ?? '');
        $password = ($env['DB_PASSWORD'] ?? '');
        $database = ($env['DB_DATABASE'] ?? '');
        $port = intval($env['DB_PORT'] ?? 3306);

        return new static($host, $username, $password, $database, $port);
    }
}