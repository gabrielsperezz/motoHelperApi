<?php

namespace MotoHelper\Controller\App;

use MotoHelper\Entity\Cor;
use MotoHelper\Entity\Veiculo;
use Silex\Application;

class VeiculoController
{

    public static function addRoutes($routing)
    {
        $routing->get('/veiculo/form/{id}', array(new self(), 'getFormVeiculo'))
            ->assert('id', '\d+')
            ->value('id', 0)
            ->bind('form_veiculo');

        $routing->get('/veiculo/form/busca', array(new self(), 'getFormVeiculoBusca'))
            ->bind('form_veiculo_busca');
    }

    public function getFormVeiculo(Application $app, $id)
    {
        $idValido = ($id > 0);
        $veiculo = ($idValido) ? $this->getVeiculo($app, $id) : null;
        $veiculosExistem = !is_null($veiculo);
        if ($idValido && $veiculosExistem) {
            $params = [
                'cores' => $this->getAllCores($app),
                'veiculo' => $veiculo->toArray()
            ];
            $response = $app['twig']->render('app/veiculo/veiculo_form.html.twig', $params);
        } elseif ($idValido && !$veiculosExistem) {
            $response = $app['twig']->render('app/errors/404.html.twig');
        } else {
            $params = [
                'cores' => $this->getAllCores($app),
                'veiculo' => null
            ];
            $response = $app['twig']->render('app/veiculo/veiculo_form.html.twig', $params);
        }

        return $response;
    }

    public function getVeiculo(Application $app, $id)
    {
        $repository = $app['orm.em']->getRepository(Veiculo::class);
        return $repository->findOneBy(["id" => $id]);
    }

    public function getFormVeiculoBusca(Application $app)
    {
        $response = $app['twig']->render('app/veiculo/veiculo_form_busca.html.twig', ["veiculos" => $this->getAllVeiculos($app)]);
        return $response;
    }

    public function getAllCores(Application $app)
    {
        $repository = $app['orm.em']->getRepository(Cor::class);
        $cores = [];
        foreach ($repository->findAll() as $cor){
            array_push($cores,$cor->toArray());
        }

        return $cores;
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
