<?php

namespace MotoHelper\Entity;

use Doctrine\ORM\Mapping as ORM;
use MotoHelper\Entity\Login;

/**
 * AccessToken
 * @ORM\Entity(repositoryClass="MotoHelper\Repository\AccessTokenRepository")
 * @ORM\Table(name="login_access_token",
 *  indexes={
 *      @ORM\Index(name="idx_token", columns={"token"})
 *  }
 * )
 */
class AccessToken
{


    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var string
     * @ORM\Column(type="string", length=70, unique=true)
     */
    private $token;

    /**
     * @var Login
     * @ORM\ManyToOne(targetEntity="Login")
     * @ORM\JoinColumn(name="id_login",referencedColumnName="id", onDelete="CASCADE")
     */
    private $login;

    public function getId()
    {
        return $this->id;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function setLogin(Login $login = null)
    {
        $this->login = $login;
        return $this;
    }

    public function toArray()
    {
        $user_id    = ($this->login && $this->login instanceof Login) ? $this->login->getId() : null;
        return [
            'token' => $this->token,
            'user_id' => $user_id
        ];
    }
}
