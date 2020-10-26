#!/usr/bin/env php
<?php

use App\Command\FetchItems;
use App\Command\GetItems;
use App\Command\MakeItems;
use Symfony\Component\Console\Application;

require 'func.php';

$app = new Application();

// Make
$app->add(new MakeItems());

// Get
$app->add(new GetItems());

// Fetch
$app->add(new FetchItems());

$app->run();