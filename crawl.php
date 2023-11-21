<?php

require_once './vendor/autoload.php';

use Crawler\Crawler;

// Check if there are command-line arguments
if ($argc <= 1) {
  exit("Usage: php script.php site1 site2 site3\n");
}

// Remove script name from the arguments
array_shift($argv);

// Loop through each site and run the crawler
foreach ($argv as $site) {
  try {
    $crawler = new Crawler($site);
    $crawler->run();
  } catch (\Exception $e) {
    echo "Error crawling $site: " . $e->getMessage() . PHP_EOL;
  }
}
