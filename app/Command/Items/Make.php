<?php

namespace App\Command\Items;

use Goutte\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class Make extends Command
{
    protected static $defaultName = 'make:items';

    protected function configure()
    {
        $this
            ->setDescription('Make items.txt')
            ->setHelp('This command allow you to make items.txt file contain list of items.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ProgressBar::setFormatDefinition('custom', ' [%bar%] %percent%% -- %message%');
        $client = new Client();
        
        $output->writeln('Memuat data ...');
        $crawler = $client->request('GET', 'https://terraria.gamepedia.com/Item_IDs');
        
        $items = $crawler->filter('table.terraria.sortable.lined tr td a');
        $progress = new ProgressBar($output, $items->count());
        $progress->setFormat('custom');

        $items = $items->each(function (Crawler $node) {
            return $node->text();
        });

        $output->writeln('Menyiapkan [items.txt]');
        file_put_contents('./items.txt', '');

        $progress->start();
        $progress->setMessage('Mulai menulis file');
        foreach ($items as $key => $item) {
            $key += 1;
            file_put_contents('./items.txt', "$item\n", FILE_APPEND);
            $progress->advance();
            $progress->setMessage("Menambahkan [<fg=blue>$key</>] <fg=cyan>$item</>");
        }
        $progress->finish();

        $output->writeln("\n[<fg=green>Ok</>] [items.txt] berhasil dibuat.");
        $clean = preg_replace('/\n$/', '', file_get_contents('./items.txt'));
        file_put_contents('./items.txt', $clean);

        return Command::SUCCESS;
    }
}