<?php

namespace MotoHelper\Controller\ApiMobile;

use GuzzleHttp\Client;
use MotoHelper\Entity\Login as LoginEntity;
use MotoHelper\Entity\LoginCliente;
use MotoHelper\Entity\LoginMotoboy;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class Corrida extends ApiMobileAbstract
{

    public static function addRoutes($routing)
    {
        $routing->post('/corrida/disponivel/{id_login}', array(new self(), 'ficarDisponivelCorrida'))->bind('ficar_disponivel_corrida');
        $routing->post('/corrida/procurar/{id_login}', array(new self(), 'procurarCorrida'))->bind('procurar_corrida_login');
        $routing->put('/corrida/{id_corrida}/aceitar', array(new self(), 'aceitarCorrida'))->bind('aceitar_corrida_login');
        $routing->put('/corrida/{id_corrida}/finalizar', array(new self(), 'finalizarCorrida'))->bind('finalizar_corrida_login');
        $routing->put('/corrida/{id_corrida}/cancelar', array(new self(), 'cancelarCorrida'))->bind('cancelar_corrida_login');

        $routing->get('/corrida/atual', array(new self(), 'buscarCorridaAtual'))->bind('buscar_corrida_atual');
        $routing->get('/corridas/historico', array(new self(), 'buscarHistoricoDeCorridas'))->bind('buscar_historico_corridas');

        $routing->get('/rotas/{login_principal}/{login_secundario}', array(new self(), 'buscarRotasPorLogin'))->bind('buscarRotasPorLogin');

    }

    public function procurarCorrida(Application $app, $id_login)
    {

        $response = new JsonResponse();

        try {
            $login = $this->getUserInfo($id_login, $app);

            if (!$login->isProcurandoCorrida()) {

                $novaCorrida = $this->getNovaCorrida($login, $app);

                $this->getMongoDb($app)->insert($novaCorrida);
                $login->setProcurandoCorrida(true);
                $em = $this->getEm($app);

                $this->publishMessage("corridas/busca", $novaCorrida);

                $em->persist($login);
                $em->flush();
            }else{
                $mongo = $this->getMongoDb($app);

                $novaCorrida = $mongo->findOne(["status" => 0, "id_usuario" => (int) $id_login]);
                $this->publishMessage("corridas/busca", $novaCorrida);
            }
            $response->setData($this->getCorridaMap($novaCorrida));
            $response->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            $app['logger']->critical($ex->getMessage());
        }

        return $response;
    }

    public function buscarRotasPorLogin(Application $app, $login_principal, $login_secundario)
    {

        $response = new JsonResponse();

        try {
            $login = $this->getUserInfo($login_principal, $app);
            $loginSec = $this->getUserInfo($login_secundario, $app);
            $rota = [];
            if ($login && $loginSec) {

              $rota = $this->buscarRotasPorLoginEntity($login, $loginSec);
            }

            $response->setData($rota);
            $response->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            $app['logger']->critical($ex->getMessage());
        }

        return $response;
    }

    public function ficarDisponivelCorrida(Application $app, $id_login)
    {

        $response = new JsonResponse();

        try {
            $login = $this->getUserInfo($id_login, $app);

            $login->setDisponivelParaCorrida(true);

            $em = $this->getEm($app);

            $em->persist($login);
            $em->flush();

            $response->setData($this->buscarCorridasEmAberto($app));
            $response->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            $app['logger']->critical($ex->getMessage());
        }

        return $response;
    }

    public function aceitarCorrida(Application $app, $id_corrida)
    {

        $response = new JsonResponse();

        try {
            $motoboy = $app['user'];
            $corrida = $this->getCorridaPorId($id_corrida, $app);
            $ususario = $this->getUserInfo($corrida['id_usuario'], $app);
            $ususario->setProcurandoCorrida(false);
            $ususario->setIdCorridaAtual($id_corrida);
            $motoboy->setDisponivelParaCorrida(false);
            $motoboy->setIdCorridaAtual($id_corrida);

            $mongo = $this->getMongoDb($app);
            $mongo->update(["_id"=> $this->getMongoIdPorId($id_corrida)], ['$set' => $this->atualizarCorridaAtender($motoboy)]);

            $em = $this->getEm($app);
            $em->persist($motoboy);
            $em->persist($ususario);
            $em->flush();

            $this->publishMessage("corrida/$id_corrida/aceita", $this->getCorridaMap($corrida));
            $response->setData($this->getCorridaMap($corrida));
            $response->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            $app['logger']->critical($ex->getMessage());
        }

        return $response;
    }

    public function finalizarCorrida(Application $app,  Request $request, $id_corrida)
    {

        $response = new JsonResponse();

        try {
            $motoboy = $app['user'];
            $corrida = $this->getCorridaPorId($id_corrida, $app);
            $ususario = $this->getUserInfo($corrida['id_usuario'], $app);
            $ususario->setIdCorridaAtual(null);
            $motoboy->setIdCorridaAtual(null);

            $mongo = $this->getMongoDb($app);
            $mongo->update(["_id"=> $this->getMongoIdPorId($id_corrida)], ['$set' => $this->finalizarCorridaByRequest($request->request->all())]);

            $this->publishMessage("corrida/$id_corrida/finalizar", $this->getCorridaMap($corrida));

            $em = $this->getEm($app);
            $em->persist($motoboy);
            $em->persist($ususario);
            $em->flush();

            $response->setData($this->getCorridaMap($corrida));
            $response->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            $app['logger']->critical($ex->getMessage());
        }

        return $response;
    }

    public function cancelarCorrida(Application $app,  Request $request, $id_corrida)
    {

        $response = new JsonResponse();

        try {
            $motoboy = $app['user'];
            $corrida = $this->getCorridaPorId($id_corrida, $app);
            $ususario = $this->getUserInfo($corrida['id_usuario'], $app);

            $ususario->setIdCorridaAtual(null);
            $motoboy->setIdCorridaAtual(null);

            $mongo = $this->getMongoDb($app);
            $mongo->update(["_id"=> $this->getMongoIdPorId($id_corrida)], ['$set' => $this->cancelarCorridaByRequest($request->request->all())]);

            $this->publishMessage("corrida/$id_corrida/cancelar", $this->getCorridaMap($corrida));
            $em = $this->getEm($app);
            $em->persist($motoboy);
            $em->persist($ususario);
            $em->flush();

            $response->setData($this->getCorridaMap($corrida));
            $response->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            $app['logger']->critical($ex->getMessage());
        }

        return $response;
    }

    public function buscarCorridaAtual(Application $app)
    {

        $response = new JsonResponse();

        try {
            $user = $app['user'];
            $corrida = $this->getCorridaPorId($user->getIdCorridaAtual(), $app);
            $response->setData($this->getCorridaMap($corrida));
            $response->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            $app['logger']->critical($ex->getMessage());
        }

        return $response;
    }

    public function buscarHistoricoDeCorridas(Application $app)
    {

        $response = new JsonResponse();

        try {
            $user = $app['user'];
            $corridas = $this->buscarCorridasPorUsuario($app, $user->getId());
            $response->setData($corridas);
            $response->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            $app['logger']->critical($ex->getMessage());
        }

        return $response;
    }

    private function buscarCorridasEmAberto($app)
    {
        $mongo = $this->getMongoDb($app);
        $corridas = [];
        foreach ($mongo->find(["status" => 0]) as $corrida){
               array_push($corridas , $this->getCorridaMap($corrida));
        }
        return $corridas;
    }

    private function buscarCorridasPorUsuario($app, $idUser)
    {
        $mongo = $this->getMongoDb($app);
        $corridas = [];
        foreach ($mongo->find(['$or' => [["id_usuario" => $idUser], ["id_motoboy_atendendo" => $idUser]]]) as $corrida){
            array_push($corridas , $this->getCorridaMap($corrida));
        }
        return $corridas;
    }

    private function getNovaCorrida(LoginCliente $loginCliente, $app)
    {

        $dataAgora = new \DateTime("UTC");

        return [
            "id_usuario" => $loginCliente->getId(),
            "id_motoboy_atendendo" => null,
            "id_empresa" => null,
            "status" => 0,
            "data_solicitacao" => new \MongoDate($dataAgora->getTimestamp()),
            "data_hora_fim" => null,
            "data_hora_inicio" => null,
            "usuario" => $loginCliente->toArray(),
            "motoboy" => null,
            "posicoes_motoboy" => [],
            "posicoes_usuario" => []
        ];
    }

    private function atualizarCorridaAtender(LoginMotoboy $loginMotoboy)
    {

        $dataAgora = new \DateTime("UTC");

        return [
            "id_motoboy_atendendo" => $loginMotoboy->getId(),
            "id_empresa" => $loginMotoboy->getEmpresa()->getId(),
            "nome_empresa" => $loginMotoboy->getEmpresa()->getNome(),
            "status" => 1,
            "data_hora_inicio" => new \MongoDate($dataAgora->getTimestamp()),
            "motoboy" => $loginMotoboy->toArray(),
        ];
    }

    private function finalizarCorridaByRequest($data)
    {
        $dataAgora = new \DateTime("UTC");
        return [
            "status" => 3,
            "data_hora_fim" => new \MongoDate($dataAgora->getTimestamp()),
            "comentario" => isset($data['comentario'])? $data['comentario'] : "" ,
            "nota" => isset($data['nota'])? $data['nota']:  ""
        ];
    }

    private function cancelarCorridaByRequest($data)
    {
        return [
            "status" => 2
        ];
    }
    
    private function buscarRotasPorLoginEntity(LoginEntity $loginPrincipal, LoginEntity $loginSecundario)
    {
        try {
            $client = new Client();

            $responseApi = $client->request('GET' , "https://route.api.here.com/routing/7.2/calculateroute.json", [
                'query' => [
                    "app_id" => "7ArpnlTqh018YozMUeXE",
                    "app_code" => "VpAlGtNYGgBUEWvrDb6wpg",
                    "waypoint0" => "geo!".$loginPrincipal->getPosicoes()->getLatLong(),
                    "waypoint1" => "geo!".$loginSecundario->getPosicoes()->getLatLong(),
                    "mode"  =>  "fastest;car"
                ]
            ]);

            $response = \GuzzleHttp\json_decode($responseApi->getBody()->getContents());
            $rotas = $this->getMapRotas($response, $loginSecundario);
        } catch (\Exception $ex) {
            $rotas = null;
        }

        return $rotas;
    }

    private function getMapRotas($rotas, LoginEntity $loginSecundario){
        $rotasInfo = $rotas->response->route[0];
        $sumario = $rotasInfo->summary;
        $pontos = $rotasInfo->waypoint;
        $rotas = $this->getLocationMap($rotasInfo->leg[0]->maneuver);
        $rotasClean = $this->getLocationMapClean($rotasInfo->leg[0]->maneuver);

        return[
            "inicial" => [
                "endereco" => $pontos[0]->label,
                "posicao" => $pontos[0]->originalPosition,
            ],
            "final" =>[
                "nome" => $loginSecundario->getDescricao(),
                "endereco" => $pontos[1]->label,
                "posicao" => $pontos[1]->originalPosition,
            ],
            "tempo_estimado" => $sumario->trafficTime,
            "distancia" => $sumario->distance,
            "rotas_clean" => $rotasClean,
            "rotas" => $rotas
        ];
    }

    private function getLocationMap($locations)
    {
        return array_map(function ($location) {
            return [
                "latitude" => $location->position->latitude,
                "longitude" => $location->position->longitude
            ];
        },$locations);

    }

    private function getLocationMapClean($locations)
    {
        return array_map(function ($location) {
            return [
                $location->position->latitude,
                $location->position->longitude
            ];
        },$locations);

    }


}