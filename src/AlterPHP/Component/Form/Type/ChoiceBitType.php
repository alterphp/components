<?php

namespace AlterPHP\Component\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Exception\FormException;
use AlterPHP\Component\Form\DataTransformer\BitPowerSumToChoicesTransformer;
use AlterPHP\Component\Form\DataTransformer\BitPowerToChoicesTransformer;
use Symfony\Component\Form\Extension\Core\ChoiceList\ArrayChoiceList;

class ChoiceBitType extends ChoiceType
{

   /**
    * {@inheritdoc}
    */
   public function buildForm(FormBuilder $builder, array $options)
   {
      if ($options['multiple'])
      {
         $builder->prependClientTransformer(new BitPowerSumToChoicesTransformer($options['choice_list']));
      }
      else
      {
         $builder->prependClientTransformer(new BitPowerToChoicesTransformer($options['choice_list']));
      }
   }

   /**
    * {@inheritdoc}
    */
   public function getDefaultOptions(array $options)
   {
      $parentOptions = parent::getDefaultOptions($options);

      $multiple = !isset($options['multiple']) || $options['multiple'];
      $expanded = !isset($options['expanded']) || $options['expanded'];

      $specificDefaultOptions = array (
              'multiple' => true,
              'expanded' => true,
              'empty_data' => $multiple || $expanded ? array () : '',
              'empty_value' => $multiple || $expanded || !isset($options['empty_value']) ? null : '',
      );

      $options = array_merge($parentOptions, $specificDefaultOptions, $options);

      if (!isset($options['choice_list']))
      {
         $options['choice_list'] = new ArrayChoiceList($options['choices']);
      }

      return $options;
   }

   public function getParent(array $options)
   {
      return 'choice';
   }

   /**
    * {@inheritdoc}
    */
   public function getName()
   {
      return 'choicebit';
   }

}
