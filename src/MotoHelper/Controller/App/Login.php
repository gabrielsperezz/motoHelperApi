<?php

namespace MotoHelper\Controller\App;

use MotoHelper\Entity\LoginAdministrador;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;
use MotoHelper\Entity\AccessToken;
use MotoHelper\Entity\LoginAluno;
use MotoHelper\Helper\Cookie;
use MotoHelper\Helper\PasswordHash;
use MotoHelper\Helper\RequestParamsParser;
use MotoHelper\Entity\Login as LoginEntity;

class Login
{

    public static function addRoutes( $routing )
    {
        $routing->get('/login' , array(new self() , 'login'))->bind('login');
        $routing->post('/login' , array(new self() , 'checkLogin'))->bind('login_post');
        $routing->post('/aluno' , array(new self() , 'criarNovoLogin'))->bind('post_cadastrar_login');
        $routing->get('/login/logout' , array(new self() , 'logout'))->bind('login_logout');
    }


    public function login( Application $app )
    {
        return $app['twig']->render('login/login.html.twig' , array());
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

            $loginComany = new LoginAdministrador();

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

    public function criarNovoLogin( Application $app )
    {
        $response = new JsonResponse();
        $request = $app['request'];

        try {
            $this->validateData($request->request->all());

            $login = strtolower($request->request->filter('login' , null));
            $email = $request->request->filter('email' , null);

            $loginAlunoRepository = $app['orm.em']->getRepository(LoginAluno::class);
            
            $loginAlunoComLoginCadastrado = $loginAlunoRepository->findOneByLoginOrEmail($login , $email);
            $loginAlunoComLoginCadastradoExiste = !empty($loginAlunoComLoginCadastrado);

            if(!$loginAlunoComLoginCadastradoExiste) {

                $loginAluno = new LoginAluno();

                $this->definirLoginClienteFromRequest($loginAluno , $request);


                $entityManager = $app['orm.em'];
                $entityManager->persist($loginAluno);
                $entityManager->flush();

                $response->setData($loginAluno->toArray());
                $response->setStatusCode(Response::HTTP_OK);
                $this->createNewSession($app,$loginAluno, $response);

            }elseif($loginAlunoComLoginCadastradoExiste) {
                $response->setStatusCode(Response::HTTP_CONFLICT);
                $response->setData(["erros" => $this->getErrorLoginClienteJaExiste($app , $loginAlunoComLoginCadastrado)]);
            }
        }catch(NestedValidationException $exception) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setData(["erros" => $this->getErrors($exception , $app)]);
        }

        return $response;
    }

    private function definirLoginClienteFromRequest(LoginAluno $loginAluno, Request $request)
    {
        $login     = $request->request->filter('usuario', null);
        $descricao = $request->request->filter('descricao', null);
        $email     = $request->request->filter('email', null, FILTER_SANITIZE_EMAIL);
        $senha     = $request->request->filter('password', null);
        $ra        = $request->request->filter('ra', null);

        $loginAluno->setLogin(strtolower($login));
        $loginAluno->setDescricao($descricao);
        $loginAluno->setEmail(strtolower($email));
        $loginAluno->setRa($ra);
        $loginAluno->setSenha(PasswordHash::gerarHashSenha($senha));
        return $loginAluno;
    }

    private function getErrorLoginClienteJaExiste(Application $app, $loginAlunoComLoginCadastrado)
    {
        $errors = [];

        $request = $app['request'];

        $login = strtolower($request->request->filter('login', null));
        $email = strtolower($request->request->filter('email', null));

        foreach ($loginAlunoComLoginCadastrado as $loginAluno){

            if ($loginAluno->getLogin() === $login) {
                $errors['login'] = "Login já cadastrado";
            }

            if ($loginAluno->getEmail() === $email) {
                $errors['email'] = "Email já cadastrado";
            }
        }

        return $errors;
    }

    private function validateData($data)
    {
        $validation = v::arrayType()
            ->key('usuario', v::regex('/^[a-zA-z09@.-_]{6,}$/')->setName('login'))
            ->key('descricao', v::stringType()->notEmpty()->setName("descricao"))
            ->key('ra', v::stringType()->notEmpty()->setName("ra"))
            ->key('email', v::email()->setName("email"))
            ->key('password', v::regex('/^[a-zA-Z0-9!@#$%^&*()-=_+]{4,}$/'));

        $validation->assert($data);
    }

    private function getErrors(NestedValidationException $exception, Application $app)
    {
        $errors = array_filter(
            $exception->findMessages([
                'usuario'     => "Login inválido" ,
                'descricao' => "Descrição é inválida",
                'email'     => "Email é inválido",
                'ra'     => "R.A informado é inválido",
                'password'     => "Senha é inválida"
            ]),
            function ($value) {
                return (strlen($value) > 0);
            });
        return $errors;
    }


    public function logout( Application $app , Request $request )
    {
        $response = $app->redirect("/login");
        $token = Cookie::getCookie($app , $request);

        if(strlen($token) > 0) {
            $entityManager = $app['orm.em'];

            $loginAccessTokenRepository = $entityManager->getRepository(AccessToken::class);
            $accessToken = $loginAccessTokenRepository->findOneBy(['token' => $token]);

            $entityManager->remove($accessToken);
            $entityManager->flush();
        }
        $response->headers->clearCookie(Cookie::COOKIE_NAME);
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

        $entityManager->persist($accessToken);
        $entityManager->flush();

        $loginToken = $accessToken->getToken();
        Cookie::setCookie($loginToken , $response);
    }

}
