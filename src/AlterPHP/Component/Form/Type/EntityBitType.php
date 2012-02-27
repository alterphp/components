<?php

namespace AlterPHP\Component\Form\Type;

use AlterPHP\Component\Form\ChoiceList\EntityBitChoiceList;
use AlterPHP\Component\Form\DataTransformer\EntityBitToIdTransformer;
use AlterPHP\Component\Form\DataTransformer\EntityBitsToArrayTransformer;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\ArrayToBooleanChoicesTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\ArrayToChoicesTransformer;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Exception\FormException;
use Symfony\Component\Form\FormBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;

class EntityBitType extends AbstractType
{

   protected $registry;

   public function __construct(RegistryInterface $registry)
   {
      $this->registry = $registry;
   }

   public function buildForm(FormBuilder $builder, array $options)
   {
      if ($options['multiple'])
      {
         $builder->prependClientTransformer(new EntityBitsToArrayTransformer($options['choice_list']));
      }
      else
      {
         $builder->prependClientTransformer(new EntityBitToIdTransformer($options['choice_list']));
      }
   }

   public function getDefaultOptions(array $options)
   {
      $multiple = !isset($options['multiple']) || $options['multiple'];
      $expanded = !isset($options['expanded']) || $options['expanded'];

      $defaultOptions = array (
              'em' => null,
              'class' => null,
              'property' => null,
              'bitpower_property' => 'bitPower',
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

      $options = array_replace($defaultOptions, $options);

      if (!isset($options['choice_list']))
      {
         $defaultOptions['choice_list'] = new EntityBitChoiceList(
               $this->registry->getEntityManager($options['em']),
               $options['class'],
               $options['bitpower_property'],
               $options['property'],
               $options['query_builder'],
               $options['choices']
         );
      }

      return $defaultOptions;
   }

   public function getParent(array $options)
   {
      return 'choice';
   }

   public function getName()
   {
      return 'entitybit';
   }

}
