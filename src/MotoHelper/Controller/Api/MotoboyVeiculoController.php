<?php

namespace MotoHelper\Controller\Api;

use Doctrine\ORM\EntityManager;
use MotoHelper\Controller\App\Login;
use MotoHelper\Entity\Cor;
use MotoHelper\Entity\Empresa;
use MotoHelper\Entity\LoginMotoboy;
use MotoHelper\Entity\LoginVeiculo;
use MotoHelper\Entity\VeiculoMotoboy;
use MotoHelper\Helper\PasswordHash;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;
use MotoHelper\Entity\Veiculo;

class MotoboyVeiculoController
{

    public static function addRoutes($routing)
    {
        $routing->post('/veiculo/{id}/motoboy/{id_motoboy}', array(new self(), 'cadastrarVeiculoMotoboy'))->bind('post_cadastrar_motoboy_veiculo');
        $routing->delete('/veiculo/motoboy/{id}', array(new self(), 'deletarVeiculoMotoboy'))->bind('delete_veiculo_motoboy');
    }

    public function cadastrarVeiculoMotoboy(Application $app, $id, $id_motoboy)
    {
        $response = new JsonResponse();

        try {

            $motoboyVeiculo = $this->definirveiculoFromRequest($app, $id, $id_motoboy);

            $entityManager = $app['orm.em'];
            $entityManager->persist($motoboyVeiculo);
            $entityManager->flush();

            $response->setData(["veiculo" => $motoboyVeiculo->toArray(), "msg" => "Informações do veiculo atualizada com sucesso"]);
            $response->setStatusCode( Response::HTTP_OK);

        } catch (NestedValidationException $exception) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setData(["erros" => $this->getErrors($exception, $app)]);
        }

        return $response;
    }

    public function deletarVeiculoMotoboy(Application $app, $id)
    {
        $response = new JsonResponse();

        try {
            $veiculoRepository = $app['orm.em']->getRepository(VeiculoMotoboy::class);
            $veiculo = $veiculoRepository->find($id);
            $veiculoExiste = !is_null($veiculo);
            if($veiculoExiste){
                $entityManager = $app['orm.em'];
                $entityManager->remove($veiculo);
                $entityManager->flush();

                $response->setData(["msg" => "Veiculo deletado com sucesso"]);
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

    private function definirveiculoFromRequest(Application $app, $idVeiculo, $idMotoboy )
    {
        $motoboy = $app['orm.em']->getReference(LoginMotoboy::class, $idMotoboy);
        $veiculo = $app['orm.em']->getReference(Veiculo::class, $idVeiculo);

        $motoboyVeiculo = new VeiculoMotoboy();
        $motoboyVeiculo->setMotoboy($motoboy);
        $motoboyVeiculo->setVeiculo($veiculo);

        return $motoboyVeiculo;
    }
}
