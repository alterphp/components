<?php

namespace AlterPHP\Tests\Component\Fixtures;

use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity
 */
class SingleIdentBitWeightEntity
{

   /** @Id @Column(type="integer") */
   protected $id;

   /** @Column(type="integer", nullable=true) */
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
