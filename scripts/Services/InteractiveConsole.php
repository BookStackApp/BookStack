<?php

namespace Cli\Services;

use Illuminate\Console\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class InteractiveConsole
{


    public function __construct(
        protected QuestionHelper $helper,
        protected InputInterface $input,
        protected OutputInterface $output,
    )
    {
    }

    public function confirm(string $text): bool
    {
        $question = new ConfirmationQuestion($text, false);
        return $this->helper->ask($this->input, $this->output, $question);
    }
}