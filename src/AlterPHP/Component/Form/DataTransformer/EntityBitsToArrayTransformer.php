<?php

namespace AlterPHP\Component\Form\DataTransformer;

use AlterPHP\Component\Form\ChoiceList\EntityBitChoiceList;
use AlterPHP\Component\ToolBox\BitTools;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class EntityBitsToArrayTransformer implements DataTransformerInterface
{

   private $choiceList;

   public function __construct(EntityBitChoiceList $choiceList)
   {
      $this->choiceList = $choiceList;
   }

   /**
    * Transforms bitSum integer into choice keys.
    *
    * @param  integer $data An integer reprensenting bitPowers sum
    *
    * @return mixed An array of choice keys, a single key or NULL
    */
   public function transform($data)
   {
      if (null === $data)
      {
         return array ();
      }

      if (!is_int($data))
      {
         throw new UnexpectedTypeException($data, 'integer');
      }

      $bitList = BitTools::getBitArrayFromInt($data);
      $largeCollection = $this->choiceList->getEntities();
      $cl = $this->choiceList;
      $callback = function ($entity) use ($cl, $bitList) { return in_array($cl->getBitPowerValue($entity), $bitList, true); };
      $collection = array_filter($largeCollection, $callback);

      $array = array ();

      foreach ($collection as $entity)
      {
         $value = $this->choiceList->getBitPowerValue($entity);
         $array[] = is_numeric($value) ? (int) $value : $value;
      }

      return $array;
   }

   /**
    * Transforms choice keys into entities.
    *
    * @param  mixed $keys        An array of keys, a single key or NULL
    *
    * @return Collection|object  A collection of entities, a single entity or NULL
    */
   public function reverseTransform($keys)
   {
      if ('' === $keys || null === $keys)
      {
         return 0;
      }

      if (!is_array($keys))
      {
         throw new UnexpectedTypeException($keys, 'array');
      }

      $bitSum = 0;
      $notFound = array ();

      // optimize this into a SELECT WHERE IN query
      foreach ($keys as $key)
      {
         if (is_object($this->choiceList->getEntity($key)))
         {
            $bitSum += pow(2, (int)$key);
         }
         else
         {
            $notFound[] = $key;
         }
      }

      if (count($notFound) > 0)
      {
         throw new TransformationFailedException(sprintf('The entities with bitPowers "%s" could not be found', implode('", "', $notFound)));
      }

      return $bitSum;
   }

}
