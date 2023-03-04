<?php

namespace Cli\Services;

use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class ProgramRunner
{
    protected int $timeout = 240;
    protected int $idleTimeout = 15;
    protected array $environment = [];

    public function __construct(
        protected string $program,
        protected string $defaultPath
    ) {
    }

    public function withTimeout(int $timeoutSeconds): static
    {
        $this->timeout = $timeoutSeconds;
        return $this;
    }

    public function withIdleTimeout(int $idleTimeoutSeconds): static
    {
        $this->idleTimeout = $idleTimeoutSeconds;
        return $this;
    }

    public function withEnvironment(array $environment): static
    {
        $this->environment = $environment;
        return $this;
    }

    public function runCapturingAllOutput(array $args): string
    {
        $output = '';
        $callable = function ($data) use (&$output) {
            $output .= $data . "\n";
        };

        $this->runWithoutOutputCallbacks($args, $callable, $callable);
        return $output;
    }

    public function runCapturingStdErr(array $args): string
    {
        $err = '';
        $this->runWithoutOutputCallbacks($args, fn() => '', function ($data) use (&$err) {
            $err .= $data . "\n";
        });
        return $err;
    }

    public function runWithoutOutputCallbacks(array $args, callable $stdOutCallback, callable $stdErrCallback): void
    {
        $process = $this->startProcess($args);
        foreach ($process as $type => $data) {
            if ($type === $process::ERR) {
                $stdErrCallback($data);
            } else {
                $stdOutCallback($data);
            }
        }
    }

    protected function startProcess(array $args): Process
    {
        $programPath = $this->resolveProgramPath();
        $process = new Process([$programPath, ...$args], null, $this->environment);
        $process->setTimeout($this->timeout);
        $process->setIdleTimeout($this->idleTimeout);
        $process->start();
        return $process;
    }

    protected function resolveProgramPath(): string
    {
        $executableFinder = new ExecutableFinder();
        $path = $executableFinder->find($this->program, $this->defaultPath);

        if (is_null($path) || !is_file($path)) {
            throw new \Exception("Could not locate \"{$this->program}\" program.");
        }

        return $path;
    }
}
