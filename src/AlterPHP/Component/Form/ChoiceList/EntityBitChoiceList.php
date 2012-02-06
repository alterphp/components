<?php

namespace AlterPHP\Component\Form\ChoiceList;

use Symfony\Component\Form\Util\PropertyPath;
use Symfony\Component\Form\Exception\FormException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\NoResultException;

class EntityBitChoiceList extends ObjectChoiceList
{

   /**
    * @var Doctrine\ORM\EntityManager
    */
   private $em;

   /**
    * @var Doctrine\ORM\Mapping\ClassMetadata
    */
   private $class;

   /**
    * The entities from which the user can choose
    *
    * This array is either indexed by ID (if the ID is a single field)
    * or by key in the choices array (if the ID consists of multiple fields)
    *
    * This property is initialized by initializeChoices(). It should only
    * be accessed through getEntity() and getEntities().
    *
    * @var Collection
    */
   private $entities = array ();

   /**
    * Contains the query builder that builds the query for fetching the
    * entities
    *
    * This property should only be accessed through queryBuilder.
    *
    * @var Doctrine\ORM\QueryBuilder
    */
   private $queryBuilder;

   /**
    * The fields of which the identifier of the underlying class consists
    *
    * This property should only be accessed through identifier.
    *
    * @var array
    */
   private $identifier = array ();

   /**
    * A cache for the UnitOfWork instance of Doctrine
    *
    * @var Doctrine\ORM\UnitOfWork
    */
   private $unitOfWork;
   private $propertyPath;
   private $bitPowerPropertyPath;

   /**
    * Constructor.
    *
    * @param EntityManager         $em               An EntityManager instance
    * @param string                $class            The class name
    * @param string                $bitPowerProperty The property name
    * @param string                $property         The property name
    * @param QueryBuilder|\Closure $queryBuilder     An optional query builder
    * @param array|\Closure        $choices          An array of choices or a function returning an array
    */
   public function __construct(
   EntityManager $em, $class, $bitPowerProperty, $property = null, $queryBuilder = null, $choices = null
   )
   {
      // If a query builder was passed, it must be a closure or QueryBuilder
      // instance
      if (!(null === $queryBuilder || $queryBuilder instanceof QueryBuilder || $queryBuilder instanceof \Closure))
      {
         throw new UnexpectedTypeException($queryBuilder, 'Doctrine\ORM\QueryBuilder or \Closure');
      }

      if ($queryBuilder instanceof \Closure)
      {
         $queryBuilder = $queryBuilder($em->getRepository($class));

         if (!$queryBuilder instanceof QueryBuilder)
         {
            throw new UnexpectedTypeException($queryBuilder, 'Doctrine\ORM\QueryBuilder');
         }
      }

      $this->em = $em;
      $this->class = $class;
      $this->queryBuilder = $queryBuilder;
      $this->unitOfWork = $em->getUnitOfWork();

      //Se sert-on de l'identifier du coup ?
      $this->identifier = $em->getClassMetadata($class)->getIdentifierFieldNames();

      // bitPower property
      if (! $em->getClassMetadata($class)->isUniqueField($bitPowerProperty))
      {
         throw new \InvalidArgumentException('The bitpower property of a entitybit field must be a unique column');
      }
      $this->bitPowerPropertyPath = new PropertyPath($bitPowerProperty);

      // The property option defines, which property (path) is used for
      // displaying entities as strings
      if ($property)
      {
         $this->propertyPath = new PropertyPath($property);
      }

      if (!is_array($choices) && !$choices instanceof \Closure && !is_null($choices))
      {
         throw new UnexpectedTypeException($choices, 'array or \Closure or null');
      }

      $this->choices = $choices;
   }

   /**
    * Initializes the choices and returns them.
    *
    * If the entities were passed in the "choices" option, this method
    * does not have any significant overhead. Otherwise, if a query builder
    * was passed in the "query_builder" option, this builder is now used
    * to construct a query which is executed. In the last case, all entities
    * for the underlying class are fetched from the repository.
    *
    * @return array  An array of choices
    */
   protected function load()
   {
      parent::load();

      if (is_array($this->choices))
      {
         $entities = $this->choices;
      }
      elseif ($qb = $this->queryBuilder)
      {
         $entities = $qb->getQuery()->execute();
      }
      else
      {
         $entities = $this->em->getRepository($this->class)->findAll();
      }

      $this->choices = array ();
      $this->entities = array ();

      $this->loadEntities($entities);

      return $this->choices;
   }

