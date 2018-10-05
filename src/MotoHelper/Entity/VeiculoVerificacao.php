<?php

namespace MotoHelper\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VeiculoVerificacao
 * @ORM\Entity
 * @ORM\Table(name="veiculo_verificacao")
 */
class VeiculoVerificacao
{

    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Veiculo
     * @ORM\OneToOne(targetEntity="Veiculo",inversedBy="verificacao")
     * @ORM\JoinColumn(name="id_veiculo",referencedColumnName="id", onDelete="CASCADE")
     */
    private $veiculo;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $verificado;

    /**
     * @var datetime
     * @ORM\Column(type="datetime")
     */
    private $data_solicitacao;

    /**
     * @var datetime
     * @ORM\Column(type="datetime", nullable = true)
     */
    private $data_resposta;

    public function __construct( Veiculo $veiculo )
    {
        $this->data_solicitacao = new \DateTime('UTC');
        $this->verificado = false;
        $this->veiculo = $veiculo;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getVeiculo()
    {
        return $this->veiculo;
    }

    public function setVeiculo( Veiculo $veiculo)
    {
        $this->veiculo = $veiculo;
        return $this;
    }

    public function isVerificado()
    {
        return $this->verificado;
    }

    public function setVerificado( )
    {
        $this->verificado = true;
        $this->data_resposta = new \DateTime();
        return $this;
    }

    public function getDataSolicitacao()
    {
        return $this->data_solicitacao;
    }

    public function setDataSolicitacao( $data_solicitacao )
    {
        $this->data_solicitacao = $data_solicitacao;
        return $this;
    }

    public function getDataResposta()
    {
        return $this->data_resposta;
    }

    public function setDataResposta( $data_resposta )
    {
        $this->data_resposta = $data_resposta;
        return $this;
    }

    public function toArray()
    {
        $dataSolicitacao = $this->data_solicitacao->format('d-m-Y');

        return [
            "data_solicitacao" => $dataSolicitacao,
            "data_resposta" => $this->data_resposta,
            "verificado" => $this->verificado
        ];
    }
}
