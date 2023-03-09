<?php

namespace Cli\Services;

use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class InteractiveConsole
{
    public function __construct(
        protected QuestionHelper $helper,
        protected InputInterface $input,
        protected OutputInterface $output,
    ) {
    }

    public function confirm(string $text): bool
    {
        $question = new ConfirmationQuestion($text . " (y/n)\n", false);
        return $this->helper->ask($this->input, $this->output, $question);
    }

    public function choice(string $question, array $answers)
    {
        $question = new ChoiceQuestion($question, $answers, $answers[0]);
        return $this->helper->ask($this->input, $this->output, $question);
    }
}
