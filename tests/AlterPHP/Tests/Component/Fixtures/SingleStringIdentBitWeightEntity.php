<?php

namespace AlterPHP\Tests\Component\Fixtures;

use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

/** @Entity */
class SingleStringIdentBitWeightEntity
{

   /** @Id @Column(type="string") */
   protected $id;

   /** @Column(type="string") */
   public $name;

   /** @Column(type="string", unique=true) */
   public $bitWeight;

   public function __construct($id, $name, $bitWeight)
   {
      $this->id = $id;
      $this->name = $name;
      $this->bitWeight = $bitWeight;
   }

}
