<?php

namespace AlterPHP\Tests\Component\Form\ChoiceList;

use AlterPHP\Tests\Component\Fixtures\SingleIdentBitWeightEntity;
use Symfony\Bridge\Doctrine\Tests\DoctrineOrmTestCase;
use AlterPHP\Component\Form\ChoiceList\EntityBitChoiceList;

class EntityBitChoiceListTest extends DoctrineOrmTestCase
{
    const SINGLE_IDENT_BIT_WEIGHT_CLASS = 'AlterPHP\Tests\Component\Fixtures\SingleIdentBitWeightEntity';

    private $em;

    protected function setUp()
    {
        parent::setUp();

        $this->em = $this->createTestEntityManager();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->em = null;
    }

    /**
     * @expectedException Symfony\Component\Form\Exception\FormException
     */
    public function testChoicesMustBeManaged()
    {
        $entity1 = new SingleIdentBitWeightEntity(1, 'Foo', 0);
        $entity2 = new SingleIdentBitWeightEntity(2, 'Bar', 1);

        // no persist here!

        $choiceList = new EntityBitChoiceList(
            $this->em,
            self::SINGLE_IDENT_BIT_WEIGHT_CLASS,
            'bitWeight',
            'name',
            null,
            array(
                $entity1,
                $entity2,
            )
        );

        // triggers loading -> exception
        $choiceList->getChoices();
    }

    public function testFlattenedChoicesAreManaged()
    {
        $entity1 = new SingleIdentBitWeightEntity(1, 'Foo', 2);
        $entity2 = new SingleIdentBitWeightEntity(2, 'Bar', 5);

        // Persist for managed state
        $this->em->persist($entity1);
        $this->em->persist($entity2);

        $choiceList = new EntityBitChoiceList(
            $this->em,
            self::SINGLE_IDENT_BIT_WEIGHT_CLASS,
            'bitWeight',
            'name',
            null,
            array(
                $entity1,
                $entity2,
            )
        );

        $this->assertSame(array(2 => 'Foo', 5 => 'Bar'), $choiceList->getChoices());
    }

    public function testEmptyChoicesAreManaged()
    {
        $entity1 = new SingleIdentBitWeightEntity(1, 'Foo', 0);
        $entity2 = new SingleIdentBitWeightEntity(2, 'Bar', 5);

        // Persist for managed state
        $this->em->persist($entity1);
        $this->em->persist($entity2);

        $choiceList = new EntityBitChoiceList(
            $this->em,
            self::SINGLE_IDENT_BIT_WEIGHT_CLASS,
            'bitWeight',
            'name',
            null,
            array()
        );

        $this->assertSame(array(), $choiceList->getChoices());
    }

    public function testNestedChoicesAreManaged()
    {
        $entity1 = new SingleIdentBitWeightEntity(1, 'Foo', 3);
        $entity2 = new SingleIdentBitWeightEntity(2, 'Bar', 6);

        // Oh yeah, we're persisting with fire now!
        $this->em->persist($entity1);
        $this->em->persist($entity2);

        $choiceList = new EntityBitChoiceList(
            $this->em,
            self::SINGLE_IDENT_BIT_WEIGHT_CLASS,
            'bitWeight',
            'name',
            null,
            array(
                'group1' => array($entity1),
                'group2' => array($entity2),
            )
        );

        $this->assertSame(array(
            'group1' => array(3 => 'Foo'),
            'group2' => array(6 => 'Bar')
        ), $choiceList->getChoices());
    }
}
