<?php

namespace MotoHelper\Controller\ApiMobile;

use MongoId;
use MongoCollection;
use MongoDate;
use Bluerhinos\phpMQTT;
use Monolog\Handler\Mongo;

abstract class ApiMobileAbstract
{

    private $mqttClient;
    private $collection;

    protected function getUserInfo($idUSer, $app)
    {
        return $this->getEm($app)->getReference(\MotoHelper\Entity\Login::class, $idUSer);
    }

    protected function getEm($app)
    {
        return $app['orm.em'];
    }

    protected function getMongoDb($app)
    {

        return $this->collection = new MongoCollection($app['mongo']['default'], 'corridas');
    }

    protected function getMqttClient()
    {
        if(!is_null($this->mqttClient)){
            return $this->mqttClient;
        }

        $this->mqttClient = new phpMQTT(MQTT_LINK, MQTT_PORTA, MQTT_CLIENT_ID);
        return $this->mqttClient->connect(true, NULL, MQTT_USER , MQTT_PASSWORD);
    }

    public function publishMessage($topic, $mensagem)
    {
        if($this->getMqttClient()){
            $this->mqttClient->publish($topic , json_encode($mensagem), 1);

            $this->mqttClient->close();
            $this->mqttClient = null;
        }
    }

    public function getCorridaMap($corrida)
    {
        $corrida['id'] = $this->getIdCorridaPorObjectIdMongo($corrida["_id"]);
        $corrida['inicio'] = $this->getDataFormat($corrida['data_hora_inicio']);
        $corrida['fim'] = $this->getDataFormat($corrida['data_hora_fim']);
        $corrida['solicitado'] = $this->getDataFormat($corrida['data_solicitacao']);
        unset($corrida['_id']);
        return $corrida;
    }

    private function getDataFormat(MongoDate $data = null)
    {
        $dataFormat = null;
        if(!is_null($data)){

            $dateTime = $data->toDateTime();
            $dateTime->setTimezone(new \DateTimeZone("America/Sao_Paulo"));
            $dataFormat = $dateTime->format("d/m/Y H-i");
        }

        return $dataFormat;
    }

    public function getCorridaPorId($idOcorrencia, $app)
    {
        $mongo = $this->getMongoDb($app);
        $idMongo = new MongoId($idOcorrencia);
        return $mongo->findOne(["_id" =>$idMongo]);
    }

    public function getMongoIdPorId($id)
    {
        return new MongoId($id);
    }

    public function getIdCorridaPorObjectIdMongo($idOcorrenciaMongo)
    {
        $id_ocorrencia = 0;
        foreach ($idOcorrenciaMongo as $objectId){
            $id_ocorrencia = $objectId;
        }
        return $id_ocorrencia;
    }




}