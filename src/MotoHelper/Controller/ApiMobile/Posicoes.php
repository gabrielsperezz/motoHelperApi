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

class Posicoes extends ApiMobileAbstract
{

    public static function addRoutes($routing)
    {
        $routing->put('/login/{id_login}/ultimaposicao', array(new self(), 'atualizarUltimaPosicaoLogin'))->bind('atualizar_ultima_posicao_login');

        $routing->get('/motoboys/ultimaposicao', array(new self(), 'buscarUltimaPosicaoDeMotoboys'))->bind('buscar_ultima_posicao_motoboys');
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
            $posicoes->setLabel($this->getEnderecoPorLatLong($posicoes));

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

    public function buscarUltimaPosicaoDeMotoboys(Application $app)
    {

        $response = new JsonResponse();

        try {

            $em = $this->getEm($app);
            $motoboyRepository = $em->getRepository(LoginMotoboy::class);

            $posicoes = array_map(function ($motoboy){
                return $motoboy->toArray();
            }, $motoboyRepository->findAll());


            $response->setData($posicoes);
            $response->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            $app['logger']->critical($ex->getMessage());
        }

        return $response;
    }

    private function atualizarPosicaoEmCorrida(LoginEntity $login, LoginPosicoes $loginPosicoes , $app)
    {
        if($login->getIdCorridaAtual()){
            $mongo = $this->getMongoDb($app);

            $this->publishMessage("corrida/". $login->getIdCorridaAtual(). "/posicoes", $loginPosicoes->toArray());

            $idMongo = new MongoId($login->getIdCorridaAtual());
            if(LoginEntity::$MOTOBOY == $login->getTipo()){
                $mongo->update(["_id" =>$idMongo], ['$push' => ["posicoes_motoboy" => $loginPosicoes->toArray()]]);
            }elseif ($login->getTipo()  == LoginEntity::$USUARIO){
                $mongo->update(["_id" =>$idMongo], ['$push' => ["posicoes_usuario" => $loginPosicoes->toArray()]]);
            }
        }
    }
    private function getEnderecoPorLatLong(LoginPosicoes $loginPosicoes)
    {

        $client = new Client();

        try{
            $responseApi = $client->request('GET' , "https://reverse.geocoder.cit.api.here.com/6.2/reversegeocode.json", [
                'query' => [
                    "app_id" => "7ArpnlTqh018YozMUeXE",
                    "app_code" => "VpAlGtNYGgBUEWvrDb6wpg",
                    "mode" => "retrieveAddresses",
                    "maxresults" => 1,
                    "prox" => $loginPosicoes->getLatLong()
                ]
            ]);
            $requestInfo = \GuzzleHttp\json_decode($responseApi->getBody()->getContents());
            $endereco = $requestInfo->Response->View[0]->Result[0]->Location->Address->Label;

        }catch(RequestException $ex){
            $endereco= "";
        }

        return $endereco;
    }

}