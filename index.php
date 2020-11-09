<?php

/**
 * Debug & test purpose
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$file = 'data/items/Leaf Wings.json';
$json = json_decode(file_get_contents($file));

// var_dump($json);

foreach ($json->craft as $craft) {
    echo "<h1>$craft->title</h1>";
    echo "<p>$craft->table</p>";
}
