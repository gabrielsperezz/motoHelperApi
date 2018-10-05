<?php

namespace MotoHelper\Controller\App;

use MotoHelper\Entity\Empresa;
use Silex\Application;

class EmpresaController
{

    public static function addRoutes($routing)
    {
        $routing->get('/empresa/form/{id}', array(new self(), 'getFormEmpresa'))
            ->assert('id', '\d+')
            ->value('id', 0)
            ->bind('form_empresa');

        $routing->get('/empresa/form/busca', array(new self(), 'getFormEmpresaBusca'))
            ->bind('form_empresa_busca');
    }

    public function getFormEmpresa(Application $app, $id)
    {
        $idValido = ($id > 0);
        $empresa = ($idValido) ? $this->getEmpresa($app, $id) : null;
        $empresasExistem = !is_null($empresa);
        if ($idValido && $empresasExistem) {
            $params = [
                'empresa' => $empresa->toArray()
            ];
            $response = $app['twig']->render('empresa/empresa_form.html.twig', $params);
        } elseif ($idValido && !$empresasExistem) {
            $response = $app['twig']->render('errors/404.html.twig');
        } else {
            $params = [
                'empresa' => null
            ];
            $response = $app['twig']->render('empresa/empresa_form.html.twig', $params);
        }

        return $response;
    }

    public function getEmpresa(Application $app, $id)
    {
        $repository = $app['orm.em']->getRepository(Empresa::class);
        return $repository->findOneBy(["id" => $id]);
    }

    public function getFormEmpresaBusca(Application $app)
    {
        $response = $app['twig']->render('empresa/empresa_form_busca.html.twig', ["empresas" => $this->getAllEmpresas($app)]);
        return $response;
    }


    public function getAllEmpresas(Application $app)
    {
        $repository = $app['orm.em']->getRepository(Empresa::class);
        $empresas = [];
        foreach ($repository->findAll() as $empresa){
            array_push($empresas,$empresa->toArray());
        }

        return $empresas;
    }

}
