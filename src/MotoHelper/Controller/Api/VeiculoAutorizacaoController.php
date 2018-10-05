<?php

namespace MotoHelper\Controller\Api;

use Silex\Application;
use MotoHelper\Entity\Veiculo;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Respect\Validation\Exceptions\NestedValidationException;

class VeiculoAutorizacaoController
{

    public static function addRoutes($routing)
    {
        $routing->patch('/veiculo/{id}/autorizar', array(new self(), 'atualizarVeiculoAutorizado'))->bind('patch_autorizar_veiculo');
    }

    public function atualizarVeiculoAutorizado(Application $app, $id)
    {
        $response = new JsonResponse();

        try {
            $entityManager = $app['orm.em'];
            $veiculo = $this->getVeiculoPorId($entityManager, $id);
            $veiculoExiste = !is_null($veiculo);
            if($veiculoExiste){

                $veiculo->autorizar();

                $entityManager->persist($veiculo);
                $entityManager->flush();

                $response->setData(["veiculo" => $veiculo->toArray(), "msg" => "O veiculo foi autorizado com sucesso"]);
                $response->setStatusCode(Response::HTTP_OK);
            }else{
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                $response->setData(["erros" => ["veiculo" => "Veiculo não encontrado"]]);
            }

        } catch (NestedValidationException $exception) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setData(["erros" => $this->getErrors($exception, $app)]);
        }

        return $response;
    }

    private function getVeiculoPorId(EntityManager $em, $idVeiculo)
    {
        $repository = $em->getRepository(Veiculo::class);
        return $repository->find($idVeiculo);
    }

}
