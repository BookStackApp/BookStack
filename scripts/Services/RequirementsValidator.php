<?php

namespace Cli\Services;

use Exception;

class RequirementsValidator
{
    /**
     * Ensure the required PHP extensions are installed for this command.
     * @throws Exception
     */
    public static function validate(): void
    {
        $errors = [];

        if (version_compare(PHP_VERSION, '8.0.2') < 0) {
            $errors[] = "PHP >= 8.0.2 is required to install BookStack.";
        }

        $requiredExtensions = ['bcmath', 'curl', 'gd', 'iconv', 'libxml', 'mbstring', 'mysqlnd', 'xml'];
        foreach ($requiredExtensions as $extension) {
            if (!extension_loaded($extension)) {
                $errors[] = "The \"{$extension}\" PHP extension is required by not active.";
            }
        }

        try {
            (new ProgramRunner('git', '/usr/bin/git'))->ensureFound();
            (new ProgramRunner('php', '/usr/bin/php'))->ensureFound();
        } catch (Exception $exception) {
            $errors[] = $exception->getMessage();
        }

        if (count($errors) > 0) {
            throw new Exception("Requirements failed with following errors:\n" . implode("\n", $errors));
        }
    }
}