<?php

namespace MotoHelper\Controller\Api;

use Doctrine\ORM\EntityManager;
use MotoHelper\Entity\LoginEmpresa;
use MotoHelper\Helper\PasswordHash;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;
use MotoHelper\Entity\Empresa;

class EmpresaController
{

    public static function addRoutes($routing)
    {
        $routing->post('/empresa', array(new self(), 'cadastrarEmpresa'))->bind('post_cadastrar_empresa');
        $routing->put('/empresa/{id}', array(new self(), 'atualizarEmpresa'))->bind('put_atualizar_empresa');
        $routing->delete('/empresa/{id}', array(new self(), 'deletarEmpresa'))->bind('delete_deletar_empresa');
    }

    public function cadastrarEmpresa(Application $app)
    {
        $response = new JsonResponse();
        $request = $app['request'];

        try {
            $this->validateData($request->request->all());

            $empresa = new Empresa();

            $this->definirempresaFromRequest($empresa, $request);

            $entityManager = $app['orm.em'];
            $entityManager->persist($empresa);
            $entityManager->flush();

            $this->criarNovoLogin($empresa, $entityManager);

            $response->setData(["empresa" => $empresa->toArray(), "msg" => "Informações do empresa atualizada com sucesso"]);
            $response->setStatusCode( Response::HTTP_OK);

        } catch (NestedValidationException $exception) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setData(["erros" => $this->getErrors($exception, $app)]);
        }

        return $response;
    }

    public function atualizarEmpresa(Application $app, $id)
    {
        $response = new JsonResponse();
        $request = $app['request'];

        try {
            $empresaRepository = $app['orm.em']->getRepository(Empresa::class);
            $empresa = $empresaRepository->find($id);
            $empresaExiste = !is_null($empresa);
            if($empresaExiste){
                $this->validateData($request->request->all());

                $this->definirempresaFromRequest($empresa, $request);

                $entityManager = $app['orm.em'];
                $entityManager->persist($empresa);
                $entityManager->flush();

                $response->setData(["empresa" => $empresa->toArray(), "msg" => "Informações do empresa atualizada com sucesso"]);
                $response->setStatusCode(Response::HTTP_OK);
            }else{
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                $response->setData(["erros" => ["empresa" => "Empresa não encontrado"]]);
            }

        } catch (NestedValidationException $exception) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setData(["erros" => $this->getErrors($exception, $app)]);
        }

        return $response;
    }

    public function deletarEmpresa(Application $app, $id)
    {
        $response = new JsonResponse();

        try {
            $empresaRepository = $app['orm.em']->getRepository(Empresa::class);
            $empresa = $empresaRepository->find($id);
            $empresaExiste = !is_null($empresa);
            if($empresaExiste){
                $entityManager = $app['orm.em'];
                $entityManager->remove($empresa);
                $entityManager->flush();

                $response->setData(["msg" => "Empresa deletado com sucesso"]);
                $response->setStatusCode(Response::HTTP_OK);
            }else{
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                $response->setData(["erros" => ["empresa" => "Empresa não encontrado"]]);
            }

        } catch (NestedValidationException $exception) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setData(["erros" => $this->getErrors($exception, $app)]);
        }

        return $response;
    }

    private function definirempresaFromRequest(Empresa $empresa, Request $request)
    {
        $nome = $request->request->filter('nome', null);
        $email = $request->request->filter('email', null);
        $documento = $request->request->filter('documento', null);
        $razao_social = $request->request->filter('razao_social', null);

        $empresa->setNome($nome);
        $empresa->setEmail($email);
        $empresa->setDocumento($documento);
        $empresa->setRazaoSocial($razao_social);

        return $empresa;
    }

    private function criarNovoLogin(Empresa $empresa, EntityManager $em)
    {
        $loginEmpresa = new LoginEmpresa();
        $loginEmpresa->setEmpresa($empresa);
        $loginEmpresa->setLogin($empresa->getDocumento());
        $loginEmpresa->setEmail($empresa->getEmail());
        $loginEmpresa->setSenha(PasswordHash::gerarHashSenha(123456));
        $loginEmpresa->setDescricao($empresa->getNome());
        $em->persist($loginEmpresa);
        $em->flush();
    }

    private function validateData($data)
    {
        $validation = v::arrayType()
            ->key('nome', v::stringType()->notEmpty()->setName("nome"))
            ->key('razao_social', v::stringType()->notEmpty()->setName("razao_social"))
            ->key('documento', v::stringType()->notEmpty()->setName("documento"))
            ->key('email', v::email()->notEmpty()->setName("email"));

        $validation->assert($data);
    }

    private function getErrors(NestedValidationException $exception, Application $app)
    {
        $errors = array_filter(
            $exception->findMessages([
                'nome' => "Nome inválido",
                'email' => "Email inválido",
                'documento' => "Documento inválido",
                'razao_social' => "Razão social inválida"
            ]),
            function ($value) {
                return (strlen($value) > 0);
            });
        return $errors;
    }

}
