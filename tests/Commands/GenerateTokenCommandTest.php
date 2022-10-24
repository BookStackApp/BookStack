<?php

namespace Tests\Commands;

use BookStack\Auth\User;
use BookStack\Api\ApiToken;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class GenerateTokenCommandTest extends TestCase
{
    public function test_standard_command_usage()
    {
      $this->artisan('bookstack:create-admin', [
          '--email'    => 'admintest@example.com',
          '--name'     => 'Admin Test',
          '--password' => 'testing-4',
      ])->assertExitCode(0);

      $this->artisan('bookstack:generate-token', [
          '--email'    => 'admintest@example.com',
          '--name'     => 'TokenName'
      ])->assertExitCode(0);

      $token = ApiToken::query()->where('name', '=', 'TokenName')->first();
      $this->assertNotEmpty($token->token_id);
      $this->assertNotEmpty($token->id);
      $this->assertNotEmpty($token->secret);
      $this->assertEquals("TokenName", $token->name);

    }

    public function test_standard_errors()
    {
      $this->artisan('bookstack:create-admin', [
          '--email'    => 'admintest@example.com',
          '--name'     => 'Admin Test',
          '--password' => 'testing-4',
      ])->assertExitCode(0);

      $this->artisan('bookstack:generate-token', [
          '--email'    => 'boby@example.com',
          '--name'     => 'TokenName'
      ])->assertExitCode(1);

      $token = ApiToken::query()->where('name', '=', 'TokenName')->first();
      $this->assertNull($token);
    }
}
