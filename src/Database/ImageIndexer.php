<?php

namespace Crawler\Database;

class ImageIndexer

{
   private $ins;
   private $update;
   private $check;

   public function __construct(\PDO $con)
   {
      $this->ins = $con->prepare("INSERT INTO images(imageUrl,siteUrl, title, alt) VALUES(:src, :url, :title, :alt)");
      $this->check = $con->prepare("SELECT * FROM image WHERE imageUrl = :src");
   }

   private function url_exist($url)
   {
      $this->check->bindParam(':src', $url);
      $this->check->execute();
      if ($this->check->rowCount() > 0) return true;
      return false;
   }

   public function insert($res)
   {
      if ($this->url_exist($res['src'])) {
         $this->ins->bindParam(':url', $res['url']);
         $this->ins->bindParam(':src', $res['src']);
         $this->ins->bindParam(':title', $res['title']);
         $this->ins->bindParam(':alt', $res['alt']);
         $this->ins->execute();
      }
   }

   public function __destruct()
   {
      $this->ins = null;
      $this->update = null;
   }
}
