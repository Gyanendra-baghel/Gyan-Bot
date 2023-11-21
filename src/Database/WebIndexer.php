<?php

namespace Crawler\Database;

class WebIndexer
{
  private $con;
  private $insertQuery;
  private $updateQuery;
  public function __constructor(\PDO $con)
  {
    $this->insertQuery = $con->prepare("");
    $this->updateQuery = $con->prepare("");
  }
  private function isUpdate($res)
  {
    return false;
  }
  public function index($res)
  {
    if ($this->isUpdate($res)) {
      // 
    } else {
      $this->insertQuery->bindParam(':summary', $res['summary']);
      $this->insertQuery->execute();
    }
  }

  public function __destruct()
  {
    $this->insertQuery = null;
    $this->updateQuery = null;
  }
}
