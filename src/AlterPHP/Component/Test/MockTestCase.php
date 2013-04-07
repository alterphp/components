<?php

namespace AlterPHP\Component\Test;

use Symfony\Bundle\FrameworkBundle\Tests\WebTestCase as BaseTestCase;

/**
 * MockTestCase is the base class for unit tests in Symfony2 projects
 * @package    AlterPHP.Component
 * @subpackage Test
 * @author     pcb <pc.bertineau@alterphp.com>
 */
abstract class MockTestCase extends BaseTestCase
{

   /**
    * Container
    * @var PHPUnit_Framework_MockObject_MockObject
    */
   protected $container;

   /**
    * EntityManager de Doctrine
    * @var PHPUnit_Framework_MockObject_MockObject
    */
   protected $em;

   /**
    * Connection de doctrine
    * @var PHPUnit_Framework_MockObject_MockObject
    */
   protected $conn;

   /**
    * SecutityContext (renvoie le user avec le caId 42)
    * @var PHPUnit_Framework_MockObject_MockObject
    */
   protected $security;

   /**
    * Logger pour les tests avec Mocks
    * @var Symfony\Component\HttpKernel\Log\LoggerInterface
    */
   protected $logger;

   /**
    * Translator
    * @var PHPUnit_Framework_MockObject_MockObject
    */
   protected $translator;

   /**
    * FormFactory
    * @var PHPUnit_Framework_MockObject_MockObject
    */
   protected $formFactory;

   /**
    * FormBuilder
    * @var PHPUnit_Framework_MockObject_MockObject
    */
   protected $formBuilder;

   /**
    * EventDispatcher
    * @var PHPUnit_Framework_MockObject_MockObject
    */
   protected $eventDispatcher;

   /**
    * SwiftMailer
    * @var PHPUnit_Framework_MockObject_MockObject
    */
   protected $mailer;

   /**
    * TwigEngine
    * @var PHPUnit_Framework_MockObject_MockObject
    */
   protected $twig;

   /**
    * Router
    * @var PHPUnit_Framework_MockObject_MockObject
    */
   protected $router;

   /**
    * Session
    * @var PHPUnit_Framework_MockObject_MockObject
    */
   protected $session;

   /**
    * UsernamePasswordToken
    * @var PHPUnit_Framework_MockObject_MockObject
    */
   protected $token;

   /**
    * User
    * @var PHPUnit_Framework_MockObject_MockObject
    */
   protected $user;

   const LOGGED_USERNAME = 'test-user@test-domain.com';

   /**
    * Creates a TestLogger.
    *
    * @param array   $options An array of options to pass to the createKernel class
    * @param array   $server  An array of server parameters
    *
    * @return Client A Logger instance
    */
   protected function getKernelServices(array $options = array ())
   {
      static::$kernel = static::createKernel($options);
      static::$kernel->boot();

      $this->logger = static::$kernel->getContainer()->get('logger');
//      $this->router = static::$kernel->getContainer()->get('router');
   }

   /**
    * @return void
    */
   public function setUp()
   {
      parent::setUp();

      $this->getKernelServices();

      //init log
      $this->logger->info('######## Starting ' . $this->getName() . ' at '. time() .' ########');

      $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
         ->disableOriginalConstructor()
         ->setMethods(array ('get'))
         ->getMock();

      $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
         ->disableOriginalConstructor()
         ->setMethods(
            array (
                    'getRepository', 'clear', 'persist', 'remove', 'flush', 'createQuery', 'getClassMetadata',
                    'getConnection', 'getReference', 'begintransaction', 'commit', 'rollback'
            )
         )
         ->getMock();

      $this->conn = $this->getMockBuilder('Doctrine\DBAL\Connection')
         ->disableOriginalConstructor()
         ->setMethods(array ('executeQuery', 'executeUpdate'))
         ->getMock();

      $this->security = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
         ->disableOriginalConstructor()
         ->setMethods(array ('getToken', 'isGranted', 'setToken'))
         ->getMock();

      $this->translator = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Translation\Translator')
         ->disableOriginalConstructor()
         ->setMethods(array ('trans'))
         ->getMock();

      $this->formFactory = $this->getMockBuilder('Symfony\Component\Form\FormFactory')
         ->disableOriginalConstructor()
         ->setMethods(array ('createBuilder'))
         ->getMock();

      $this->formBuilder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
         ->disableOriginalConstructor()
         ->setMethods(array ('add', 'get', 'getForm', 'setData'))   // A compléter si besoin !
         ->getMock();

      $this->eventDispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcher')
         ->disableOriginalConstructor()
         ->setMethods(array ())   // A compléter si besoin !
         ->getMock();

      $this->mailer = $this->getMockBuilder('\Swift_Mailer')
         ->disableOriginalConstructor()
         ->setMethods(array ('send'))   // A compléter si besoin !
         ->getMock();

      $this->twig = $this->getMockBuilder('Symfony\Bundle\TwigBundle\TwigEngine')
         ->disableOriginalConstructor()
         ->setMethods(array ('render', 'renderResponse', 'exists', 'supports'))
         ->getMock();

      $this->router = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Routing\Router')
         ->disableOriginalConstructor()
         ->setMethods(array ('generate', 'match'))
         ->getMock();

      $this->session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session')
         ->disableOriginalConstructor()
         ->setMethods(
            array (
                    'has', 'get', 'set', 'remove', 'clear', 'getLocale', 'setLocale', 'getFlashes', 'setFlashes',
                    'getFlash', 'setFlash', 'hasFlash', 'removeFlash', 'clearFlashes', 'all', 'replace', 'invalidate'
            )
         )
         ->getMock();

      $this->initializeLoggedUser();
   }

