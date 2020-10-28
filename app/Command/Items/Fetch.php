<?php

namespace App\Command\Items;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Fetch extends Command
{
    protected static $defaultName = 'fetch:items';

    protected function configure()
    {
        $this
        ->addOption('update', 'u', InputOption::VALUE_NONE, 'Update existing data')
        ->setDescription('Make items.txt')
        ->setHelp('This command allow you to make items.txt file contain list of items.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ProgressBar::setFormatDefinition('custom', "%percent%% [%bar%] %current%/%max% -- %message%");
        $items = explode("\n", file_get_contents('./items.txt'));
        $update = $input->getOption('update');
        $progress = new ProgressBar($output, count($items));
        $progress->setFormat('custom');

        $progress->setMessage('Mendapatkan data ...');
        $progress->start();
        foreach ($items as $item) {
            $file = str_replace("'", '', $item);
            $file = str_replace("/", '_', $file);
            if (!file_exists("./data/items/$file.json") || $update) {
                exec("./terraguide.php get:items \"$item\"");
            }
            $progress->advance();
            $progress->setMessage("<fg=cyan>$item</>");
        }
        $progress->setMessage("<fg=green>Complete!</>\n");
        $progress->finish();

        return Command::SUCCESS;
    }
}