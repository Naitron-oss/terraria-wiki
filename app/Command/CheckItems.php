<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckItems extends Command
{
    protected static $defaultName = 'check:items';

    protected function configure()
    {
        $this
        ->setDescription('Check missing items.')
        ->setHelp('Checking missing items data where not listed in items.txt');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Mulai memeriksa ...');
        $items = explode("\n", file_get_contents('./items.txt'));

        $done = 0;
        $fail = 0;
        foreach ($items as $item) {
            $name = str_replace("'", '', $item);
            $name = str_replace("/", '_', $name);
            if (file_exists("./data/items/$name.json")) {
                $done++;
            } else {
                $output->writeln("[<fg=red>Fail</>] $item");
                $fail++;
            }
        }

        $output->writeln("Sukses: $done\nGagal: $fail");

        return Command::SUCCESS;
    }
}