<?php

namespace BookStack\Logging;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class CustomizeHourlyLogs
{
    public function __invoke(Logger $logger)
    {
        $hour = now()->format('Y-m-d_H'); // Format like 2025-07-08_14
        $path = storage_path("logs/hourly/hourly-{$hour}.log");

        $logger->setHandlers([
            new StreamHandler($path, Logger::DEBUG),
        ]);
    }
}

