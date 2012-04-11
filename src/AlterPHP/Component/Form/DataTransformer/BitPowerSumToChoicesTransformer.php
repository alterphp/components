<?php

namespace AlterPHP\Component\Form\DataTransformer;

use AlterPHP\Component\ToolBox\BitTools;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\DataTransformerInterface;

class BitPowerSumToChoicesTransformer implements DataTransformerInterface
{

   private $choiceList;
   private $expanded;

   public function __construct(ChoiceListInterface $choiceList, $expanded = true)
   {
      $this->choiceList = $choiceList;
      $this->expanded = $expanded;
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
      try
      {
         $largeChoices = $this->choiceList->getChoices();
      }
      catch (\Exception $e)
      {
         throw new TransformationFailedException('Can not get the choice list', $e->getCode(), $e);
      }

      $array = array ();

      foreach ($largeChoices as $bitPower => $value)
      {
         if (in_array($bitPower, $bitList))
         {
            $array[] = is_numeric($bitPower) ? (int) $bitPower : $bitPower;
         }
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
         if (!$this->expanded)
         {
            if (array_key_exists((int) $key[0], $this->choiceList->getChoices()))
            {
               $bitSum += pow(2, (int) $key[0]);
            }
            else
            {
               $notFound[] = $key[0];
            }
         }
         else
         {
            if (array_key_exists((int) $key, $this->choiceList->getChoices()))
            {
               $bitSum += pow(2, (int) $key);
            }
            else
            {
               $notFound[] = $key;
            }
         }
      }

      if (count($notFound) > 0)
      {
         throw new TransformationFailedException(sprintf('The choices with bitPower "%s" could not be found', implode(',', $notFound)));
      }

      return $bitSum;
   }

}
