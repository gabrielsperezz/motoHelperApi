<?php

namespace MotoHelper\Controller\ApiMobile;

use MongoId;
use Silex\Application;
use MotoHelper\Entity\Login;
use MotoHelper\Entity\LoginPosicoes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class Posicoes extends ApiMobileAbstract
{

    public static function addRoutes($routing)
    {
        $routing->put('/login/{id_login}/ultimaposicao', array(new self(), 'atualizarUltimaPosicaoLogin'))->bind('atualizar_ultima_posicao_login');
    }

    public function atualizarUltimaPosicaoLogin(Application $app, Request $request, $id_login)
    {

        $response = new JsonResponse();

        try {

            $latitude = $request->request->get("latitude");
            $longitude = $request->request->get("longitude");

            $login = $this->getUserInfo($id_login, $app);

            $posicoes = $login->getPosicoes();

            $posicoes->setLatitude($latitude);
            $posicoes->setLongitude($longitude);

            $em = $this->getEm($app);
            $this->publishMessage("ultimaposicao/". $id_login, $posicoes->toArray());
            $this->atualizarPosicaoEmCorrida($login, $posicoes, $app);
            $em->persist($login);
            $em->flush();

            $response->setData($posicoes->toArray());
            $response->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            $app['logger']->critical($ex->getMessage());
        }

        return $response;
    }

    private function atualizarPosicaoEmCorrida(Login $login, LoginPosicoes $loginPosicoes , $app)
    {
        if($login->getIdCorridaAtual()){
            $mongo = $this->getMongoDb($app);
            $idMongo = new MongoId($login->getIdCorridaAtual());
            if($login::$MOTOBOY == $login->getTipo()){
                $mongo->update(["_id" =>$idMongo], ['$push' => ["posicoes_motoboy" => $loginPosicoes->toArray()]]);
            }elseif ($login->getTipo()  == Login::$USUARIO){
                $mongo->update(["_id" =>$idMongo], ['$push' => ["posicoes_usuario" => $loginPosicoes->toArray()]]);
            }
        }
    }

}