<?php

namespace MotoHelper\Controller\App;

use MotoHelper\Entity\LoginAdministrador;
use MotoHelper\Entity\LoginEmpresa;
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

        $routing->get('/login' , array(new self() , 'login'))->bind('login_app');
        $routing->post('/login' , array(new self() , 'checkLogin'))->bind('login_post_app');
        $routing->get('/login/logout' , array(new self() , 'logout'))->bind('login_logout_app');
    }


    public function login( Application $app )
    {
        return $app['twig']->render('app/login/login.html.twig' , array());
    }

    public function checkLogin( Request $request , Application $app )
    {
        $validation = v::arrayType()->key('username' , v::stringType()->notEmpty())
            ->key('password' , v::stringType()->regex('/^[a-zA-z0-9!@#$%]{6,}$/'));

        $response = new Response(json_encode(array('erro' => "Erro interno")) , Response::HTTP_INTERNAL_SERVER_ERROR);

        $login = $request->request->get('username');
        $password = $request->request->get('password');

        try {
            $validation->assert($request->request->all());

            $loginComany = new LoginEmpresa();

            $loginComany->setLogin($login);
            $loginComany->setSenha($password);

            if($this->verificarUsuarioPorLoginESenha($app , $loginComany)) {
                $this->createNewSession($app , $loginComany , $response);
                $response->setStatusCode(Response::HTTP_NO_CONTENT);
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

    public function logout( Application $app , Request $request )
    {
        $response = $app->redirect("/login");
        $token = Cookie::getCookieApp($app , $request);

        if(strlen($token) > 0) {
            $entityManager = $app['orm.em'];

            $loginAccessTokenRepository = $entityManager->getRepository(AccessToken::class);
            $accessToken = $loginAccessTokenRepository->findOneBy(['token' => $token]);

            $entityManager->remove($accessToken);
            $entityManager->flush();
        }
        $response->headers->clearCookie(Cookie::COOKIE_NAME_APP);
        return $response;
    }


    private function verificarUsuarioPorLoginESenha( Application $app , LoginEntity &$login )
    {
        $loginValido = false;

        $entityManager = $app['orm.em'];

        $loginRepository = $entityManager->getRepository(LoginEntity::class);
        $loginE = $loginRepository->findOneBy(['login' => $login->getLogin()]);

        if(!is_null($loginE) && $loginE->verifyPassword($login->getSenha())) {
            $loginValido = true;
            $login = $loginE;
        }

        return $loginValido;
    }

    private function createNewSession( Application $app , LoginEntity $user , Response $response )
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

        $loginToken = $accessToken->getToken();
        Cookie::setCookieApp($loginToken , $response);
    }

}
