<?php

namespace BookStack\Exceptions;

interface ShowsApiExceptionMessage
{
    /**
     * Get the message to be shown to the API user for this exception.
     * This is used for non-debug scenarios, so should not contain sensitive information.
     * The original exception message will be used in debug scenarios.
     */
    public function getMessageForApi(): string;
}
