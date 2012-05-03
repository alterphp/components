<?php

namespace Sellermania\SellerBundle\Component\OverSf2\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Link;

/**
 * WebTestCase is the base class for functional tests in Symfony2 projects
 * @package    AlterPHP.Component
 * @subpackage Test
 * @author     pcb <pc.bertineau@alterphp.com>
 */
class WebTestCase extends BaseWebTestCase
{

   /**
    * @var \Doctrine\ORM\EntityManager
    */
   protected $em;

   /**
    * @var \Symfony\Bundle\FrameworkBundle\Client
    * client qui simule la navigation
    */
   protected $client;

   /**
    * @var Router
    * client qui simule la navigation
    */
   protected $router;

   /**
    *
    * @var <type> translator instance
    */
   protected $t;

   /**
    * @var
    * Logger
    */
   protected $logger;

   /**
    * Prépare chaque test (réinitialise le client, affecte les membres de l'instance de la classe)
    * @return void
    */
   public function setUp()
   {
      parent::setUp();

      if (isset($this->client))
      {
         $this->client = null;
         unset($this->client);
      }

      $this->client = $this->createClient();

      //XXX: Surveiller ce bug : https://github.com/symfony/symfony/issues/1726
      //$this->client->insulate();
      //On force la redirection
      $this->client->followRedirects();

      //On invalide la session...
      $session = $this->client->getContainer()->get('session');
      if (isset($session))
      {
         $session->invalidate();
      }

      $this->em = $this->client->getContainer()->get('doctrine')->getEntityManager();

      $this->t = $this->client->getContainer()->get('translator');
      $this->logger = $this->client->getContainer()->get('logger');
      $this->router = $this->client->getContainer()->get('router');

      $this->logger->info('############## Starting ' . $this->getName() . ' ##############');
   }

   /**
    * Asserts that an IDtag exists and is unique.
    * @param string  $route
    * @param Crawler $crawler
    * @return void
    */
   protected function assertIdTag($route, Crawler $crawler)
   {
      $ids = $crawler->filter('div.IDtag');
      $this->assertEquals(1, $ids->count(), 'Il y a ' . $ids->count() . ' IDTag !');
      $routeActual = $ids->extract('id');
      $this->assertEquals($route, $routeActual[0]);
   }

   /**
    * Asserts that a link exists, and is unique.
    * @param string  $linkId
    * @param Crawler $crawler
    * @return void
    */
   protected function assertLink($linkId, Crawler $crawler)
   {
      $links = $crawler->filter('a#' . $linkId);
      $this->assertEquals(1, $links->count());
   }

   /**
    * Asserts that a link exists, is unique and returns it.
    * @param string  $linkId
    * @param Crawler $crawler
    * @return Link A Link object
    */
   protected function assertAndGetLink($linkId, Crawler $crawler)
   {
      $links = $crawler->filter('a#' . $linkId);
      $this->assertEquals(1, $links->count());

      return $links->link();
   }

}
