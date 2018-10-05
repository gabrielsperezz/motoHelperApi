<?php

namespace MotoHelper\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Veiculo
 * @ORM\Entity
 * @ORM\Table(name="veiculo")
 */
class Veiculo
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Empresa
     * @ORM\ManyToOne(targetEntity="Empresa")
     * @ORM\JoinColumn(name="id_empresa",referencedColumnName="id", onDelete="CASCADE")
     */
    private $empresa;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $descricao;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $cor;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $modelo;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $fabricante;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $placa;

    /**
     * @var VeiculoVerificacao
     * @ORM\OneToOne(targetEntity="VeiculoVerificacao", mappedBy="veiculo",cascade={"persist","remove"}))
     */
    protected $verificacao;


    public function __construct()
    {
        $this->verificacao = new VeiculoVerificacao($this);
    }

    public function getId() {
        return $this->id;
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

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
        return $this;
    }

    public function getCor()
    {
        return $this->cor;
    }

    public function setCor($cor)
    {
        $this->cor = $cor;
        return $this;
    }

    public function getModelo()
    {
        return $this->modelo;
    }

    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
        return $this;
    }

    public function getFabricante()
    {
        return $this->fabricante;
    }

    public function setFabricante($fabricante)
    {
        $this->fabricante = $fabricante;
    }

    public function getPlaca()
    {
        return $this->placa;
    }

    public function setPlaca($placa)
    {
        $this->placa = $placa;
        return $this;
    }

    public function getVerificacao()
    {
        return $this->verificacao;
    }

    public function setVerificacao($verificacao)
    {
        $this->verificacao = $verificacao;
        return $this;
    }

    public function autorizar()
    {
        $this->verificacao->setVerificado();
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'cor' => $this->cor,
            'placa' => $this->placa,
            'modelo' => $this->modelo,
            'descricao' => $this->descricao,
            'fabricante' => $this->fabricante,
            'verificacao' => $this->verificacao->toArray(),
            'empresa' => $this->empresa->toArray()
        ];
    }
}
