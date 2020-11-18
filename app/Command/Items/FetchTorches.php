<?php

namespace App\Command\Items;

use app\Command\Items\Get as GetItems;
use Goutte\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchTorches extends Command
{
    protected static $defaultName = 'fetch:torches';

    public function configure()
    {
        $this
        ->setDescription('Fetch data of torches type item');
    }

    public function getLists(OutputInterface $output) : array
    {
        $output->writeln('Mendapatkan data ...');
        $client = new Client();
        $crawler = $client->request('GET', 'https://terraria.gamepedia.com/Torches');

        $list = $crawler->filter('tr td.il2c > span > span > span')->each(function ($node, $i) {
            return $node->text();
        });

        $output->writeln('Data berhasil diunduh.');
        return $list;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $get = new GetItems('Torches');
        $crawler = $get->getCrawler('Torches');
        $get->getTitle($crawler);
        $get->getInfo($crawler, [1,2,3,4]);
        $get->getStat($crawler);
        $get->getCraft($crawler, '.crafts');
        $get->saveJson("_Torches");

        foreach ($this->getLists($output) as $item) {
            if (strlen($item) > 3) {
                $name = str_replace("'", '', $item);
                $name = str_replace("/", '_', $name);
                $get->json = ['refer' => '_Torches'];
                $get->saveJson($name);
                $output->writeln("[<fg=green>Ok</>] $name.json (Type)");
            }
        }

        return Command::SUCCESS;
    }
}