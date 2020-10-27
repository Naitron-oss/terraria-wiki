<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakePage extends Command
{
    protected static $defaultName = 'make:page';

    protected function configure()
    {
        $this
        ->setDescription('Make indexing json file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ProgressBar::setFormatDefinition('custom', "%percent%% [%bar%] %current%/%max% -- %message%");
        $json = [];
        $page = 0;
        $limit = 20;
        
        $progress = new ProgressBar($output, count(glob('./data/items/*.json')));
        $progress->setMessage('Memulai');
        $progress->start();
        foreach (glob('./data/items/*.json') as $file) {
            $title = basename($file, '.json');
            $json[] = $title;
            if (count($json) >= $limit) {
                $page++;
                file_put_contents("./data/page/$page.json", json_encode($json, JSON_PRETTY_PRINT));
                $json = [];
                $progress->setMessage("[<fg=green>Ok</>] $page.json");
                $progress->advance();
            }
        }
        $progress->finish();
        $output->writeln("\n[<fg=green>Done</>] Generated <fg=yellow>$page</> pages.");

        file_put_contents('./data/page/pagination.json', json_encode([
            'pages' => $page,
            'limit' => $limit
        ], JSON_PRETTY_PRINT));
        $output->writeln("[<fg=green>Ok</>] pagination.json");

        $json = [];
        foreach (glob('./data/items/*.json') as $file) {
            $title = basename($file, '.json');
            $json[] = $title;
        };
        file_put_contents('./data/page/all.json', json_encode($json, JSON_PRETTY_PRINT));
        $output->writeln("[<fg=green>Ok</>] all.json");

        return Command::SUCCESS;
    }
}