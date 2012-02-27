<?php

namespace AlterPHP\Tests\Component\Fixtures;

use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

/** @Entity */
class CompositeStringIdentBitPowerEntity
{

   /** @Id @Column(type="string") */
   protected $id1;

   /** @Id @Column(type="string") */
   protected $id2;

   /** @Column(type="string") */
   public $name;

   /** @Column(type="string", unique=true) */
   public $bitPower;

   public function __construct($id1, $id2, $name, $bitPower)
   {
      $this->id1 = $id1;
      $this->id2 = $id2;
      $this->name = $name;
      $this->bitPower = $bitPower;
   }

}
