<?php

namespace MotoHelper\Entity;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Mapping as ORM;

/**
 * LoginEmpresa
 * @ORM\Entity(repositoryClass="MotoHelper\Repository\LoginMotoboyRepository")
 * 
 */
class LoginMotoboy extends Login
{
    const TIPO_MOTOBOY = 4;

    /**
     * @var Empresa
     * @ORM\ManyToOne(targetEntity="Empresa")
     * @ORM\JoinColumn(name="id_empresa",referencedColumnName="id", onDelete="CASCADE")
     */
    private $empresa;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="VeiculoMotoboy", mappedBy="motoboy",cascade={"persist"})
     *
     */
    private $veiculo_motoboy;

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
        return self::TIPO_MOTOBOY;
    }

    public function toArray()
    {
        $veicuclos = [];
        foreach ($this->veiculo_motoboy as $veiculo){
            array_push($veicuclos, $veiculo->getVeiculo()->toArray());
        }
        return array_merge(parent::toArray(), [
            'veiculos' => $veicuclos
        ]);
    }

}
