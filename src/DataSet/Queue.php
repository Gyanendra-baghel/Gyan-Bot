<?php

namespace Crawler\Dataset;

class Queue
{
   private $_items = array();
   public int $length = 0;

   public function enqueue($value)
   {
      if (!$this->has($value))
         array_unshift($this->_items, $value);
   }
   public function has($item)
   {
      return in_array($item, $this->_items);
   }
   public function dequeue()
   {
      return array_pop($this->_items);
   }

   public function peek()
   {
      return end($this->_items);
   }

   public function size()
   {
      return count($this->_items);
   }

   public function isEmpty()
   {
      return empty($this->_items);
   }
}


// class RestUrl
// {
//   public $store = [];
//   public $size = 0;
//   public function values()
//   {
//     return $this->store;
//   }
//   public function add($item)
//   {
//     if (!$this->has($item)) {
//       $this->store[] = $item;
//       $this->size++;
//     }
//   }
//   public function has($item)
//   {
//     return in_array($item, $this->store);
//   }
//   public function pop()
//   {
//     $this->size--;
//     return array_pop($this->store);
//   }
//   public function shift()
//   {
//     $this->size--;
//     return array_shift($this->store);
//   }
//   public function empty()
//   {
//     $this->size = 0;
//     $this->store = array();
//   }
// }
