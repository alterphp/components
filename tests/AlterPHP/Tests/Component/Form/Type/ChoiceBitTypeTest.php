<?php

namespace AlterPHP\Tests\Component\Form\Type;

use AlterPHP\Component\Form\AlterPHPExtension;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;

class ChoiceBitTypeTest extends TypeTestCase
{

   private $choices = array (
           0 => 'Bernhard',
           1 => 'Fabien',
           2 => 'Kris',
           4 => 'Jon',
           5 => 'Roman',
   );
   protected $groupedChoices = array (
           'Symfony' => array (
                   0 => 'Bernhard',
                   1 => 'Fabien',
                   2 => 'Kris',
           ),
           'Doctrine' => array (
                   4 => 'Jon',
                   5 => 'Roman',
           )
   );

   protected function getExtensions()
   {
      return array_merge(parent::getExtensions(), array (
                 new AlterPHPExtension(),
         ));
   }

   /**
    * @expectedException Symfony\Component\Form\Exception\UnexpectedTypeException
    */
   public function testChoicesOptionExpectsArray()
   {
      $form = $this->factory->create('choicebit', null, array (
              'choices' => new \ArrayObject(),
         ));
   }

   /**
    * @expectedException Symfony\Component\Form\Exception\FormException
    */
   public function testChoiceListOptionExpectsChoiceListInterface()
   {
      $form = $this->factory->create('choicebit', null, array (
              'choice_list' => array ('foo' => 'foo'),
         ));
   }

   public function testExpandedCheckboxesAreNeverRequired()
   {
      $form = $this->factory->create('choicebit', null, array (
              'multiple' => true,
              'expanded' => true,
              'required' => true,
              'choices' => $this->choices,
         ));

      foreach ($form as $child) {
         $this->assertFalse($child->isRequired());
      }
   }

    public function testExpandedRadiosAreRequiredIfChoiceFieldIsRequired()
    {
        $form = $this->factory->create('choicebit', null, array(
            'multiple' => false,
            'expanded' => true,
            'required' => true,
            'choices' => $this->choices,
        ));

        foreach ($form as $child) {
            $this->assertTrue($child->isRequired());
        }
    }

    public function testExpandedRadiosAreNotRequiredIfChoiceFieldIsNotRequired()
    {
        $form = $this->factory->create('choicebit', null, array(
            'multiple' => false,
            'expanded' => true,
            'required' => false,
            'choices' => $this->choices,
        ));

        foreach ($form as $child) {
            $this->assertFalse($child->isRequired());
        }
    }

    public function testBindSingleNonExpanded()
    {
        $form = $this->factory->create('choicebit', null, array(
            'multiple' => false,
            'expanded' => false,
            'choices' => $this->choices,
        ));

        $form->bind('1');

        $this->assertEquals(2, $form->getData());
        $this->assertEquals('1', $form->getClientData());
    }

    public function testBindMultipleNonExpanded()
    {
        $form = $this->factory->create('choicebit', null, array(
            'multiple' => true,
            'expanded' => false,
            'choices' => $this->choices,
        ));

        $form->bind(array('0', '1'));

        $this->assertEquals(3, $form->getData());
        $this->assertEquals(array('0', '1'), $form->getClientData());
    }

    public function testBindSingleExpanded()
    {
        $form = $this->factory->create('choicebit', null, array(
            'multiple' => false,
            'expanded' => true,
            'choices' => $this->choices,
        ));

        $form->bind('1');

        $this->assertEquals(2, $form->getData());
        $this->assertEquals(false, $form['0']->getData());
        $this->assertEquals(true, $form['1']->getData());
        $this->assertEquals(false, $form['2']->getData());
        $this->assertEquals(false, $form['4']->getData());
        $this->assertEquals(false, $form['5']->getData());
        $this->assertEquals('', $form['0']->getClientData());
        $this->assertEquals('1', $form['1']->getClientData());
        $this->assertEquals('', $form['2']->getClientData());
        $this->assertEquals('', $form['4']->getClientData());
        $this->assertEquals('', $form['5']->getClientData());
    }

    public function testBindSingleExpandedWithFalseDoesNotHaveExtraFields()
    {
        $form = $this->factory->create('choicebit', null, array(
            'multiple' => false,
            'expanded' => true,
            'choices' => $this->choices,
        ));

        $form->bind(false);

        $this->assertEmpty($form->getExtraData());
        $this->assertEquals(0, $form->getData());
    }

    public function testBindMultipleExpanded()
    {
        $form = $this->factory->create('choicebit', null, array(
            'multiple' => true,
            'expanded' => true,
            'choices' => $this->choices,
        ));

        $form->bind(array('0' => '0', '1' => '1'));

        $this->assertEquals(3, $form->getData());
        $this->assertEquals(true, $form['0']->getData());
        $this->assertEquals(true, $form['1']->getData());
        $this->assertEquals(false, $form['2']->getData());
        $this->assertEquals(false, $form['4']->getData());
        $this->assertEquals(false, $form['5']->getData());
        $this->assertEquals('1', $form['0']->getClientData());
        $this->assertEquals('1', $form['1']->getClientData());
        $this->assertEquals('', $form['2']->getClientData());
        $this->assertEquals('', $form['4']->getClientData());
        $this->assertEquals('', $form['5']->getClientData());
    }
}
