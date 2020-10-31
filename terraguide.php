#!/usr/bin/env php
<?php

use App\Command\Items\Check as ItemsCheck;
use App\Command\Items\Fetch as ItemsFetch;
use App\Command\Items\Get as ItemsGet;
use App\Command\Items\Make as ItemsMake;
use App\Command\Items\FetchDyes;
use App\Command\Items\FetchFish;
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
  new FetchDyes(),
  new FetchFish(),
  new FetchSprite(),
]);

// Check
$app->add(new ItemsCheck());

$app->run();