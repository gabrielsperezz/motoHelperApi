<?php

namespace MotoHelper;

use Silex\Application;
use Silex\ControllerProviderInterface;
use MotoHelper\Helper\Cookie;
use MotoHelper\Entity\AccessToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class App implements ControllerProviderInterface
{
    private $noAuthCalls = ['login_app','login_post_app'];
    
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        Controller\App\Login::addRoutes($controllers);
        Controller\App\Home::addRoutes($controllers);
        Controller\App\VeiculoController::addRoutes($controllers);
        Controller\App\MotoboyController::addRoutes($controllers);
        
        $controllers->before(function (Request $request) use ($app) {
            $uri    = $request->get('_route');

            if (in_array($uri, $this->noAuthCalls)) {
                return;
            }
            
            try {
                $serviceToken = new Services\AccessTokenService($app['orm.em']->getRepository(AccessToken::class));

                $token = $serviceToken->getToken(Cookie::getCookieApp($app, $request));

                if (is_null($token)) {
                    return $this->getResponseUnauthorized($app, $request);
                }else{
                    $login = $token->getLogin();
                    $app['usuario'] = $login->toArray();
                }
                
                $app['token'] = $token;

            }catch (\Exception $ex) {
                $app['logger']->critical($ex->getMessage());
                return $this->getErrorResponse($app, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
        });
        
        $app->after(function (Request $request, Response $response) use ($app) {
            
            $token = isset($app['token']) ? $app['token'] : null;
            
            if (!is_null($token)) {
                if($token->getTipo() == AccessToken::TIPO_APP){
                    Cookie::setCookieApp($token->getToken(), $response);
                }
            }
            
        });
        
        return $controllers;
    }

    private function getResponseUnauthorized(Application $app, Request $request)
    {
        $response = $app->redirect('/login');
        
        if (strpos($request->headers->get('Accept'), 'application/json') !== false) {
            $response = new JsonResponse(Response::$statusTexts[Response::HTTP_UNAUTHORIZED], Response::HTTP_UNAUTHORIZED);
        }
        
        return $response;
    }
    
    private function getErrorResponse(Application $app, $code)
    {
        $acceptJson = (strpos($app['request']->headers->get('Accept'), 'application/json') === false);
        
        if ($code == 404) {
            return new Response($app['twig']->render('errros/404.html.twig'),  Response::HTTP_NOT_FOUND);
        }

        if ($code == 403) {
            return new Response($app['twig']->render('errros/block.html.twig'),  Response::HTTP_NOT_FOUND);
        }
        
        if ($code == 500) {
            $response = new JsonResponse(Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR], Response::HTTP_INTERNAL_SERVER_ERROR);
            if ($acceptJson) {
                $response = new Response($app['twig']->render('errros/500.html.twig'),  Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return $response;
        }

        if ($code == 401) {
            return $this->getResponseUnauthorized($app, $app['request']);
        }
        
        return null;
    }
}
