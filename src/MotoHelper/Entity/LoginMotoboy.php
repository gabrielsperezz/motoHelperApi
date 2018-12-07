<?php

namespace MotoHelper\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    protected $disponivel_para_corrida;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $id_corrida_atual;

    /**
     * @var Veiculo
     * @ORM\ManyToOne(targetEntity="Veiculo")
     * @ORM\JoinColumn(name="id_veiculo",referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $veiculo_atual;


    public function __construct()
    {
        $this->veiculo_motoboy = new ArrayCollection();
        $this->disponivel_para_corrida = false;
    }

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

    public function getVeiculoMotoboy()
    {
        return $this->veiculo_motoboy;
    }

    public function setVeiculoMotoboy($veiculo_motoboy)
    {
        $this->veiculo_motoboy = $veiculo_motoboy;
        return $this;
    }

    public function isDisponivelParaCorrida()
    {
        return $this->disponivel_para_corrida;
    }

    public function setDisponivelParaCorrida($disponivel_para_corrida)
    {
        $this->disponivel_para_corrida = $disponivel_para_corrida;
        return $this;
    }

    public function getIdCorridaAtual()
    {
        return $this->id_corrida_atual;
    }

    public function setIdCorridaAtual($id_corrida_atual)
    {
        $this->id_corrida_atual = $id_corrida_atual;
        return $this;
    }

    public function getVeiculoAtual()
    {
        return $this->veiculo_atual;
    }

    public function setVeiculoAtual($veiculo_atual)
    {
        $this->veiculo_atual = $veiculo_atual;
        return $this;
    }

    public function toArray()
    {
        $veicuclos = [];
        foreach ($this->veiculo_motoboy as $veiculo){
            array_push($veicuclos, $veiculo->getVeiculo()->toArray());
        }
        return array_merge(parent::toArray(), [
            'veiculos' => $veicuclos,
            'veiculo_atual' => is_null($this->veiculo_atual)? null : $this->veiculo_atual->toArray()
        ]);
    }

}
