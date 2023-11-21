<?php

namespace Crawler;

use Crawler\Database\ImageIndexer;
use Crawler\Database\WebIndexer;
use Crawler\Database\MailIndexer;

class Indexer
{
  private $webIndexer;
  private $imageIndexer;
  private $mailIndexer;
  public function __construct()
  {
    $conn = new \PDO("");
    $this->imageIndexer = new ImageIndexer($conn);
    $this->webIndexer = new WebIndexer($conn);
    $this->mailIndexer = new MailIndexer($conn);
  }
  public function img($d)
  {
    $this->imageIndexer->insert($d);
  }
  public function web($data)
  {
    $this->webIndexer->index($data);
  }
  public function mail($mail)
  {
    $this->mailIndexer->insert($mail);
  }
}
