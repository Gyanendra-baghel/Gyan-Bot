<?php

namespace Crawler\Dataset;

class Set
{
   private $data = [];
   public $length = 0;
   public function add($item)
   {
      if (!$this->has($item)) array_push($this->data, $item);
      $this->length++;
   }
   public function has($item)
   {
      return in_array($item, $this->data);
   }
   public function remove($item)
   {
      // 
   }
}
