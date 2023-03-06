#!/usr/bin/env php
<?php

if (php_sapi_name() !== 'cli') {
    exit;
}

require __DIR__ . '/vendor/autoload.php';

use Cli\Commands\BackupCommand;
use Cli\Commands\InitCommand;
use Cli\Commands\RestoreCommand;
use Cli\Commands\UpdateCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;

// Setup our CLI
$app = new Application('bookstack-system');
$app->setCatchExceptions(false);

$app->add(new BackupCommand());
$app->add(new UpdateCommand());
$app->add(new InitCommand());
$app->add(new RestoreCommand());

try {
    $app->run();
} catch (Exception $error) {
    $output = (new ConsoleOutput())->getErrorOutput();
    $output->getFormatter()->setStyle('error', new OutputFormatterStyle('red'));
    $output->writeln("<error>\nAn error occurred when attempting to run a command:\n</error>");
    $output->writeln($error->getMessage());
    exit(1);
}
