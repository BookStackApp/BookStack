<?php

namespace Illuminate\Foundation\Console;

use Closure;
use ReflectionFunction;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClosureCommand extends Command
{
    /**
     * The command callback.
     *
     * @var \Closure
     */
    protected $callback;

    /**
     * Create a new command instance.
     *
     * @param  string  $signature
     * @param  Closure  $callback
     * @return void
     */
    public function __construct($signature, Closure $callback)
    {
        $this->callback = $callback;
        $this->signature = $signature;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputs = array_merge($input->getArguments(), $input->getOptions());

        $parameters = [];

        foreach ((new ReflectionFunction($this->callback))->getParameters() as $parameter) {
            if (isset($inputs[$parameter->name])) {
                $parameters[$parameter->name] = $inputs[$parameter->name];
            }
        }

        return $this->laravel->call(
            $this->callback->bindTo($this, $this), $parameters
        );
    }

    /**
     * Set the description for the command.
     *
     * @param  string  $description
     * @return $this
     */
    public function describe($description)
    {
        $this->setDescription($description);

        return $this;
    }
}
