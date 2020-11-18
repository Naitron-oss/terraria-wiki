<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Runner extends Command
{
    protected static $defaultName = 'runner';

    protected function configure()
    {
        $this
        ->setDescription('Run a batch of command.')
        ->addArgument('action', InputArgument::OPTIONAL, 'Which action? [items].');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');
        $app = $this->getApplication();

        if ($action == 'items') {
            $commands = ['dyes', 'fish', 'kites', 'statues', 'wings', 'torches', 'chests', 'strings', 'cages', 'items'];
            foreach ($commands as $type) {
                $command = $app->find("fetch:$type");
                $input = new ArrayInput([]);
                $command->run($input, $output);
            }
        } else {
            $output->writeln('No argument inputted.');
        }
        
        return Command::SUCCESS;
    }
}