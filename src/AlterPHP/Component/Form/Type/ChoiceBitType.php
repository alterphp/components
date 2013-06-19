<?php

namespace AlterPHP\Component\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use AlterPHP\Component\Form\DataTransformer\BitWeightSumToChoicesTransformer;
use AlterPHP\Component\Form\DataTransformer\BitWeightToChoicesTransformer;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;

class ChoiceBitType extends ChoiceType
{

   /**
    * {@inheritdoc}
    */
   public function buildForm(FormBuilderInterface $builder, array $options)
   {
      if ($options['multiple']) {
         $builder->prependClientTransformer(new BitWeightSumToChoicesTransformer($options['choice_list'], $options['expanded']));
      } else {
         $builder->prependClientTransformer(new BitWeightToChoicesTransformer($options['choice_list']));
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

      if (!isset($options['choice_list'])) {
         $options['choice_list'] = new SimpleChoiceList($options['choices']);
      }

      return $options;
   }

   public function getParent()
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