   public function tearDown()
   {
      //close log
      $this->logger->info('#### Ending ' . $this->getName() . ' at '. time() .' ####');

   }

   /**
    * Chaque classe de test étendant MockTestCase doit implémenter une méthode protected getInstance servant à
    * appeler le constructeur et retourner une instance de la classe testée.
    */
   abstract protected function getInstance();

   /**
    * Chaque classe de test étendant MockTestCase doit implémenter une méthode publique testant le constructeur
    * de la classe testée et vérifiant le type de l'objet retourné.
    */
   abstract public function testConstructor();

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
      $this->user = $this->getMockBuilder('Symfony\Component\Security\Core\User\User')
         ->disableOriginalConstructor()
         ->setMethods(array ('getUsername'))
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
         ->method('getUsername')
         ->will($this->returnValue(self::LOGGED_USERNAME));
   }

   /**
    * Raccourci pour définir les "attendus" d'une méthode mockée
    * @param \PHPUnit_Framework_MockObject_MockObject $mockObject
    * @param mixed  $invocation Array avec nom de l'invocation en 0 et param en 1, sinon string du nom de l'invocation
    * @param string $method     Nom de la méthode mockée
    * @param array  $with       Tableau des paramètres passés
    * @param mixed  $will       Valeur retournée par le mock
    */
   protected function setExpectation(
   \PHPUnit_Framework_MockObject_MockObject $mockObject, $invocation, $method, array $with = null, $will = null
   )
   {
      //########### On traite l'invocation et la méthode mockée ###########
      //Cas avec argument : exactly | at
      if (is_array($invocation))
      {
         $mockInvocation = call_user_func_array(array ($this, $invocation[0]), array ($invocation[1]));
      }
      //Cas sans argument : any | never | atLeastOnce | once
      else
      {
         $mockInvocation = call_user_func(array ($this, $invocation));
      }
      $expectation = $mockObject->expects($mockInvocation)->method($method);

      //########### On traite les arguments à passer mockée ###########
      if (isset($with))
      {
         call_user_func_array(array ($expectation, 'with'), $with);
      }

      //########### On traite les arguments à passer mockée ###########
      if (isset($will))
      {
         if ($will instanceof \Exception)
         {
            $mockExpected = call_user_func(array ($this, 'throwException'), $will);
         }
         else
         {
            $mockExpected = call_user_func(array ($this, 'returnValue'), $will);
         }
         $expectation->will($mockExpected);
      }
   }

   /**
    * Retourne un objet Repository mocké
    * @param array $additionalMethods
    * @return PHPUnit_Framework_MockObject_MockObject
    */
   protected function getMockRepository(array $additionalMethods = array ())
   {
      $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
         ->disableOriginalConstructor()
         ->setMethods(array_merge(array ('find', 'findAll', 'findBy', 'findOneBy'), $additionalMethods))
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
              'setParameter', 'setParameters', 'getResult', 'getArrayResult', 'getScalarResult', 'getSingleResult',
              'getSingleScalarResult', 'getOneOrNullResult', 'setMaxResults', 'execute'
      );

      $query = $this->getMock('Doctrine\ORM\AbstractQuery', $methods, array ($this->em), '', true);

      $query->expects($this->any())
         ->method('setParameter')
         ->will($this->returnValue($query));
      $query->expects($this->any())
         ->method('setParameters')
         ->will($this->returnValue($query));
      $query->expects($this->any())
         ->method('setMaxResults')
         ->will($this->returnValue($query));

      return $query;
   }

   /**
    * Retourne un objet Statement mocké
    * @return PHPUnit_Framework_MockObject_MockObject
    */
   protected function getMockStatement()
   {
      $methods = array ('fetchAll', 'fetch');

      $stmt = $this->getMock('Doctrine\DBAL\Driver\Statement', $methods, array (\PDO::FETCH_ASSOC), '', true);

      return $stmt;
   }

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
   $callOriginalConstructor = TRUE, $callOriginalClone = TRUE, $callAutoload = TRUE, $cloneArguments = FALSE
   )
   {
      if ($methods !== null)
      {
         $methods = array_unique(array_merge($methods, self::getAbstractMethods($originalClassName, $callAutoload)));
      }

      return parent::getMock(
            $originalClassName, $methods, $arguments, $mockClassName, $callOriginalConstructor, $callOriginalClone,
            $callAutoload, $cloneArguments
      );
   }

   /**
    * Returns an array containing the names of the abstract methods in <code>$class</code>.
    *
    * @param string  $class name of the class
    * @param boolean $autoload
    * @return array zero or more abstract methods names
    */
   private static function getAbstractMethods($class, $autoload = true)
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
