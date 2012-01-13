<?php

require_once __DIR__.'/src/AlterPHP/Component/HttpFoundation/RedirectResponseWithCookie.php';

namespace AlterPHP\Tests\Component\HttpFoundation;

use AlterPHP\Component\HttpFoundation\RedirectResponseWithCookie;

/**
 * RedirectResponseWithCookie represents an HTTP response doing a redirect and sending cookies
 * @author     pcb <pc.bertineau@alterphp.com>
 */
class RedirectResponseWithCookieTest extends \PHPUnit_Framework_TestCase
{
   
    public function testIsRedirectAndHasCookieRedirectionWithCookie()
    {
       //TODO: Ajouter les Cookie

        foreach (array(301, 302, 303, 307) as $code)
        {
            $response = new RedirectResponseWithCookie('', $code);
            $this->assertTrue($response->isRedirection());
            $this->assertTrue($response->isRedirect());
        }

        //TODO : expect \InvalidArgumentException
        $response = new RedirectResponseWithCookie('', 304);

        //TODO : expect \InvalidArgumentException
        $response = new RedirectResponseWithCookie('', 200);

        //TODO : expect \InvalidArgumentException
        $response = new RedirectResponseWithCookie('', 404);

        $response = new RedirectResponseWithCookie('', 301, array('Location' => '/good-uri'));
        $this->assertFalse($response->isRedirect('/bad-uri'));
        $this->assertTrue($response->isRedirect('/good-uri'));
    }
}
