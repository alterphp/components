<?php

namespace AlterPHP\Component\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Exception\FormException;

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

      $specificDefaultOptions = array ('multiple' => true, 'expanded' => true);

      return array_merge($parentOptions, $specificDefaultOptions);
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
