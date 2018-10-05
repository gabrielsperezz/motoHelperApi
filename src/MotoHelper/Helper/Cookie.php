<?php
namespace MotoHelper\Helper;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie as CookieSymfony;

class Cookie
{
    
    const COOKIE_NAME = "terceiro_semestre";
    
    public static function getCookie(Application $app, Request $request,  $cookiename = self::COOKIE_NAME)
    {
        $cookie = null;
        
        if ($request->cookies->has($cookiename)) {
            $cookiecrpt = $request->cookies->get($cookiename);
            $cookie = base64_decode($cookiecrpt);
        }
        
        return $cookie;
    }
    
    public static function setCookie($valueCookie,Response $response, $cookiename = self::COOKIE_NAME)
    {
        $valueCookieCrpt = base64_encode($valueCookie);
        $cookie = new CookieSymfony($cookiename, $valueCookieCrpt);
        
        $response->headers->setCookie($cookie);
    }
}
