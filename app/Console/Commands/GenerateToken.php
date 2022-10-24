<?php

namespace BookStack\Console\Commands;

use BookStack\Auth\UserRepo;
use BookStack\Auth\User;
use BookStack\Api\ApiToken;
use BookStack\Exceptions\NotFoundException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class GenerateToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:generate-token
                            {--name= : The name of the token}
                            {--email= : The name of the targeted user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate token for a targeted user';

    protected $userRepo;

    /**
     * Create a new command instance.
     */
    public function __construct(UserRepo $userRepo)
    {
        $this->userRepo = $userRepo;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws NotFoundException
     *
     * @return mixed
     */
    public function handle()
    {
        $details = $this->snakeCaseOptions();

        if (empty($details['name'])) {
            $details['name'] = $this->ask('Please specify the name of the token');
        }
        if (empty($details['email'])) {
            $details['email'] = $this->ask('Please specify the email to generate token');
        }

        $validator = Validator::make($details, [
            'name'             => ['required', 'min:2'],
            'email'            => ['required', 'email', 'min:5']
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return SymfonyCommand::FAILURE;
        }
        $user = $this->userRepo->getByEmail($details['email']);

        if ($user == null){
          $email = $details['email'];
          $this->info("The email '\"{$email}\"' doesnt exists");
          return SymfonyCommand::FAILURE;
        }
        $token = $this->prepareToken($details['name'], $user);

        $token->save();
        $this->info("{$token->id}:{$token->secret}");
        return SymfonyCommand::SUCCESS;
    }

    private function prepareToken($tokenName, $user): ApiToken
    {
      $secret = Str::random(32);
      $token = (new ApiToken())->forceFill([
          'name'       => $tokenName,
          'token_id'   => Str::random(32),
          'secret'     => Hash::make($secret),
          'user_id'    => $user->id,
          'expires_at' => ApiToken::defaultExpiry()
      ]);
      return $token;
    }


    protected function snakeCaseOptions(): array
    {
        $returnOpts = [];
        foreach ($this->options() as $key => $value) {
            $returnOpts[str_replace('-', '_', $key)] = $value;
        }
        return $returnOpts;
    }
}
