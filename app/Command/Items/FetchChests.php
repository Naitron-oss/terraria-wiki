<?php

namespace App\Command\Items;

use app\Command\Items\Get as GetItems;
use Goutte\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class FetchChests extends Command
{
    protected static $defaultName = 'fetch:chests';

    public function configure()
    {
        $this
        ->setDescription('Fetch data of chests type item');
    }

    public function getNormalLists(OutputInterface $output) : array
    {
        $output->writeln('Mendapatkan data normal chest ...');
        $client = new Client();
        $crawler = $client->request('GET', 'https://terraria.gamepedia.com/Chests');
        
        // Type
        $html = '';
        $html .= $crawler->filter('.terraria')->eq(0)->html();
        $html .= $crawler->filter('.terraria')->eq(1)->html();
        $html .= $crawler->filter('.crafts')->eq(0)->html();

        $crawler = new Crawler($html);

        $list = $crawler->filter('tr > td .i.-w span > span')->each(function (Crawler $node) {
            return $node->text();
        });

        $output->writeln('Data berhasil diunduh.');
        return $list;
    }

    public function getTrappedLists(OutputInterface $output) : array
    {
        $output->writeln('Mendapatkan data trapped chest ...');
        $client = new Client();
        $crawler = $client->request('GET', 'https://terraria.gamepedia.com/Trapped_Chests');
        
        $list = $crawler->filter('.crafts tr > td .i.-w span > span')->each(function (Crawler $node) {
            return $node->text();
        });

        $output->writeln('Data berhasil diunduh.');
        return $list;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Normal chest
        $get = new GetItems('Chests');
        $crawler = $get->getCrawler('Chests');
        $get->getTitle($crawler);
        $get->getInfo($crawler, [1,2,3,4,5]);
        $get->getStat($crawler);
        $headline = $get->getCraft($crawler, '.crafts');
        $get->saveJson("_Chests");

        foreach ($this->getNormalLists($output) as $item) {
            if (strlen($item) > 3) {
                $name = str_replace("'", '', $item);
                $name = str_replace("/", '_', $name);
                $get->json = ['refer' => '_Chests'];
                $get->saveJson($name);
                $output->writeln("[<fg=green>Ok</>] $name.json ($headline)");
            }
        }

        // Trapped Chest
        $get = new GetItems('Trapped_Chests');
        $crawler = $get->getCrawler('Trapped_Chests');
        $get->getTitle($crawler);
        $get->getInfo($crawler, [0,1]);
        $get->getStat($crawler);
        $headline = $get->getCraft($crawler, '.crafts');
        $get->saveJson("_TrappedChests");

        foreach ($this->getTrappedLists($output) as $item) {
            if (strlen($item) > 3) {
                $name = str_replace("'", '', $item);
                $name = str_replace("/", '_', $name);
                $get->json = ['refer' => '_TrappedChests'];
                $get->saveJson($name);
                $output->writeln("[<fg=green>Ok</>] $name.json ($headline)");
            }
        }

        return Command::SUCCESS;
    }
}