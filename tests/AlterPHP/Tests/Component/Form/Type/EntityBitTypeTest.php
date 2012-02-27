<?php

namespace AlterPHP\Tests\Component\Form\Type;

use AlterPHP\Component\Form\AlterPHPExtension;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Tests\Component\Form\Extension\Core\Type\TypeTestCase;
use Symfony\Tests\Bridge\Doctrine\DoctrineOrmTestCase;
use AlterPHP\Tests\Component\Fixtures\SingleIdentBitPowerEntity;
use AlterPHP\Tests\Component\Fixtures\SingleStringIdentBitPowerEntity;
use AlterPHP\Tests\Component\Fixtures\CompositeIdentBitPowerEntity;
use AlterPHP\Tests\Component\Fixtures\CompositeStringIdentBitPowerEntity;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Common\Collections\ArrayCollection;

class EntityBitTypeTest extends TypeTestCase
{

   const SINGLE_STRING_IDENT_BIT_POWER_CLASS = 'AlterPHP\Tests\Component\Fixtures\SingleStringIdentBitPowerEntity';
   const SINGLE_IDENT_BIT_POWER_CLASS = 'AlterPHP\Tests\Component\Fixtures\SingleIdentBitPowerEntity';
   const COMPOSITE_STRING_IDENT_BIT_POWER_CLASS = 'AlterPHP\Tests\Component\Fixtures\CompositeStringIdentBitPowerEntity';
   const COMPOSITE_IDENT_BIT_POWER_CLASS = 'AlterPHP\Tests\Component\Fixtures\CompositeIdentBitPowerEntity';

   private $em;

   protected function setUp()
   {
      if (!class_exists('Doctrine\\Common\\Version'))
      {
         $this->markTestSkipped('Doctrine is not available.');
      }

      $this->em = DoctrineOrmTestCase::createTestEntityManager();

      parent::setUp();

      $schemaTool = new SchemaTool($this->em);
      $classes = array (
              $this->em->getClassMetadata(self::SINGLE_IDENT_BIT_POWER_CLASS),
              $this->em->getClassMetadata(self::SINGLE_STRING_IDENT_BIT_POWER_CLASS),
              $this->em->getClassMetadata(self::COMPOSITE_IDENT_BIT_POWER_CLASS),
              $this->em->getClassMetadata(self::COMPOSITE_STRING_IDENT_BIT_POWER_CLASS),
      );

      try
      {
         $schemaTool->dropSchema($classes);
      }
      catch (\Exception $e)
      {

      }

      try
      {
         $schemaTool->createSchema($classes);
      }
      catch (\Exception $e)
      {

      }
   }

   protected function tearDown()
   {
      parent::tearDown();

      $this->em = null;
   }

   protected function getExtensions()
   {
      return array_merge(parent::getExtensions(), array (
                 new AlterPHPExtension($this->createRegistryMock('default', $this->em)),
         ));
   }

   protected function persist(array $entities)
   {
      foreach ($entities as $entity)
      {
         $this->em->persist($entity);
      }

      $this->em->flush();
      // no clear, because entities managed by the choice field must
      // be managed!
   }

