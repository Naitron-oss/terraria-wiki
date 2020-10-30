<?php

namespace App\Command\Images;

use Goutte\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class Get extends Command
{
    protected static $defaultName = 'get:images';

    protected function configure()
    {
        $this
        ->addArgument('slug', InputArgument::REQUIRED, 'Slug / permalink')
        ->addArgument('selector', InputArgument::REQUIRED, 'Element selector')
        ->setDescription('Download images inside element selector.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $slug = $input->getArgument('slug');
        $selector = $input->getArgument('selector');

        $client = new Client();
        $crawler = $client->request('GET', 'https://terraria.gamepedia.com/' . $slug);

        $container = $crawler->filter($selector)->each(function (Crawler $node) {
            return $node->outerHtml();
        });

        foreach ($container as $node) {
            $node = new Crawler($node);
            foreach ($node->filter('img')->extract(['src', 'alt']) as $img) {
                $replace = [
                    "'" => '',
                    '/' => '_',
                    '.png' => '',
                    ' item sprite' => '',
                ];
                $name = str_replace(array_keys($replace), array_values($replace), $img[1]);
                $name = strtolower($name);
                $url = $img[0];
    
                if (!file_exists("./data/images/$name.png")) {
                    exec("wget $url -O './data/images/$name.png'");
                } else {
                    $output->writeln("[<fg=green>Ok</>] $name.png");
                }
            }
        }

        return Command::SUCCESS;
    }
}
