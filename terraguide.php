#!/usr/bin/env php
<?php

use App\Command\Items\Check as ItemsCheck;
use App\Command\Items\Fetch as ItemsFetch;
use App\Command\Items\Get as ItemsGet;
use App\Command\Items\Make as ItemsMake;
use App\Command\Items\FetchDyes;
use App\Command\Items\FetchFish;
use App\Command\MakePage;
use Symfony\Component\Console\Application;

require 'func.php';

$app = new Application();

// Make
$app->addCommands([
  new ItemsMake(),
  new MakePage(),
]);

// Get
$app->add(new ItemsGet());

// Fetch
$app->addCommands([
  new ItemsFetch(),
  new FetchDyes(),
  new FetchFish(),
]);

// Check
$app->add(new ItemsCheck());

$app->run();