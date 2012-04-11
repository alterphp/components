<?php

namespace AlterPHP\Component\Form\DataTransformer;

use AlterPHP\Component\ToolBox\BitTools;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Collections\Collection;

class BitPowerToChoicesTransformer implements DataTransformerInterface
{

   private $choiceList;

   public function __construct(ChoiceListInterface $choiceList)
   {
      $this->choiceList = $choiceList;
   }

   /**
    * Transforms bitPowers into choice keys.
    *
    * @param  integer $data An integer reprensenting a bitPower
    *
    * @return mixed An array of choice keys, a single key or NULL
    */
   public function transform($data)
   {
      if (null === $data)
      {
         return null;
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
      $callback = function ($bitPower) use ($bitList)
         {
            return in_array($bitPower, $bitList, true);
         };
      $choices = array_filter(array_keys($largeChoices), $callback);

      if (count($choices) > 1)
      {
         throw new \InvalidArgumentException('Provided data leads to many selected choices. Did you disable "multiple" option ?');
      }
      elseif (count($choices) == 0)
      {
         return null;
      }

      $choice = array_shift($choices);

      return is_int($choice) ? (int) $choice : $choice;
   }

   /**
    * Transforms choice keys into bitPower sum.
    *
    * @param  mixed $key   An array of keys, a single key or NULL
    *
    * @return Collection|object  A collection of values, a single entity or NULL
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

      if (!array_key_exists((int) $key, $this->choiceList->getChoices()))
      {
         throw new TransformationFailedException(sprintf('The choice with bitPower "%s" could not be found', $key));
      }

      return pow(2, $key);
   }

}
