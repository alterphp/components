<?php

namespace AlterPHP\Tests\Component\HttpFoundation;

use AlterPHP\Component\HttpFoundation\RedirectResponseWithCookie;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * RedirectResponseWithCookie represents an HTTP response doing a redirect and sending cookies
 * @author     pcb <pc.bertineau@alterphp.com>
 */
class RedirectResponseWithCookieTest extends \PHPUnit_Framework_TestCase
{

   public function testIsRedirectAndHasCookieRedirectionWithCookie()
   {
      $url = '/';
      $cookie = new Cookie('test_cookie_name', 'test_cookie_value', 0, $url, 'test_cookie_domain');

      foreach (array (301, 302, 303, 307) as $code)
      {
         $response = new RedirectResponseWithCookie($url, $code, array($cookie));
         $this->assertTrue($response->isRedirection());
         $this->assertTrue($response->isRedirect());

         $responseCookies = $response->headers->getCookies();
         $this->assertEquals($cookie, $responseCookies[0]);
      }
   }

}
