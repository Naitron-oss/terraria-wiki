<?php

namespace App\Command\Items;

use Goutte\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class Get extends Command
{
    public $json = [];
    protected static $defaultName = 'get:items';

    protected function configure()
    {
        $this
        ->addArgument('name', InputArgument::REQUIRED, 'Type items name:')
        ->setDescription('Get items json data')
        ->setHelp('Save json file contain items data by given items name.');
    }

    public function getCrawler(String $slug) : Crawler
    {
        $link = 'https://terraria.gamepedia.com/' . $slug;
        $this->json['link'] = $link;
        $client = new Client();
        return $client->request('GET', $link);
    }

    public function getTitle(Crawler $node) : void
    {
        $text = $node->filter('h1#firstHeading')->first()->text();
        $this->json['title'] = $text;
    }

    public function getInfo(Crawler $crawler) : void
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

    public function getStat(Crawler $crawler) : void
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

    public function getCraft(Crawler $crawler, $mainFilter = '', $altFilter = '') : string
    {
        $mainFilter = $mainFilter ?: '#mw-content-text > div > .crafts';
        $altFilter = $altFilter ?: '.crafts';
        $this->json['craft'] = [];

        $craftsDom = $crawler->filter($mainFilter);
        $craftsDom = !$craftsDom->count()
        ? $crawler->filter($altFilter)
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

            $table = localify($node->outerHtml());

            return [
                'title' => strip_tags($title),
                'info' => '',
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

    public function customCraft($headline = [], $info = []) : string
    {
        $crafts = [];
        foreach ($this->json['craft'] as $key => $craft) {
            $crafts[] = [
                'title' => count($headline) ? $headline[$key] : $craft['title'],
                'info' => count($info) ? $info[$key] : $craft['info'],
                'table' => $craft['table'],
            ];
        }
        $this->json['craft'] = $crafts;
        return implode(', ', $headline);
    }

    public function saveJson($name)
    {
        $json = json_encode($this->json, JSON_PRETTY_PRINT);
        return file_put_contents("./data/items/$name.json", $json);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $slug = str_replace(' ', '_', urldecode($name));
        
        $output->writeln('Memuat data ...');
        $crawler = $this->getCrawler($slug);

        $this->getTitle($crawler);
        $this->getInfo($crawler);
        $this->getStat($crawler);
        $headline = $this->getCraft($crawler);

        $name = str_replace("'", '', $name);
        $name = str_replace("/", '_', $name);
        $this->saveJson($name);
        $output->writeln("[<fg=green>Ok</>] $name.json ($headline)");

        return Command::SUCCESS;
    }
}
