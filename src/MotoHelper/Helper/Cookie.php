<?php
namespace MotoHelper\Helper;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie as CookieSymfony;

class Cookie
{
    
    const COOKIE_NAME_ADMIN = "admin";
    const COOKIE_NAME_APP = "app";
    
    public static function getCookie(Application $app, Request $request,  $cookiename = self::COOKIE_NAME_ADMIN)
    {
        $cookie = null;
        
        if ($request->cookies->has($cookiename)) {
            $cookiecrpt = $request->cookies->get($cookiename);
            $cookie = base64_decode($cookiecrpt);
        }
        
        return $cookie;
    }

    public static function getCookieApp(Application $app, Request $request,  $cookiename = self::COOKIE_NAME_APP)
    {
        $cookie = null;

        if ($request->cookies->has($cookiename)) {
            $cookiecrpt = $request->cookies->get($cookiename);
            $cookie = base64_decode($cookiecrpt);
        }

        return $cookie;
    }

    public static function setCookie($valueCookie,Response $response, $cookiename = self::COOKIE_NAME_ADMIN)
    {
        $valueCookieCrpt = base64_encode($valueCookie);
        $cookie = new CookieSymfony($cookiename, $valueCookieCrpt);

        $response->headers->setCookie($cookie);
    }
    
    public static function setCookieApp($valueCookie,Response $response, $cookiename = self::COOKIE_NAME_APP)
    {
        $valueCookieCrpt = base64_encode($valueCookie);
        $cookie = new CookieSymfony($cookiename, $valueCookieCrpt);
        
        $response->headers->setCookie($cookie);
    }
}
