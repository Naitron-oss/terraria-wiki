<?php

/**
 * Debug & test purpose
 */

use Goutte\Client;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

$client = new Client();
$crawler = $client->request('GET', 'https://terraria.gamepedia.com/Chests');

// $parents = $crawler->filter('.terraria')->each(function ($node, $i) {
//     echo $node->html();
// });

echo $crawler->filter('.crafts')->eq(0)->html();