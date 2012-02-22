<?php

namespace AlterPHP\Tests\Component\Fixtures;

use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity
 */
class BitPowerEntity
{

   /** @Id @Column(type="integer") */
   protected $id;

   /** @Column(type="integer", nullable=true) */
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
