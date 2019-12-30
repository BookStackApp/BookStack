<?php

namespace Tests;

trait TestsApi
{

    protected $apiTokenId = 'apitoken';
    protected $apiTokenSecret = 'password';

    protected function errorResponse(string $messge, int $code)
    {
        return ["error" => ["code" => $code, "message" => $messge]];
    }

}