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
        $routing->get('/' , array(new self() , 'defaultPage'))->bind('defaultPage');
        $routing->get('/home' , array(new self() , 'home'))->bind('home');
    }

    public function defaultPage( Application $app )
    {
        return $app->redirect("/home");
    }

    public function home( Application $app )
    {
        $params = [
            "veiculos" => $this->getVeiculosAVerificar($app['orm.em']) ,
            "informacoes" => [
                "total_veiculos" => $this->getTotalVeiculos($app['orm.em']) ,
                "total_empresas" => $this->getTotalEmpresas($app['orm.em']) ,
                "total_moto_boy" => $this->getTotalMotoBoy($app['orm.em']) ,
                "total_usuarios" => $this->getTotalUsuarios($app['orm.em']) ,
            ]
        ];
        return $app['twig']->render('home/home.html.twig' , $params);
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

    public function getTotalVeiculos( EntityManager $em )
    {
        $livrosRepository = $em->getRepository(Veiculo::class);
        return count($livrosRepository->findAll());
    }

    public function getTotalEmpresas( EntityManager $em )
    {
        $livrosRepository = $em->getRepository(Empresa::class);
        return count($livrosRepository->findAll());
    }

    public function getTotalMotoBoy( EntityManager $em )
    {
        $livrosRepository = $em->getRepository(LoginEmpresa::class);
        return count($livrosRepository->findAll());
    }

    public function getTotalUsuarios( EntityManager $em )
    {
        $livrosRepository = $em->getRepository(LoginCliente::class);
        return count($livrosRepository->findAll());
    }
}
