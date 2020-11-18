<?php

namespace App\Command\Items;

use app\Command\Items\Get as GetItems;
use Goutte\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchStrings extends Command
{
    protected static $defaultName = 'fetch:strings';

    public function configure()
    {
        $this
        ->setDescription('Fetch data of strings type item');
    }

    public function getLists() : array
    {
        $items = file_get_contents('items.txt');
        $items = explode("\n", $items);
        
        $list = [];
        foreach ($items as $item) {
            if (strpos($item, 'String')) {
                $list[] = $item;
            }
        }

        return $list;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $get = new GetItems('Strings');
        $crawler = $get->getCrawler('Strings');
        $get->getTitle($crawler);
        $get->getInfo($crawler);
        $get->getStat($crawler);
        $headline = $get->getCraft($crawler, '.crafts');
        $get->saveJson('_Strings');

        foreach ($this->getLists($output) as $item) {
            if ($item) {
                $name = str_replace("'", '', $item);
                $name = str_replace("/", '_', $name);
                $get->json = ['refer' => '_Strings'];
                $get->saveJson($name);
                $output->writeln("[<fg=green>Ok</>] $name.json ($headline)");
            }
        }

        return Command::SUCCESS;
    }
}