<?php

namespace AlterPHP\Component\Form;

use Symfony\Component\Form\AbstractExtension;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser;

class AlterPHPExtension extends AbstractExtension
{

   protected $registry;

   public function __construct(RegistryInterface $registry = null)
   {
      if (isset($registry))
      {
         $this->registry = $registry;
      }
   }

   protected function loadTypes()
   {
      $types = array(new Type\ChoiceBitType());

      if (isset($this->registry))
      {
         $types[] = new Type\EntityBitType($this->registry);
      }

      return $types;
   }

   protected function loadTypeGuesser()
   {
      return new DoctrineOrmTypeGuesser($this->registry);
   }

}
