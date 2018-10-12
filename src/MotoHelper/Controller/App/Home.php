<?php

namespace MotoHelper\Controller\App;

use Doctrine\ORM\EntityManager;
use MotoHelper\Entity\Empresa;
use MotoHelper\Entity\Veiculo;
use MotoHelper\Entity\LoginCliente;
use MotoHelper\Entity\LoginEmpresa;
use MotoHelper\Entity\VeiculoVerificacao;
use Silex\Application;
use GuzzleHttp\Client;

class Home
{

    public static function addRoutes( $routing )
    {
        $routing->get('/' , array(new self() , 'defaultPage'))->bind('defaultPage_app');
        $routing->get('/home' , array(new self() , 'home'))->bind('home_app');
    }

    public function defaultPage( Application $app )
    {
        return $app->redirect("/home");
    }

    public function home( Application $app )
    {
        $empresa = $app['token']->getLogin()->getEmpresa()->getId();

        $params = [
            "informacoes" => [
                "total_veiculos" => $this->getTotalVeiculos($app['orm.em'], $empresa) ,
                "total_moto_boy" => $this->getTotalMotoBoy($app['orm.em'], $empresa)
            ]
        ];
        return $app['twig']->render('app/home/home.html.twig' , $params);
    }

    public function getTotalVeiculos( EntityManager $em, $idEmpresa )
    {
        $repository = $em->getRepository(Veiculo::class);
        return count($repository->findBy(["empresa" => $idEmpresa]));
    }

    public function getTotalMotoBoy( EntityManager $em, $idEmpresa )
    {
        $repository = $em->getRepository(LoginEmpresa::class);
        return count($repository->findBy(["empresa" => $idEmpresa]));
    }

}
