<?php

namespace App\Command\Items;

use app\Command\Items\Get as GetItems;
use Goutte\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchWings extends Command
{
    protected static $defaultName = 'fetch:wings';

    public function configure()
    {
        $this
        ->setDescription('Fetch data of kite\'s type item');
    }

    public function getLists(OutputInterface $output) : array
    {
        $output->writeln('Mendapatkan data ...');
        $client = new Client();
        $crawler = $client->request('GET', 'https://terraria.gamepedia.com/Wings');

        $list = $crawler->filter('tr td.il2c > span > span > span')->each(function ($node, $i) {
            return $node->text();
        });

        $output->writeln('Data berhasil diunduh.');
        return $list;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $get = new GetItems('Wings');
        $crawler = $get->getCrawler('Wings');
        $get->getTitle($crawler);
        $get->getInfo($crawler);
        $get->getStat($crawler);
        $get->getCraft($crawler, 'table.terraria');
        $get->json['craft'] = array_splice($get->json['craft'], 0, 1);

        foreach ($this->getLists($output) as $item) {
            if (strlen($item) > 3) {
                $name = str_replace("'", '', $item);
                $name = str_replace("/", '_', $name);
                $get->json['craft'][0]['title'] = 'Type';
                $get->saveJson("$name");
                $output->writeln("[<fg=green>Ok</>] $name.json (Type)");
            }
        }

        return Command::SUCCESS;
    }
}