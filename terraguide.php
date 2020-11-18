#!/usr/bin/env php
<?php

use App\Command\Items\Check as ItemsCheck;
use App\Command\Items\Fetch as ItemsFetch;
use App\Command\Items\Get as ItemsGet;
use App\Command\Items\Make as ItemsMake;
use App\Command\Images\Get as ImagesGet;
use App\Command\Images\FetchSprite;
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
$app->addCommands([
  new ItemsGet(),
  new ImagesGet(),
]);

// Fetch
$app->addCommands([
  new ItemsFetch(),
  new App\Command\Items\FetchDyes(),
  new App\Command\Items\FetchFish(),
  new App\Command\Items\FetchKites(),
  new App\Command\Items\FetchStatues(),
  new App\Command\Items\FetchWings(),
  new App\Command\Items\FetchTorches(),
  new App\Command\Items\FetchChests(),
  new App\Command\Items\FetchStrings(),
  new App\Command\Items\FetchCages(),
  new FetchSprite(),
]);

// Check
$app->add(new ItemsCheck());

$app->run();