<?php

namespace MotoHelper\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LoginEmpresa
 * @ORM\Entity(repositoryClass="MotoHelper\Repository\LoginEmpresaRepository")
 * 
 */
class LoginEmpresa extends Login
{
    const TIPO_EMPRESA = 2;

    /**
     * @var Empresa
     * @ORM\ManyToOne(targetEntity="Empresa")
     * @ORM\JoinColumn(name="id_empresa",referencedColumnName="id", onDelete="CASCADE")
     */
    private $empresa;

    public function getEmpresa()
    {
        return $this->empresa;
    }

    public function setEmpresa($empresa)
    {
        $this->empresa = $empresa;
        return $this;
    }

    public function getTipo()
    {
        return self::TIPO_EMPRESA;
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), ['empresa'=> $this->getEmpresa()->toArray()]);
    }

}
