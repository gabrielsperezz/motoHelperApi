<?php

namespace MotoHelper\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Empresa
 * @ORM\Entity
 * @ORM\Table(name="empresa")
 */
class Empresa
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
    private $nome;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $razao_social;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $documento;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $email;


    public function getId() {
        return $this->id;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
        return $this;
    }

    public function getRazaoSocial()
    {
        return $this->razao_social;
    }

    public function setRazaoSocial($razao_social)
    {
        $this->razao_social = $razao_social;
        return $this;
    }

    public function getDocumento()
    {
        return $this->documento;
    }

    public function setDocumento($documento)
    {
        $this->documento = $documento;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'email' => $this->email,
            'documento' => $this->documento,
            'razao_social' => $this->razao_social
        ];
    }
}