   /**
    * Converts entities into choices with support for groups.
    *
    * The choices are generated from the entities. If the entities have a
    * composite identifier, the choices are indexed using ascending integers.
    * Otherwise the identifiers are used as indices.
    *
    * If the option "property" was passed, the property path in that option
    * is used as option values. Otherwise this method tries to convert
    * objects to strings using __toString().
    *
    * @param array  $entities An array of entities
    * @param string $group    A group name
    */
   private function loadEntities($entities, $group = null)
   {
      foreach ($entities as $key => $entity)
      {
         if (is_array($entity))
         {
            // Entities are in named groups
            $this->loadEntities($entity, $key);
            continue;
         }

         if ($this->propertyPath)
         {
            // If the property option was given, use it
            $value = $this->propertyPath->getValue($entity);
         }
         else
         {
            // Otherwise expect a __toString() method in the entity
            if (!method_exists($entity, '__toString'))
            {
               throw new FormException('Entities passed to the choice field must have a "__toString()" method defined (or you can also override the "property" option).');
            }

            $value = (string) $entity;
         }

         $bitPower = $this->bitPowerPropertyPath->getValue($entity);

         if (null === $group)
         {
            // Flat list of choices
            $this->choices[$bitPower] = $value;
         }
         else
         {
            // Nested choices
            $this->choices[$group][$bitPower] = $value;
         }

         $this->entities[$bitPower] = $entity;
      }
   }

   /**
    * Returns the according entities for the choices.
    *
    * If the choices were not initialized, they are initialized now. This
    * is an expensive operation, except if the entities were passed in the
    * "choices" option.
    *
    * @return array  An array of entities
    */
   public function getEntities()
   {
      if (!$this->loaded)
      {
         $this->load();
      }

      return $this->entities;
   }

   /**
    * Returns the entity for the given key.
    *
    * If the underlying entities have composite identifiers, the choices
    * are initialized. The key is expected to be the index in the choices
    * array in this case.
    *
    * If they have single identifiers, they are either fetched from the
    * internal entity cache (if filled) or loaded from the database.
    *
    * @param  string $key The choice key (for entities with composite
    *                     identifiers) or entity ID (for entities with single
    *                     identifiers)
    *
    * @return object      The matching entity
    */
   public function getEntity($key)
   {
      if (!$this->loaded)
      {
         $this->load();
      }

      try
      {
         if (count($this->identifier) > 1)
         {
            // $key is a collection index
            $entities = $this->getEntities();

            return isset($entities[$key]) ? $entities[$key] : null;
         }
         elseif ($this->entities)
         {
            return isset($this->entities[$key]) ? $this->entities[$key] : null;
         }
         elseif ($qb = $this->queryBuilder)
         {
            // should we clone the builder?
            $alias = $qb->getRootAlias();

            $where = $qb->expr()->eq($alias . '.' . $this->bitPowerPropertyPath, $key);

            return $qb->andWhere($where)->getQuery()->getSingleResult();
         }

         return $this->em->getRepository($this->class)->findOneBy(array($this->bitPowerPropertyPath => $key));
      }
      catch (NoResultException $e)
      {
         return null;
      }
   }

   /**
    * Returns the values of the bitPower field of an entity.
    *
    * Doctrine must know about this entity, that is, the entity must already
    * be persisted or added to the identity map before. Otherwise an
    * exception is thrown.
    *
    * @param  object $entity The entity for which to get the identifier
    *
    * @return integer          The identifier value
    *
    * @throws FormException  If the entity does not exist in Doctrine's identity map
    */
   public function getBitPowerValue($entity)
   {
      if (!$this->unitOfWork->isInIdentityMap($entity))
      {
         throw new FormException('Entities passed to the choice field must be managed');
      }

      return $this->bitPowerPropertyPath->getValue($entity);
   }

}
