<?php

namespace MotoHelper\Controller\ApiMobile;

use MotoHelper\Entity\LoginAdministrador;
use MotoHelper\Entity\LoginCliente;
use MotoHelper\Entity\LoginEmpresa;
use MotoHelper\Entity\LoginMotoboy;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;
use MotoHelper\Entity\AccessToken;
use MotoHelper\Helper\Cookie;
use MotoHelper\Helper\PasswordHash;
use MotoHelper\Helper\RequestParamsParser;
use MotoHelper\Entity\LoginEmpresa as LoginEntity;

class Login
{

    public static function addRoutes( $routing )
    {

        $routing->post('/login' , array(new self() , 'checkLogin'))->bind('login_app_hibrido');
        $routing->post('/login/motoboy' , array(new self() , 'checkLoginMotoboy'))->bind('login_app_hibrido_motoboy');
        $routing->post('/login/novo' , array(new self() , 'cadastrarNovoLoginMobile'))->bind('cadastrar_login_app_mobile');
    }

    public function checkLoginMotoboy( Request $request , Application $app )
    {

        $validation = v::arrayType()->key('username' , v::stringType()->notEmpty())
            ->key('password' , v::stringType()->regex('/^[a-zA-z0-9!@#$%]{6,}$/'));

        $response = new JsonResponse();

        $login = $request->request->get('username');
        $password = $request->request->get('password');

        try {
            $validation->assert($request->request->all());

            $loginMotoboy = new LoginMotoboy();

            $loginMotoboy->setLogin($login);
            $loginMotoboy->setSenha($password);

            if($this->verificarUsuarioMotoboyPorLoginESenha($app , $loginMotoboy)) {
                $token = $this->getNewToken($app , $loginMotoboy );

                $response->setData($token);

                $response->setStatusCode(Response::HTTP_OK);
            }else {
                $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
                $response->setContent(json_encode(array("erro" => 'Não foi possível autenticar com os dados informados.')));
            }
        }catch(NestedValidationException $exception) {
            $errors = array_filter(
                $exception->findMessages([
                    'username' => 'Login não pode estar vazio' ,
                    'password' => 'Senha inválida'
                ]) ,
                function( $value ) {
                    return (strlen($value) > 0);

                });
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setContent(json_encode(array("erros" => $errors)));
        }catch(\Exception $ex) {
            $app['logger']->critical($ex->getMessage());
        }

        return $response;
    }


    public function checkLogin( Request $request , Application $app )
    {
        $validation = v::arrayType()->key('username' , v::stringType()->notEmpty())
            ->key('password' , v::stringType()->regex('/^[a-zA-z0-9!@#$%]{6,}$/'));

        $response = new JsonResponse();

        $login = $request->request->get('username');
        $password = $request->request->get('password');

        try {
            $validation->assert($request->request->all());

            $loginCliente = new LoginCliente();

            $loginCliente->setLogin($login);
            $loginCliente->setSenha($password);

            if($this->verificarUsuarioPorLoginESenha($app , $loginCliente)) {
                $token = $this->getNewToken($app , $loginCliente );

                $response->setData($token);

                $response->setStatusCode(Response::HTTP_OK);
            }else {
                $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
                $response->setContent(json_encode(array("erro" => 'Não foi possível autenticar com os dados informados.')));
            }
        }catch(NestedValidationException $exception) {
            $errors = array_filter(
                $exception->findMessages([
                    'username' => 'Login não pode estar vazio' ,
                    'password' => 'Senha inválida'
                ]) ,
                function( $value ) {
                    return (strlen($value) > 0);

                });
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setContent(json_encode(array("erros" => $errors)));
        }catch(\Exception $ex) {
            $app['logger']->critical($ex->getMessage());
        }

        return $response;
    }

    public function cadastrarNovoLoginMobile(Application $app)
    {
        $response = new JsonResponse();
        $request = $app['request'];

        try {
            $this->validateData($request->request->all());

            $loginCliente = new LoginCliente();

            $this->definirNovoLoginFromRequest($loginCliente, $request);

            $entityManager = $app['orm.em'];
            $entityManager->persist($loginCliente);
            $entityManager->flush();

            $response->setData(["login" => $loginCliente->toArray(), "msg" => "Login cadastrado com sucesso"]);
            $response->setStatusCode( Response::HTTP_OK);

        } catch (NestedValidationException $exception) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setData(["erros" => $this->getErrors($exception, $app)]);
        }

        return $response;
    }



    private function verificarUsuarioPorLoginESenha( Application $app , LoginCliente &$login )
    {
        $loginValido = false;

        $entityManager = $app['orm.em'];

        $loginRepository = $entityManager->getRepository(LoginCliente::class);
        $loginE = $loginRepository->findOneBy(['login' => $login->getLogin()]);

        if(!is_null($loginE) && $loginE->verifyPassword($login->getSenha())) {
            $loginValido = true;
            $login = $loginE;
        }

        return $loginValido;
    }

    private function verificarUsuarioMotoboyPorLoginESenha( Application $app , LoginMotoboy &$login )
    {
        $loginValido = false;

        $entityManager = $app['orm.em'];

        $loginRepository = $entityManager->getRepository(LoginMotoboy::class);

        $loginE = $loginRepository->findOneBy(['login' => $login->getLogin()]);

        if(!is_null($loginE) && $loginE->verifyPassword($login->getSenha())) {
            $loginValido = true;
            $login = $loginE;
        }

        return $loginValido;
    }


    private function getNewToken( Application $app , \MotoHelper\Entity\Login $user )
    {
        $entityManager = $app['orm.em'];

        $dataAgora = new \DateTime("UTC");

        $token = hash('sha256' , $user->getId() . $user->getLogin() . $dataAgora->getTimestamp());

        $accessToken = new AccessToken();

        $accessToken->setLogin($user);
        $accessToken->setToken($token);
        $accessToken->setTipo(AccessToken::TIPO_APP);

        $entityManager->persist($accessToken);
        $entityManager->flush();

        return $accessToken->toArray();
    }

    private function validateData($data)
    {
        return  v::arrayType()
            ->key('nome', v::stringType()->notEmpty()->setName("nome"))
            ->key('email', v::email()->setName("email"))
            ->key('login', v::stringType()->notEmpty()->setName("login"))
            ->key('senha', v::stringType()->notEmpty()->setName("senha"))->assert($data);
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

    private function definirNovoLoginFromRequest(LoginCliente $loginCliente, Request $request )
    {
        $nome = $request->request->filter('nome', null);
        $login = $request->request->filter('login', null);
        $email = $request->request->filter('email', null);
        $senha = $request->request->filter('senha', null);

        $loginCliente->setSenha(PasswordHash::gerarHashSenha($senha));
        $loginCliente->setDescricao($nome);
        $loginCliente->setLogin($login);
        $loginCliente->setEmail($email);

        return $loginCliente;
    }

}
