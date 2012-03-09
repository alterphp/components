<?php

namespace AlterPHP\Component\Form\DataTransformer;

use Symfony\Component\Form\Extension\Core\ChoiceList\ArrayChoiceList;
use AlterPHP\Component\ToolBox\BitTools;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\DataTransformerInterface;

class BitPowerSumToChoicesTransformer implements DataTransformerInterface
{

   private $choiceList;

   public function __construct(ArrayChoiceList $choiceList)
   {
      $this->choiceList = $choiceList;
   }

   /**
    * Transforms bitSum integer into choice keys.
    * @param  integer $data An integer reprensenting bitPowers sum
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
      $largeChoices = $this->choiceList->getChoices();
      $cl = $this->choiceList;
      $callback = function ($bitPower) use ($cl, $bitList) { return in_array($bitPower, $bitList, true); };
      $choices = array_filter($largeChoices, $callback);

      $array = array ();

      foreach ($choices as $bitPower)
      {
         $array[] = is_numeric($bitPower) ? (int) $bitPower : $bitPower;
      }

      return $array;
   }

   /**
    * Transforms choice keys into bitPower sum...
    * @param  mixed $keys An array of keys, a single key or NULL
    * @return integer An integer reprensenting bitPowers sum
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

      foreach ($keys as $key)
      {
         if (in_array($key, $this->choiceList))
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
         throw new TransformationFailedException(sprintf('The itPowers "%s" could not be found', implode('", "', $notFound)));
      }

      return $bitSum;
   }

}
