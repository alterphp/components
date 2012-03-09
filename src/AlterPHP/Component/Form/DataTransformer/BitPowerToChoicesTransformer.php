<?php

namespace AlterPHP\Component\Form\DataTransformer;

use AlterPHP\Component\ToolBox\BitTools;
use AlterPHP\Component\Form\ChoiceList\EntityBitChoiceList;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Collections\Collection;

class EntityBitToIdTransformer implements DataTransformerInterface
{

   private $choiceList;

   public function __construct(EntityBitChoiceList $choiceList)
   {
      $this->choiceList = $choiceList;
   }

   /**
    * Transforms entities into choice keys.
    *
    * @param  integer $data An integer reprensenting a bitPower entity
    *
    * @return mixed An array of choice keys, a single key or NULL
    */
   public function transform($data)
   {
      if (null === $data)
      {
         return 0;
      }

      if (!is_int($data))
      {
         throw new UnexpectedTypeException($data, 'integer');
      }

      $bitPowerArray = BitTools::getBitArrayFromInt($data);

      if (count($bitPowerArray) > 1)
      {
         throw new \InvalidArgumentException('Provided data leads to many selected choices. Did you disable "multiple" option ?');
      }
      elseif (count($bitPowerArray) == 0)
      {
         return 0;
      }

      if ($this->choiceList->getEntity($bitPowerArray[0]))
      {
         return $bitPowerArray[0];
      }
   }

   /**
    * Transforms choice keys into entities bitPower sum.
    *
    * @param  mixed $key   An array of keys, a single key or NULL
    *
    * @return Collection|object  A collection of entities, a single entity or NULL
    */
   public function reverseTransform($key)
   {
      if ('' === $key || null === $key)
      {
         return 0;
      }

      if (!is_numeric($key))
      {
         throw new UnexpectedTypeException($key, 'numeric');
      }

      if (!is_object($this->choiceList->getEntity($key)))
      {
         throw new TransformationFailedException(sprintf('The entity with key "%s" could not be found', $key));
      }

      return pow(2, $key);
   }

}
