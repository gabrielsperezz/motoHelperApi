<?php

namespace MotoHelper\Controller\App;

use Doctrine\ORM\EntityManager;
use MotoHelper\Entity\LoginCliente;
use MotoHelper\Entity\LoginMotoboy;
use Silex\Application;

class Localizacao
{

    public static function addRoutes( $routing )
    {
        $routing->get('/localizacoes' , array(new self() , 'localizacoes'))->bind('localizacoes');
    }

    public function localizacoes( Application $app )
    {
        $params = [ "usuarios" => array_merge($this->getMotoboys($app['orm.em']), $this->getUsuarios($app['orm.em']))];
        return $app['twig']->render('app/localizacao/localizacao.html.twig' , $params);
    }

    private function getMotoboys( EntityManager $em)
    {
        $repository = $em->getRepository(LoginMotoboy::class);
        return $repository->findAll();
    }

    private function getUsuarios( EntityManager $em)
    {
        $repository = $em->getRepository(LoginCliente::class);
        return $repository->findAll();
    }

}
