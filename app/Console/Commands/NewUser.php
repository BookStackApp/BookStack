<?php

namespace BookStack\Console\Commands;

use Illuminate\Console\Command;
use BookStack\User;

class NewUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newuser {name} {email} {avatar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

    /**
     * Create a new command instance.
     *
     * @return void
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
    public function handle() {
    	
    	User::create([
    			'name' => $this->argument('name'),
    			'email' => $this->argument('email'),
    			'password' => bcrypt(str_random(40)),
    	]);
        
    }
}