   public function testSetDataToUninitializedEntityWithNonRequired()
   {
      $entity1 = new SingleIdentBitPowerEntity(1, 'Foo', 2);
      $entity2 = new SingleIdentBitPowerEntity(2, 'Bar', 4);

      $this->persist(array ($entity1, $entity2));

      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'em' => 'default',
              'class' => self::SINGLE_IDENT_BIT_POWER_CLASS,
              'required' => false,
              'bitpower_property' => 'bitPower',
              'property' => 'name'
         ));

      $this->assertEquals(array (2 => 'Foo', 4 => 'Bar'), $field->createView()->get('choices'));
   }

   /**
    * @expectedException Symfony\Component\Form\Exception\UnexpectedTypeException
    */
   public function testConfigureQueryBuilderWithNonQueryBuilderAndNonClosure()
   {
      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'em' => 'default',
              'class' => self::SINGLE_IDENT_BIT_POWER_CLASS,
              'query_builder' => new \stdClass(),
         ));
   }

   /**
    * @expectedException Symfony\Component\Form\Exception\UnexpectedTypeException
    */
   public function testConfigureQueryBuilderWithClosureReturningNonQueryBuilder()
   {
      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'em' => 'default',
              'class' => self::SINGLE_IDENT_BIT_POWER_CLASS,
              'query_builder' => function ()
              {
                 return new \stdClass();
              },
         ));

      $field->bind('2');
   }

   public function testSetDataSingleNull()
   {
      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'multiple' => false,
              'expanded' => false,
              'em' => 'default',
              'class' => self::SINGLE_IDENT_BIT_POWER_CLASS,
         ));
      $field->setData(null);

      $this->assertEquals(null, $field->getData());
      $this->assertEquals(0, $field->getClientData());
   }

   public function testSetDataMultipleExpandedNull()
   {
      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'em' => 'default',
              'class' => self::SINGLE_IDENT_BIT_POWER_CLASS,
         ));
      $field->setData(null);

      $this->assertEquals(null, $field->getData());
      $this->assertEquals(array (), $field->getClientData());
   }

   public function testSetDataMultipleNonExpandedNull()
   {
      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'expanded' => false,
              'em' => 'default',
              'class' => self::SINGLE_IDENT_BIT_POWER_CLASS,
         ));
      $field->setData(null);

      $this->assertEquals(null, $field->getData());
      $this->assertEquals(array (), $field->getClientData());
   }

   public function testSubmitSingleExpandedNull()
   {
      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'multiple' => false,
              'em' => 'default',
              'class' => self::SINGLE_IDENT_BIT_POWER_CLASS,
         ));
      $field->bind(null);

      $this->assertEquals(null, $field->getData());
      $this->assertEquals(array (), $field->getClientData());
   }

   public function testSubmitSingleNonExpandedNull()
   {
      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'multiple' => false,
              'expanded' => false,
              'em' => 'default',
              'class' => self::SINGLE_IDENT_BIT_POWER_CLASS,
         ));
      $field->bind(null);

      $this->assertEquals(null, $field->getData());
      $this->assertEquals(0, $field->getClientData());
   }

   public function testSubmitMultipleNull()
   {
      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'expanded' => false,
              'em' => 'default',
              'class' => self::SINGLE_IDENT_BIT_POWER_CLASS,
         ));
      $field->bind(null);

      $this->assertEquals(0, $field->getData());
      $this->assertEquals(array (), $field->getClientData());
   }

   public function testSubmitSingleNonExpandedSingleIdentifier()
   {
      $entity1 = new SingleIdentBitPowerEntity(1, 'Foo', 5);
      $entity2 = new SingleIdentBitPowerEntity(2, 'Bar', 2);

      $this->persist(array ($entity1, $entity2));

      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'multiple' => false,
              'expanded' => false,
              'em' => 'default',
              'class' => self::SINGLE_IDENT_BIT_POWER_CLASS,
              'property' => 'name',
         ));

      $field->bind('2');

      $this->assertTrue($field->isSynchronized());
      $this->assertEquals(4, $field->getData());
      $this->assertEquals(2, $field->getClientData());
   }

   public function testSubmitSingleNonExpandedCompositeIdentifier()
   {
      $entity1 = new CompositeIdentBitPowerEntity(10, 20, 'Foo', 3);
      $entity2 = new CompositeIdentBitPowerEntity(30, 40, 'Bar', 4);

      $this->persist(array ($entity1, $entity2));

      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'multiple' => false,
              'expanded' => false,
              'em' => 'default',
              'class' => self::COMPOSITE_IDENT_BIT_POWER_CLASS,
              'property' => 'name',
         ));

      // the collection key is used here
      $field->bind('3');

      $this->assertTrue($field->isSynchronized());
      $this->assertEquals(8, $field->getData());
      $this->assertEquals(3, $field->getClientData());
   }

   public function testSubmitMultipleNonExpandedSingleIdentifier()
   {
      $entity1 = new SingleIdentBitPowerEntity(1, 'Foo', 3);
      $entity2 = new SingleIdentBitPowerEntity(2, 'Bar', 2);
      $entity3 = new SingleIdentBitPowerEntity(3, 'Baz', 0);

      $this->persist(array ($entity1, $entity2, $entity3));

      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'expanded' => false,
              'em' => 'default',
              'class' => self::SINGLE_IDENT_BIT_POWER_CLASS,
              'property' => 'name',
         ));

      $field->bind(array ('0', '3'));

      $expected = 9;

      $this->assertTrue($field->isSynchronized());
      $this->assertEquals($expected, $field->getData());
      $this->assertEquals(array (3, 0), $field->getClientData());
   }

   public function testSubmitMultipleNonExpandedSingleIdentifier_existingData()
   {
      $entity1 = new SingleIdentBitPowerEntity(1, 'Foo', 0);
      $entity2 = new SingleIdentBitPowerEntity(2, 'Bar', 1);
      $entity3 = new SingleIdentBitPowerEntity(3, 'Baz', 2);

      $this->persist(array ($entity1, $entity2, $entity3));

      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'expanded' => false,
              'em' => 'default',
              'class' => self::SINGLE_IDENT_BIT_POWER_CLASS,
              'property' => 'name',
         ));

      $existing = 2;

      $field->setData($existing);
      $field->bind(array ('0', '2'));

      // entry with index 0 was removed
      $expected = 5;

      $this->assertTrue($field->isSynchronized());
      $this->assertEquals($expected, $field->getData());
      $this->assertEquals(array (0, 2), $field->getClientData());
   }

   public function testSubmitMultipleNonExpandedCompositeIdentifier()
   {
      $entity1 = new CompositeIdentBitPowerEntity(10, 20, 'Foo', 0);
      $entity2 = new CompositeIdentBitPowerEntity(30, 40, 'Bar', 1);
      $entity3 = new CompositeIdentBitPowerEntity(50, 60, 'Baz', 3);

      $this->persist(array ($entity1, $entity2, $entity3));

      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'expanded' => false,
              'em' => 'default',
              'class' => self::COMPOSITE_IDENT_BIT_POWER_CLASS,
              'property' => 'name',
         ));

      // because of the composite key collection keys are used
      $field->bind(array ('0', '3'));

      $expected = 9;

      $this->assertTrue($field->isSynchronized());
      $this->assertEquals($expected, $field->getData());
      $this->assertEquals(array (0, 3), $field->getClientData());
   }

   public function testSubmitMultipleNonExpandedCompositeIdentifier_existingData()
   {
      $entity1 = new CompositeIdentBitPowerEntity(10, 20, 'Foo', 0);
      $entity2 = new CompositeIdentBitPowerEntity(30, 40, 'Bar', 1);
      $entity3 = new CompositeIdentBitPowerEntity(50, 60, 'Baz', 3);

      $this->persist(array ($entity1, $entity2, $entity3));

      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'expanded' => false,
              'em' => 'default',
              'class' => self::COMPOSITE_IDENT_BIT_POWER_CLASS,
              'property' => 'name',
         ));

      $existing = 2;

      $field->setData($existing);
      $field->bind(array ('0', '3'));

      // entry with index 0 was removed
      $expected = 9;

      $this->assertTrue($field->isSynchronized());
      $this->assertEquals($expected, $field->getData());
      $this->assertEquals(array (0, 3), $field->getClientData());
   }

   public function testSubmitSingleExpanded()
   {
      $entity1 = new SingleIdentBitPowerEntity(1, 'Foo', 4);
      $entity2 = new SingleIdentBitPowerEntity(2, 'Bar', 2);

      $this->persist(array ($entity1, $entity2));

      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'multiple' => false,
              'em' => 'default',
              'class' => self::SINGLE_IDENT_BIT_POWER_CLASS,
              'property' => 'name',
         ));

      $field->bind('2');

      $this->assertTrue($field->isSynchronized());
      $this->assertEquals(4, $field->getData());
      $this->assertSame(false, $field['4']->getData());
      $this->assertSame(true, $field['2']->getData());
      $this->assertSame('', $field['4']->getClientData());
      $this->assertSame('1', $field['2']->getClientData());
   }

   public function testSubmitMultipleExpanded()
   {
      $entity1 = new SingleIdentBitPowerEntity(1, 'Foo', 0);
      $entity2 = new SingleIdentBitPowerEntity(2, 'Bar', 1);
      $entity3 = new SingleIdentBitPowerEntity(3, 'Bar', 2);

      $this->persist(array ($entity1, $entity2, $entity3));

      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'em' => 'default',
              'class' => self::SINGLE_IDENT_BIT_POWER_CLASS,
              'property' => 'name',
         ));

      $field->bind(array ('0' => '0', '2' => '2'));

      $expected = 5;

      $this->assertTrue($field->isSynchronized());
      $this->assertEquals($expected, $field->getData());
      $this->assertSame(true, $field['0']->getData());
      $this->assertSame(false, $field['1']->getData());
      $this->assertSame(true, $field['2']->getData());
      $this->assertSame('1', $field['0']->getClientData());
      $this->assertSame('', $field['1']->getClientData());
      $this->assertSame('1', $field['2']->getClientData());
   }

   public function testOverrideChoices()
   {
      $entity1 = new SingleIdentBitPowerEntity(1, 'Foo', 2);
      $entity2 = new SingleIdentBitPowerEntity(2, 'Bar', 3);
      $entity3 = new SingleIdentBitPowerEntity(3, 'Baz', 4);

      $this->persist(array ($entity1, $entity2, $entity3));

      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'em' => 'default',
              'class' => self::SINGLE_IDENT_BIT_POWER_CLASS,
              'multiple' => false,
              'expanded' => false,
              // not all persisted entities should be displayed
              'choices' => array ($entity1, $entity2),
              'property' => 'name',
         ));

      $field->bind('3');

      $this->assertEquals(array (2 => 'Foo', 3 => 'Bar'), $field->createView()->get('choices'));
      $this->assertTrue($field->isSynchronized());
      $this->assertEquals(8, $field->getData());
      $this->assertEquals(3, $field->getClientData());
   }

   public function testDisallowChoicesThatAreNotIncluded_choicesSingleIdentifier()
   {
      $entity1 = new SingleIdentBitPowerEntity(1, 'Foo', 0);
      $entity2 = new SingleIdentBitPowerEntity(2, 'Bar', 1);
      $entity3 = new SingleIdentBitPowerEntity(3, 'Baz', 2);

      $this->persist(array ($entity1, $entity2, $entity3));

      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'em' => 'default',
              'multiple' => false,
              'expanded' => false,
              'class' => self::SINGLE_IDENT_BIT_POWER_CLASS,
              'choices' => array ($entity1, $entity2),
              'property' => 'name',
         ));

      $field->bind('2');

      $this->assertFalse($field->isSynchronized());
      $this->assertNull($field->getData());
   }

   public function testDisallowChoicesThatAreNotIncluded_choicesCompositeIdentifier()
   {
      $entity1 = new CompositeIdentBitPowerEntity(10, 20, 'Foo', 0);
      $entity2 = new CompositeIdentBitPowerEntity(30, 40, 'Bar', 1);
      $entity3 = new CompositeIdentBitPowerEntity(50, 60, 'Baz', 2);

      $this->persist(array ($entity1, $entity2, $entity3));

      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'em' => 'default',
              'multiple' => false,
              'expanded' => false,
              'class' => self::COMPOSITE_IDENT_BIT_POWER_CLASS,
              'choices' => array ($entity1, $entity2),
              'property' => 'name',
         ));

      $field->bind('2');

      $this->assertFalse($field->isSynchronized());
      $this->assertNull($field->getData());
   }

   public function testDisallowChoicesThatAreNotIncludedQueryBuilderSingleIdentifier()
   {
      $entity1 = new SingleIdentBitPowerEntity(1, 'Foo', 0);
      $entity2 = new SingleIdentBitPowerEntity(2, 'Bar', 1);
      $entity3 = new SingleIdentBitPowerEntity(3, 'Baz', 2);

      $this->persist(array ($entity1, $entity2, $entity3));

      $repository = $this->em->getRepository(self::SINGLE_IDENT_BIT_POWER_CLASS);

      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'em' => 'default',
              'multiple' => false,
              'expanded' => false,
              'class' => self::SINGLE_IDENT_BIT_POWER_CLASS,
              'query_builder' => $repository->createQueryBuilder('e')
                 ->where('e.id IN (1, 2)'),
              'property' => 'name',
         ));

      $field->bind('2');

      $this->assertFalse($field->isSynchronized());
      $this->assertNull($field->getData());
   }

   public function testDisallowChoicesThatAreNotIncludedQueryBuilderAsClosureSingleIdentifier()
   {
      $entity1 = new SingleIdentBitPowerEntity(1, 'Foo', 0);
      $entity2 = new SingleIdentBitPowerEntity(2, 'Bar', 1);
      $entity3 = new SingleIdentBitPowerEntity(3, 'Baz', 2);

      $this->persist(array ($entity1, $entity2, $entity3));

      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'em' => 'default',
              'multiple' => false,
              'expanded' => false,
              'class' => self::SINGLE_IDENT_BIT_POWER_CLASS,
              'query_builder' => function ($repository)
              {
                 return $repository->createQueryBuilder('e')
                       ->where('e.id IN (1, 2)');
              },
              'property' => 'name',
         ));

      $field->bind('2');

      $this->assertFalse($field->isSynchronized());
      $this->assertNull($field->getData());
   }

   public function testDisallowChoicesThatAreNotIncludedQueryBuilderAsClosureCompositeIdentifier()
   {
      $entity1 = new CompositeIdentBitPowerEntity(10, 20, 'Foo', 0);
      $entity2 = new CompositeIdentBitPowerEntity(30, 40, 'Bar', 1);
      $entity3 = new CompositeIdentBitPowerEntity(50, 60, 'Baz', 2);

      $this->persist(array ($entity1, $entity2, $entity3));

      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'em' => 'default',
              'multiple' => false,
              'expanded' => false,
              'class' => self::COMPOSITE_IDENT_BIT_POWER_CLASS,
              'query_builder' => function ($repository)
              {
                 return $repository->createQueryBuilder('e')
                       ->where('e.id1 IN (10, 50)');
              },
              'property' => 'name',
         ));

      $field->bind('1');

      $this->assertFalse($field->isSynchronized());
      $this->assertNull($field->getData());
   }

   public function testSubmitSingleStringIdentifier()
   {
      $entity1 = new SingleStringIdentBitPowerEntity('foo', 'Foo', 0);

      $this->persist(array ($entity1));

      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'multiple' => false,
              'expanded' => false,
              'em' => 'default',
              'class' => self::SINGLE_STRING_IDENT_BIT_POWER_CLASS,
              'property' => 'name',
         ));

      $field->bind(0);

      $this->assertTrue($field->isSynchronized());
      $this->assertEquals(1, $field->getData());
      $this->assertEquals(0, $field->getClientData());
   }

   public function testSubmitCompositeStringIdentifier()
   {
      $entity1 = new CompositeStringIdentBitPowerEntity('foo1', 'foo2', 'Foo', 4);

      $this->persist(array ($entity1));

      $field = $this->factory->createNamed('entitybit', 'name', null, array (
              'multiple' => false,
              'expanded' => false,
              'em' => 'default',
              'class' => self::COMPOSITE_STRING_IDENT_BIT_POWER_CLASS,
              'property' => 'name',
         ));

      // the collection key is used here
      $field->bind('4');

      $this->assertTrue($field->isSynchronized());
      $this->assertEquals(16, $field->getData());
      $this->assertEquals(4, $field->getClientData());
   }

   protected

   function createRegistryMock($name, $em)
   {
      $registry = $this->getMock('Symfony\Bridge\Doctrine\RegistryInterface');
      $registry->expects($this->any())
         ->method('getEntityManager')
         ->with($this->equalTo($name))
         ->will($this->returnValue($em));

      return $registry;
   }

}

