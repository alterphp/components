<?php

namespace AlterPHP\Component\Form\Type;

use AlterPHP\Component\Form\ChoiceList\EntityBitChoiceList;
use AlterPHP\Component\Form\DataTransformer\EntityBitToIdTransformer;
use AlterPHP\Component\Form\DataTransformer\EntityBitsToArrayTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;

class EntityBitType extends AbstractType
{

   protected $registry;

   public function __construct(RegistryInterface $registry)
   {
      $this->registry = $registry;
   }

   public function buildForm(FormBuilderInterface $builder, array $options)
   {
      if ($options['multiple']) {
         $builder->prependClientTransformer(new EntityBitsToArrayTransformer($options['choice_list']));
      } else {
         $builder->prependClientTransformer(new EntityBitToIdTransformer($options['choice_list']));
      }
   }

   public function getDefaultOptions(array $options)
   {
      $parentOptions = parent::getDefaultOptions($options);

      //Following 2 lines depends on default
      $multiple = !isset($options['multiple']) || $options['multiple'];
      $expanded = !isset($options['expanded']) || $options['expanded'];

      $specificDefaultOptions = array (
              'em' => null,
              'class' => null,
              'property' => null,
              'bitweight_property' => 'bitWeight',
              'query_builder' => null,
              'choices' => null,
              'choice_list' => null,
              'multiple' => true,
              'expanded' => true,
              'error_bubbling' => false,
              'preferred_choices' => array (),
              'empty_data' => $multiple || $expanded ? array () : '',
              'empty_value' => $multiple || $expanded || !isset($options['empty_value']) ? null : '',
      );

      $options = array_merge($parentOptions, $specificDefaultOptions, $options);

      if (!isset($options['choice_list'])) {
         $options['choice_list'] = new EntityBitChoiceList(
               $this->registry->getEntityManager($options['em']),
               $options['class'],
               $options['bitweight_property'],
               $options['property'],
               $options['query_builder'],
               $options['choices']
         );
      }

      return $options;
   }

   public function getParent()
   {
      return 'choice';
   }

   public function getName()
   {
      return 'entitybit';
   }

}
