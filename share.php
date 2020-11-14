<?php

$f = $_GET['f'];
$file = file_get_contents("$f");

if ($file) {
    header("Access-Control-Allow-Origin: *");
    header("Content-type: application/json");
    echo $file;
}
