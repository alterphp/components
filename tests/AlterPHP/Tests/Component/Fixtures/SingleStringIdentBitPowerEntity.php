<?php

namespace AlterPHP\Tests\Component\Fixtures;

use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

/** @Entity */
class SingleStringIdentEntity
{

   /** @Id @Column(type="string") */
   protected $id;

   /** @Column(type="string") */
   public $name;

   /** @Column(type="string", unique=true) */
   public $bitPower;

   public function __construct($id, $name, $bitPower)
   {
      $this->id = $id;
      $this->name = $name;
      $this->bitPower = $bitPower;
   }

}
