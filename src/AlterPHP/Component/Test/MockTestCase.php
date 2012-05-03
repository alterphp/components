<?php

namespace Sellermania\SellerBundle\Component\OverSf2\Test;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase as BaseTestCase;

/**
 * MockTestCase is the base class for unit tests in Symfony2 projects
 * @package    SellerPhp.SellerBundle
 * @subpackage Component.OverSf2.Test
 * @author     pcb <pc.bertineau@alterphp.com>
 */
class MockTestCase extends BaseTestCase
{

   /**
    * Doctrine EntityManager
    * @var PHPUnit_Framework_MockObject_MockObject
    */
   protected $em;

   /**
    * Doctrine Configuration
    * @var PHPUnit_Framework_MockObject_MockObject
    */
   protected $doctrineConf;

   /**
    * SecutityContext (returns a User with ID 42)
    * @var PHPUnit_Framework_MockObject_MockObject
    */
   protected $security;

   /**
    * Logger
    * @var PHPUnit_Framework_MockObject_MockObject
    */
   protected $logger;
   protected $token;
   protected $user;

   const USERID = 42;

   /**
    * Prépare chaque test (initialise les Mock)
    * @return void
    */
   public function setUp()
   {
      parent::setUp();

      $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
         ->disableOriginalConstructor()
         ->setMethods(array ('getRepository', 'persist', 'remove', 'flush', 'createQuery', 'getClassMetadata'))
         ->getMock();

      $this->security = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
         ->disableOriginalConstructor()
         ->setMethods(array ('getToken', 'isGranted'))
         ->getMock();

      $this->logger = $this->getMockBuilder('Symfony\Bridge\Monolog\Logger')
         ->disableOriginalConstructor()
         ->setMethods(array ('debug', 'info', 'warn', 'err', 'crit', 'alert', 'notice', 'emerg'))
         ->getMock();

      $this->initializeLoggedUser();
   }

   /**
    * Initialise les Mock pour simuler le user loggé et l'affecte au SecurityContext mocké
    * @return void
    */
   private function initializeLoggedUser()
   {
      //On mocke le Token et les User simulé
      $this->token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken')
         ->disableOriginalConstructor()
         ->setMethods(array ('getUser'))
         ->getMock();
      $this->user = $this->getMockBuilder('Sellermania\SellerBundle\Entity\User')
         ->disableOriginalConstructor()
         ->setMethods(array ('getId'))
         ->getMock();

      //On fait renvoyer ce token par le SecurityContext mocké
      $this->security
         ->expects($this->any())
         ->method('getToken')
         ->will($this->returnValue($this->token));
      $this->token
         ->expects($this->any())
         ->method('getUser')
         ->will($this->returnValue($this->user));
      $this->user
         ->expects($this->any())
         ->method('getId')
         ->will($this->returnValue(self::USERID));
   }

   /**
    * Retourne un objet Repository mocké
    * @return PHPUnit_Framework_MockObject_MockObject
    */
   protected function getMockRepository()
   {
      $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
         ->disableOriginalConstructor()
         ->setMethods(array ('find', 'findAll', 'findBy', 'findOneBy'))
         ->getMock();

      return $repo;
   }

   /**
    * Retourne un objet Query mocké
    * @return PHPUnit_Framework_MockObject_MockObject
    */
   protected function getMockQuery()
   {
      $methods = array (
              'setParameter', 'getResult', 'getArrayResult', 'getScalarResult', 'getSingleResult',
              'getSingleScalarResult', 'getOneOrNullResult', 'setMaxResults'
      );

      $query = $this->getMock('Doctrine\ORM\AbstractQuery', $methods, array ($this->em), '', true);

      $query->expects($this->any())
         ->method('setParameter')
         ->will($this->returnValue($query));

      return $query;
   }

   /*
    * Following two methods comes from this stackoverflow question :
    * http://stackoverflow.com/questions/8040296/mocking-concrete-method-in-abstract-class-using-phpunit
    * It's been provided by David Harckness (http://stackoverflow.com/users/285873/david-harkness)
    */

   /**
    * Surcharge de la méthode getMock pour les mocks de classe abstraite
    * @param type $originalClassName
    * @param type $methods
    * @param array $arguments
    * @param type $mockClassName
    * @param type $callOriginalConstructor
    * @param type $callOriginalClone
    * @param type $callAutoload
    * @return type
    */
   public function getMock(
   $originalClassName, $methods = array (), array $arguments = array (), $mockClassName = '',
   $callOriginalConstructor = TRUE, $callOriginalClone = TRUE, $callAutoload = TRUE
   )
   {
      if ($methods !== null)
      {
         $methods = array_unique(array_merge($methods, self::getAbstractMethods($originalClassName, $callAutoload)));
      }
      return parent::getMock($originalClassName, $methods, $arguments, $mockClassName, $callOriginalConstructor,
                             $callOriginalClone, $callAutoload);
   }

   /**
    * Returns an array containing the names of the abstract methods in <code>$class</code>.
    *
    * @param string  $class name of the class
    * @param boolean $autoload
    * @return array zero or more abstract methods names
    */
   private static function getAbstractMethods($class, $autoload=true)
   {
      $methods = array ();
      if (class_exists($class, $autoload) || interface_exists($class, $autoload))
      {
         $reflector = new \ReflectionClass($class);
         foreach ($reflector->getMethods() as $method)
         {
            if ($method->isAbstract())
            {
               $methods[] = $method->getName();
            }
         }
      }
      return $methods;
   }

}
