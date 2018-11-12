<?php

namespace MotoHelper\Controller\Api;

use MotoHelper\Helper\PasswordHash;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;
use MotoHelper\Entity\LoginMotoboy;

class MotoboyController
{

    public static function addRoutes($routing)
    {
        $routing->post('/motoboy', array(new self(), 'cadastrarMotoboy'))->bind('post_cadastrar_motoboy');
        $routing->put('/motoboy/{id}', array(new self(), 'atualizarMotoboy'))->bind('put_atualizar_motoboy');
        $routing->delete('/motoboy/{id}', array(new self(), 'deletarMotoboy'))->bind('delete_deletar_motoboy');
    }

    public function cadastrarMotoboy(Application $app)
    {
        $response = new JsonResponse();
        $request = $app['request'];

        try {
            $this->validateData($request->request->all(), true);

            $motoboy = new LoginMotoboy();

            $this->definirmotoboyFromRequest($motoboy, $request, $app, true);

            $entityManager = $app['orm.em'];
            $entityManager->persist($motoboy);
            $entityManager->flush();

            $response->setData(["motoboy" => $motoboy->toArray(), "msg" => "Informações do motoboy atualizada com sucesso"]);
            $response->setStatusCode( Response::HTTP_OK);

        } catch (NestedValidationException $exception) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setData(["erros" => $this->getErrors($exception, $app)]);
        }

        return $response;
    }

    public function atualizarMotoboy(Application $app, $id)
    {
        $response = new JsonResponse();
        $request = $app['request'];

        try {
            $motoboyRepository = $app['orm.em']->getRepository(LoginMotoboy::class);
            $motoboy = $motoboyRepository->find($id);
            $motoboyExiste = !is_null($motoboy);
            if($motoboyExiste){
                $atualizarSenha = $request->request->filter('atualizar_senha', null);

                $this->validateData($request->request->all(), $atualizarSenha);


                $this->definirmotoboyFromRequest($motoboy, $request, $app, $atualizarSenha);

                $entityManager = $app['orm.em'];
                $entityManager->persist($motoboy);
                $entityManager->flush();

                $response->setData(["motoboy" => $motoboy->toArray(), "msg" => "Informações do motoboy atualizada com sucesso"]);
                $response->setStatusCode(Response::HTTP_OK);
            }else{
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                $response->setData(["erros" => ["motoboy" => "Motoboy não encontrado"]]);
            }

        } catch (NestedValidationException $exception) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setData(["erros" => $this->getErrors($exception, $app)]);
        }

        return $response;
    }

    public function deletarMotoboy(Application $app, $id)
    {
        $response = new JsonResponse();

        try {
            $motoboyRepository = $app['orm.em']->getRepository(LoginMotoboy::class);
            $motoboy = $motoboyRepository->find($id);
            $motoboyExiste = !is_null($motoboy);
            if($motoboyExiste){
                $entityManager = $app['orm.em'];
                $entityManager->remove($motoboy);
                $entityManager->flush();

                $response->setData(["msg" => "Motoboy deletado com sucesso"]);
                $response->setStatusCode(Response::HTTP_OK);
            }else{
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                $response->setData(["erros" => ["motoboy" => "Motoboy não encontrado"]]);
            }

        } catch (NestedValidationException $exception) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setData(["erros" => $this->getErrors($exception, $app)]);
        }

        return $response;
    }

    private function definirmotoboyFromRequest(LoginMotoboy $motoboy, Request $request, Application $app, $atualizarSenha )
    {
        $empresa = $app['token']->getLogin()->getEmpresa();
        $nome = $request->request->filter('nome', null);
        $login = $request->request->filter('login', null);
        $email = $request->request->filter('email', null);
        $senha = $request->request->filter('senha', null);

        if($atualizarSenha){
            $motoboy->setSenha(PasswordHash::gerarHashSenha($senha));
        }

        $motoboy->setDescricao($nome);
        $motoboy->setLogin($login);
        $motoboy->setEmail($email);
        $motoboy->setEmpresa($empresa);

        return $motoboy;
    }

    private function validateData($data, $atualizarSenha)
    {

        $basic = [];
        if($atualizarSenha){
            $basic = v::arrayType()
                ->key('senha', v::stringType()->notEmpty()->setName("senha"));
        }


        $validation = v::arrayType()
            ->key('nome', v::stringType()->notEmpty()->setName("nome"))
            ->key('email', v::email()->setName("email"))
            ->key('login', v::stringType()->notEmpty()->setName("login"));

        v::allOf($basic, $validation)->assert($data);
    }

    private function getErrors(NestedValidationException $exception, Application $app)
    {
        $errors = array_filter(
            $exception->findMessages([
                'login' => "Login inválido",
                'nome' => "Nome inválido",
                'email' => "Email inválido",
                'senha' => "Senha inválida"
            ]),
            function ($value) {
                return (strlen($value) > 0);
            });
        return $errors;
    }

}
