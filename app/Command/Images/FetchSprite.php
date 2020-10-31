<?php

namespace App\Command\Images;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchSprite extends Command
{
    protected static $defaultName = 'fetch:sprite';

    protected function configure()
    {
        $this
        ->setDescription('Fetch each items image sprite.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $items = explode("\n", file_get_contents('./items.txt'));
        
        foreach ($items as $item) {
            $slug = str_replace(' ', '_', urlencode($item));
            exec("./terraguide.php get:images '$slug' .infobox");
            $output->writeln("[<fg=green>Ok</>] $item");
        }

        return Command::SUCCESS;
    }
}