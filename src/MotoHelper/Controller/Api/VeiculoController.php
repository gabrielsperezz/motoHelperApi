<?php

namespace MotoHelper\Controller\Api;

use Doctrine\ORM\EntityManager;
use MotoHelper\Entity\Cor;
use MotoHelper\Entity\Empresa;
use MotoHelper\Entity\LoginVeiculo;
use MotoHelper\Helper\PasswordHash;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;
use MotoHelper\Entity\Veiculo;

class VeiculoController
{

    public static function addRoutes($routing)
    {
        $routing->post('/veiculo', array(new self(), 'cadastrarVeiculo'))->bind('post_cadastrar_veiculo');
        $routing->put('/veiculo/{id}', array(new self(), 'atualizarVeiculo'))->bind('put_atualizar_veiculo');
        $routing->delete('/veiculo/{id}', array(new self(), 'deletarVeiculo'))->bind('delete_deletar_veiculo');
    }

    public function cadastrarVeiculo(Application $app)
    {
        $response = new JsonResponse();
        $request = $app['request'];

        try {
            $this->validateData($request->request->all());

            $veiculo = new Veiculo();

            $this->definirveiculoFromRequest($veiculo, $request, $app);

            $entityManager = $app['orm.em'];
            $entityManager->persist($veiculo);
            $entityManager->flush();

            $response->setData(["veiculo" => $veiculo->toArray(), "msg" => "Informações do veiculo atualizada com sucesso"]);
            $response->setStatusCode( Response::HTTP_OK);

        } catch (NestedValidationException $exception) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setData(["erros" => $this->getErrors($exception, $app)]);
        }

        return $response;
    }

    public function atualizarVeiculo(Application $app, $id)
    {
        $response = new JsonResponse();
        $request = $app['request'];

        try {
            $veiculoRepository = $app['orm.em']->getRepository(Veiculo::class);
            $veiculo = $veiculoRepository->find($id);
            $veiculoExiste = !is_null($veiculo);
            if($veiculoExiste){
                $this->validateData($request->request->all());

                $this->definirveiculoFromRequest($veiculo, $request, $app);

                $entityManager = $app['orm.em'];
                $entityManager->persist($veiculo);
                $entityManager->flush();

                $response->setData(["veiculo" => $veiculo->toArray(), "msg" => "Informações do veiculo atualizada com sucesso"]);
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

    public function deletarVeiculo(Application $app, $id)
    {
        $response = new JsonResponse();

        try {
            $veiculoRepository = $app['orm.em']->getRepository(Veiculo::class);
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

    private function definirveiculoFromRequest(Veiculo $veiculo, Request $request, Application $app )
    {
        $empresa = $app['token']->getLogin()->getEmpresa();
        $placa = $request->request->filter('placa', null);
        $descricao = $request->request->filter('descricao', null);
        $cor = $request->request->filter('cor', null);
        $modelo = $request->request->filter('modelo', null);
        $fabricante = $request->request->filter('fabricante', null);

        $veiculo->setPlaca($placa);
        $veiculo->setDescricao($descricao);
        $veiculo->setCor($app['orm.em']->getReference(Cor::class, $cor));
        $veiculo->setFabricante($fabricante);
        $veiculo->setModelo($modelo);
        $veiculo->setEmpresa($empresa);

        return $veiculo;
    }

    private function validateData($data)
    {
        $validation = v::arrayType()
            ->key('placa', v::regex('/[a-zA-Z]{3}\-\d{4}/')->setName("placa"))
            ->key('descricao', v::stringType()->notEmpty()->setName("descricao"))
            ->key('cor', v::numeric()->positive()->setName("cor"))
            ->key('fabricante', v::stringType()->notEmpty()->setName("fabricante"))
            ->key('modelo', v::stringType()->notEmpty()->setName("modelo"));

        $validation->assert($data);
    }

    private function getErrors(NestedValidationException $exception, Application $app)
    {
        $errors = array_filter(
            $exception->findMessages([
                'placa' => "Placa inválido",
                'cor' => "Cor inválido",
                'fabricante' => "Fabricante inválido",
                'modelo' => "Modelo inválido",
                'descricao' => "Descrição inválida"
            ]),
            function ($value) {
                return (strlen($value) > 0);
            });
        return $errors;
    }

}
