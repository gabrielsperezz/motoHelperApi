<?php

namespace MotoHelper\Controller\ApiMobile;

use Doctrine\DBAL\Types\Type;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use MongoId;
use MotoHelper\Entity\LoginMotoboy;
use Silex\Application;
use MotoHelper\Entity\Login as LoginEntity;
use MotoHelper\Entity\LoginPosicoes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class User extends ApiMobileAbstract
{

    public static function addRoutes($routing)
    {
        $routing->get('/user/me', array(new self(), 'buscarInformacoesDoUsuario'))->bind('buscar_minhas_informacoes');
    }

    public function buscarInformacoesDoUsuario(Application $app)
    {

        $response = new JsonResponse();

        try {

            $login = $app['user'];

            $response->setData($login->toArray());
            $response->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            $app['logger']->critical($ex->getMessage());
        }

        return $response;
    }

}