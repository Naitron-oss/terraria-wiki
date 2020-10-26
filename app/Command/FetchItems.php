<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchItems extends Command
{
    protected static $defaultName = 'fetch:items';

    protected function configure()
    {
        $this
        ->setDescription('Make items.txt')
        ->setHelp('This command allow you to make items.txt file contain list of items.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ProgressBar::setFormatDefinition('custom', "%percent%% [%bar%] %current%/%max% -- %message%");
        $items = explode("\n", file_get_contents('./items.txt'));
        $progress = new ProgressBar($output, count($items));
        $progress->setFormat('custom');

        $progress->setMessage('Mendapatkan data ...');
        $progress->start();
        foreach ($items as $item) {
            if (!file_exists("./data/items/$item.json")) {
                exec("./terraguide.php get:items \"$item\"");
            }
            $progress->advance();
            $progress->setMessage("<fg=cyan>$item</>");
        }
        $progress->finish();

        return Command::SUCCESS;
    }
}