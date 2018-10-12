<?php

namespace MotoHelper\Controller\Admin;

use Doctrine\ORM\EntityManager;
use MotoHelper\Entity\Veiculo;
use Silex\Application;
use MotoHelper\Entity\VeiculoVerificacao;

class VeiculoVerificacaoController
{

    public static function addRoutes($routing)
    {
        $routing->get('/veiculo/verificao' , array(new self() , 'getVeiculosVerificacao'))
            ->bind('form_veiculos_verificar');

        $routing->get('/veiculo/{id_veiculo}/verificao' , array(new self() , 'verificarVeiculo'))
            ->bind('form_veiculos_verificar_id');
    }

    public function verificarVeiculo(Application $app)
    {
        $veiculo = $this->getVeiculoPorId($app, $app['request']->attributes->get('id_veiculo'));

        if(!is_null($veiculo)){
            $response = $app['twig']->render('admin/veiculo_autorizacao/veiculo_autorizacao_form.html.twig', ["veiculo" => $veiculo->toArray()]);
        }
        return $response;
    }

    public function getVeiculosVerificacao( Application $app )
    {
        return $app['twig']->render('admin/veiculo_autorizacao/veiculo_autorizacao_form_busca.html.twig' , ["veiculos" => $this->getVeiculosAVerificar($app['orm.em'])]);
    }

    public function getVeiculosAVerificar( EntityManager $em )
    {
        $repository = $em->getRepository(VeiculoVerificacao::class);
        $veiculos = [];
        foreach($repository->findBy(["verificado" => false] , ["id" => "DESC"]) as $veiculoVerificacao) {
            array_push($veiculos , $veiculoVerificacao->getVeiculo()->toArray());
        }
        return $veiculos;
    }


    public function getVeiculoPorId(Application $app, $idVeiculo)
    {
        return $app['orm.em']->getReference(Veiculo::class, $idVeiculo);
    }


}
