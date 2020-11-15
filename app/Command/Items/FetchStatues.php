<?php

namespace App\Command\Items;

use app\Command\Items\Get as GetItems;
use Goutte\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchStatues extends Command
{
    protected static $defaultName = 'fetch:statues';

    public function configure()
    {
        $this
        ->setDescription('Fetch data of statues type item');
    }

    public function getLists(OutputInterface $output) : array
    {
        $output->writeln('Mendapatkan data ...');
        $client = new Client();
        $crawler = $client->request('GET', 'https://terraria.gamepedia.com/Statues');

        $table = $crawler->filter('td:nth-child(1) .i.-w > span > span')->each(function ($node, $i) {
            return $node->text();
        });

        $itemlist = $crawler->filter('.itemlist .i.-w > span > span')->each(function ($node, $i) {
            return $node->text();
        });

        $output->writeln('Data berhasil diunduh.');
        return [...$table, ...$itemlist];
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $get = new GetItems('Statues');
        $crawler = $get->getCrawler('Statues');
        $get->getTitle($crawler);
        $get->getInfo($crawler);
        $get->getStat($crawler);
        $headline = $get->getCraft($crawler, 'table.terraria');
        $get->json['craft'] = array_splice($get->json['craft'], 0, 5);

        // Decorative statue
        $dec = new GetItems('Statues');
        $crawler = $dec->getCrawler('Statues');
        $headline2 = $dec->getCraft($crawler, '.itemlist.terraria');
        $get->json['craft'] = [...$get->json['craft'], ...$dec->json['craft']];
        $get->saveJson('_Statues');

        foreach ($this->getLists($output) as $item) {
            if ($item) {
                $name = str_replace("'", '', $item);
                $name = str_replace("/", '_', $name);
                $get->json = ['refer' => '_Statues'];
                $get->saveJson($name);
                $output->writeln("[<fg=green>Ok</>] $name.json ($headline, $headline2)");
            }
        }

        return Command::SUCCESS;
    }
}