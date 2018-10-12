<?php

namespace MotoHelper\Controller\App;

use MotoHelper\Entity\LoginMotoboy;
use MotoHelper\Entity\Veiculo;
use Silex\Application;

class MotoboyController
{

    public static function addRoutes($routing)
    {
        $routing->get('/motoboy/form/{id}', array(new self(), 'getFormMotoboy'))
            ->assert('id', '\d+')
            ->value('id', 0)
            ->bind('form_motoboy');

        $routing->get('/motoboy/form/busca', array(new self(), 'getFormMotoboyBusca'))
            ->bind('form_motoboy_busca');
    }

    public function getFormMotoboy(Application $app, $id)
    {
        $idValido = ($id > 0);
        $motoboy = ($idValido) ? $this->getMotoboy($app, $id) : null;
        $motoboysExistem = !is_null($motoboy);
        if ($idValido && $motoboysExistem) {
            $params = [
                'motoboy' => $motoboy->toArray(),
                'veiculos' => $this->getAllVeiculos($app)
            ];
            $response = $app['twig']->render('app/motoboy/motoboy_form.html.twig', $params);
        } elseif ($idValido && !$motoboysExistem) {
            $response = $app['twig']->render('app/errors/404.html.twig');
        } else {
            $params = [
                'motoboy' => null,
                'veiculos' => $this->getAllVeiculos($app)
            ];
            $response = $app['twig']->render('app/motoboy/motoboy_form.html.twig', $params);
        }

        return $response;
    }

    public function getMotoboy(Application $app, $id)
    {
        $repository = $app['orm.em']->getRepository(LoginMotoboy::class);
        return $repository->findOneBy(["id" => $id]);
    }

    public function getFormMotoboyBusca(Application $app)
    {
        $response = $app['twig']->render('app/motoboy/motoboy_form_busca.html.twig', ["motoboys" => $this->getAllMotoboys($app)]);
        return $response;
    }


    public function getAllMotoboys(Application $app)
    {

        $repository = $app['orm.em']->getRepository(LoginMotoboy::class);

        $motoboys = [];
        foreach ($repository->findBy(['empresa' => $app['token']->getLogin()->getEmpresa()]) as $motoboy){
            array_push($motoboys,$motoboy->toArray());
        }

        return $motoboys;
    }


    public function getAllVeiculos(Application $app)
    {
        $repository = $app['orm.em']->getRepository(Veiculo::class);
        $veiculos = [];

        foreach ($repository->findBy(['empresa' => $app['token']->getLogin()->getEmpresa()]) as $veiculo){
            array_push($veiculos,$veiculo->toArray());
        }

        return $veiculos;
    }

}
