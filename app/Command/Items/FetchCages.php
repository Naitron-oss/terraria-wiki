<?php

namespace App\Command\Items;

use app\Command\Items\Get as GetItems;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchCages extends Command
{
    protected static $defaultName = 'fetch:cages';

    public function configure()
    {
        $this
        ->setDescription('Fetch data of cages type item');
    }

    public function getLists() : array
    {
        $items = file_get_contents('items.txt');
        $items = explode("\n", $items);
        
        $list = [];
        foreach ($items as $item) {
            // posisi harus lebih dari 0++
            if (strpos($item, 'Cage') > 1) {
                $list[] = $item;
            }
        }

        return $list;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $get = new GetItems('Cages');
        $crawler = $get->getCrawler('Cages');
        $get->getTitle($crawler);
        $get->getInfo($crawler);
        $get->getStat($crawler);
        $headline = $get->getCraft($crawler, '.crafts');
        $get->saveJson('_Cages');

        foreach ($this->getLists($output) as $item) {
            if ($item) {
                $name = str_replace("'", '', $item);
                $name = str_replace("/", '_', $name);
                $get->json = ['refer' => '_Cages'];
                $get->saveJson($name);
                $output->writeln("[<fg=green>Ok</>] $name.json ($headline)");
            }
        }

        return Command::SUCCESS;
    }
}