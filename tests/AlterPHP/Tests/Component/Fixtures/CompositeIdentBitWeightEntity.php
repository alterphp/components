<?php

namespace AlterPHP\Tests\Component\Fixtures;

use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Security\Core\User\UserInterface;

/** @Entity */
class CompositeIdentBitWeightEntity implements UserInterface
{

   /** @Id @Column(type="integer") */
   protected $id1;

   /** @Id @Column(type="integer") */
   protected $id2;

   /** @Column(type="string") */
   public $name;

   /** @Column(type="string", unique=true) */
   public $bitWeight;

   public function __construct($id1, $id2, $name, $bitWeight)
   {
      $this->id1 = $id1;
      $this->id2 = $id2;
      $this->name = $name;
      $this->bitWeight = $bitWeight;
   }

   public function getRoles()
   {

   }

   public function getPassword()
   {

   }

   public function getSalt()
   {

   }

   public function getUsername()
   {
      return $this->name;
   }

   public function eraseCredentials()
   {

   }

   public function equals(UserInterface $user)
   {

   }

}
