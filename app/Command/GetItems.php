<?php

namespace app\Command;

use Goutte\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class GetItems extends Command
{
    private $json = [];
    protected static $defaultName = 'get:items';

    protected function configure()
    {
        $this
        ->addArgument('name', InputArgument::REQUIRED, 'Type items name:')
        ->setDescription('Get items json data')
        ->setHelp('Save json file contain items data by given items name.');
    }

    private function getTitle(Crawler $node) : void
    {
        $text = $node->filter('h1#firstHeading')->first()->text();
        $this->json['title'] = $text;
    }

    private function getInfo(Crawler $crawler) : void
    {
        $main = $crawler->filter('#mw-content-text > div > p');
        $alt = $crawler->filter('#mw-content-text > div > div > p');
        
        if ($main->count()) {
            $this->json['info'] = $main->first()->html();
        } else if ($alt->count()) {
            $this->json['info'] = $alt->first()->html();
        } else $this->json['info'] = '';
        
        $this->json['info'] = localify($this->json['info']);
    }

    private function getStat(Crawler $crawler) : void
    {
        $table = $crawler->filter('.infobox.item table.stat')->each(function (Crawler $crawler) {
            return localify($crawler->outerHtml());
        });
        
        $image = $crawler->filter('.infobox.item .section.images')->each(function (Crawler $node, $i) {
            return localify($node->outerHtml());
        });
        
        $this->json['stat']['image'] = count($image) ? $image[0] : '';
        $this->json['stat']['table'] = count($table) ? $table[0] : '';    
    }

    public function getCraft(Crawler $crawler) : string
    {
        $this->json['craft'] = [];

        $craftsDom = $crawler->filter('#mw-content-text > div > .crafts');
        $craftsDom = !$craftsDom->count()
        ? $crawler->filter('.crafts')
        : $craftsDom;

        $crafts = $craftsDom->each(function (Crawler $node) {

            $titleCrawler = new Crawler($node->html());
            $title = $titleCrawler->filter('caption');
            
            if ($title->count())
            {
                $title = strip_tags(str_replace('<br>', ' ', $title->first()->html()));
            }
            else {
            
                if ($node->previousAll()->count()) {
                    $titleCrawler = new Crawler($node->previousAll()->first()->html());
                    $title = $titleCrawler->filter('span.mw-headline');
                    
                    if ($title->count()) {
                        $title = $title->first()->html();
                    }
                    
                    else if ($node->previousAll()->count() && $node->previousAll()->previousAll()->count()) {
                        $titleCrawler = new Crawler($node->previousAll()->previousAll()->first()->html());
                        $title = $titleCrawler->filter('span.mw-headline');
                        
                        if ($title->count()) {
                            $title = $title->first()->html();
                        }
                        
                        else $title = 'Crafting';

                    }
                    
                    else $title = 'Crafting';
                
                }
                
                else $title = 'Crafting';
                
            }

            $table = localify($node->html());

            return [
                'title' => strip_tags($title),
                'table' => $table
            ];
        });
        
        $headline = [];
        foreach ($crafts as $craft) {
            $this->json['craft'][] = $craft;
            $headline[] = $craft['title'];
        }

        return implode(', ', $headline);
    }

    private function saveJson($name)
    {
        $json = json_encode($this->json, JSON_PRETTY_PRINT);
        return file_put_contents("./data/items/$name.json", $json);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $slug = str_replace(' ', '_', urldecode($name));

        $client = new Client();
        
        $output->writeln('Memuat data ...');
        $crawler = $client->request('GET', 'https://terraria.gamepedia.com/' . $slug);

        $this->getTitle($crawler);
        $this->getInfo($crawler);
        $this->getStat($crawler);
        $headline = $this->getCraft($crawler);

        $name = str_replace("'", '', $name);
        $name = str_replace("/", '_', $name);
        $output->writeln("\n[<fg=green>Ok</>] $name.json ($headline)");
        $this->saveJson($name);

        return Command::SUCCESS;
    }
}
