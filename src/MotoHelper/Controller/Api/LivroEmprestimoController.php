<?php

namespace MotoHelper\Controller\Api;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;
use MotoHelper\Entity\Veiculo;
use MotoHelper\Entity\LivrosEmprestimo;
use MotoHelper\Entity\Login;
use MotoHelper\Entity\LoginAluno;

class LivroEmprestimoController
{

    public static function addRoutes($routing)
    {
        $routing->post('/livro/emprestimo', array(new self(), 'cadastrarLivroEmprestimo'))->bind('post_cadastrar_livro_eprestimo');
        $routing->patch('/livro/emprestimo/{id}/entregue', array(new self(), 'atualizarLivroEntregue'))->bind('put_atualizar_livro_entregue');
    }

    public function cadastrarLivroEmprestimo(Application $app )
    {
        $response = new JsonResponse();
        $request = $app['request'];

        try {
            $this->validateData($request->request->all());

            $entityManager = $app['orm.em'];

            $idLivro = $request->request->filter('livro', null);
            $idLogin = $request->request->filter('aluno', null);

            $livro = $entityManager->getReference(Veiculo::class, $idLivro);
            $login = $entityManager->getReference(LoginAluno::class, $idLogin);

            if(!is_null($livro) && !is_null($login)){

                if($livro->isDisponivel()){
                    $livroEmprestimo = new LivrosEmprestimo();

                    $this->definirlivroFromRequest($livroEmprestimo,  $login, $livro);

                    $entityManager->persist($livroEmprestimo);
                    $entityManager->flush();

                    $response->setData(["msg" => "Emprestimo realizado com sucesso"]);
                    $response->setStatusCode( Response::HTTP_OK);
                }else{
                    $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                    $response->setData(["erros" => ["livro" => "O Livro fornecido não está mais disponivel"]]);
                }

            }else if(is_null($login)){
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $response->setData(["erros" => ["login" => "O Aluno fornecido não foi encontrado, talvez ele possa ter sido excluido"]]);
            }else if(is_null($livro)){
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $response->setData(["erros" => ["livro" => "O Livro fornecido não foi encontrado, talvez ele possa ter sido excluido"]]);
            }

        } catch (NestedValidationException $exception) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setData(["erros" => $this->getErrors($exception, $app)]);
        }catch (ORMException $exception) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setData(["erros" => $this->getErrors($exception, $app)]);
        }

        return $response;
    }

    public function atualizarLivroEntregue(Application $app, $id)
    {
        $response = new JsonResponse();

        try {
            $livroRepository = $app['orm.em']->getRepository(LivrosEmprestimo::class);
            $livro = $livroRepository->find($id);
            $livroExiste = !is_null($livro);
            if($livroExiste){

                $livro->setEntregue(true);
                $entityManager = $app['orm.em'];
                $entityManager->persist($livro);
                $entityManager->flush();

                $response->setData(["livro" => $livro->toArray(), "msg" => "Informações do livro atualizada com sucesso"]);
                $response->setStatusCode(Response::HTTP_OK);
            }else{
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                $response->setData(["erros" => ["livro" => "Livro não encontrado"]]);
            }

        } catch (NestedValidationException $exception) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setData(["erros" => $this->getErrors($exception, $app)]);
        }

        return $response;
    }

    private function definirlivroFromRequest(LivrosEmprestimo $livroEmprestimo, LoginAluno $aluno, Veiculo $livro )
    {
        $dataEmprestimo = new \DateTime();
        $dataEntrega = clone $dataEmprestimo;
        $dateInterval = new \DateInterval("P3D");
        $dataEntrega->add($dateInterval);

        $livroEmprestimo->setLogin($aluno);
        $livroEmprestimo->setLivro($livro);
        $livroEmprestimo->setDataDevolucao($dataEntrega);
        $livroEmprestimo->setDataEmprestimo($dataEmprestimo);

        return $livroEmprestimo;
    }

    private function validateData($data)
    {
        $validation = v::arrayType()
            ->key('aluno', v::numeric()->positive()->setName("aluno"))
            ->key('livro', v::stringType()->positive()->setName("livro"));

        $validation->assert($data);
    }

    private function getErrors(NestedValidationException $exception, Application $app)
    {
        $errors = array_filter(
            $exception->findMessages([
                'livro' => "Livro não pode ser vázio",
                'aluno' => "Aluno não pode ser vázio"
            ]),
            function ($value) {
                return (strlen($value) > 0);
            });
        return $errors;
    }

}
