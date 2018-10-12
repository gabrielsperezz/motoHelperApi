<?php

namespace MotoHelper\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use MotoHelper\Entity\LoginConfig;
use MotoHelper\Helper\PasswordHash;

/**
 * Login
 * @ORM\Entity(repositoryClass="MotoHelper\Repository\LoginRepository")
 * @ORM\Table(name="login",
 *  indexes={
 *      @ORM\Index(name="idx_email", columns={"email"}),
 *      @ORM\Index(name="idx_login", columns={"login"})
 *  },
 * uniqueConstraints={
 *      @ORM\UniqueConstraint(name="unique_login", columns={"login"})
 * })
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="tipo", type="integer")
 * @ORM\DiscriminatorMap({"1" = "MotoHelper\Entity\LoginAdministrador", "2" = "MotoHelper\Entity\LoginEmpresa", "3" = "MotoHelper\Entity\LoginCliente", "4" = "MotoHelper\Entity\LoginMotoboy"})
 */
abstract class Login
{

    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer") 
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
    * @var string
    * @ORM\Column(type="string")
    */
    protected $login;
    
    /**
    * @var string
    * @ORM\Column(type="string", nullable=true)
    */
    protected $descricao;
    
    /**
    * @var string
    * @ORM\Column(type="string")
    */
    protected $senha;
    
    /**
    * @var string
    * @ORM\Column(type="string")
    */
    protected $email;
    
    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function getLogin()
    {
        return $this->login;
    }

    public function getSenha()
    {
        return $this->senha;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    public function setSenha($senha)
    {
        $this->senha = $senha;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
        return $this;
    }
    
    public function verifyPassword($password)
    {
        return PasswordHash::verificarSenha($password, $this->getSenha());
    }
    
    abstract public function getTipo();

    public function toArray()
    {
        return [
            'id' => $this->id,
            'tipo' => $this->getTipo(),
            'login' => $this->login,
            'email' => $this->email,
            'descricao' => $this->descricao
        ];
    }
}
