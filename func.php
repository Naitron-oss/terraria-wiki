<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

function handle_image($match) {
    $replace = [
        "'" => '',
        '/' => '_',
        '.png' => '',
        ' item sprite' => '',
    ];
    $src = str_replace(array_keys($replace), array_values($replace), $match[2]);
            
    $img = '<img';
    $img = $img." src=\"./imgs/$src.png\"";
    $img = $img." alt=\"$src\"";
    $img = $img." title=\"$src\"";
    $img = $img.' />';
           
    return $img;
}

function localify($input) {        
    $html = preg_replace_callback('/<img(.*?)alt="(.*?)"(.*?)>/', 'handle_image', $input);
    $html = preg_replace('/<sup .*?>(.*?)<\/sup>/', '', $html);
    return preg_replace('/<a .*?>(.*?)<\/a>/', '$1', $html);
}
