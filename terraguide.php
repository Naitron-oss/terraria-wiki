#!/usr/bin/env php
<?php

use App\Command\CheckItems;
use App\Command\FetchDyes;
use App\Command\FetchFish;
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
$app->addCommands([
  new FetchItems(),
  new FetchDyes(),
  new FetchFish(),
]);

// Check
$app->add(new CheckItems());

$app->run();