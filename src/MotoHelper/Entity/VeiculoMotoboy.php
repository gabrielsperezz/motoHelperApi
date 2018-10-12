<?php

namespace MotoHelper\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Veiculo
 * @ORM\Entity
 * @ORM\Table(name="veiculo_motoboy")
 */
class VeiculoMotoboy
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
     * @ORM\ManyToOne(targetEntity="Veiculo")
     * @ORM\JoinColumn(name="id_veiculo",referencedColumnName="id", onDelete="CASCADE")
     */
    private $veiculo;

    /**
     * @var LoginMotoboy
     * @ORM\ManyToOne(targetEntity="LoginMotoboy")
     * @ORM\JoinColumn(name="id_motoboy",referencedColumnName="id",onDelete="CASCADE")
     */
    private $motoboy;


    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function getVeiculo()
    {
        return $this->veiculo;
    }

    public function setVeiculo($veiculo)
    {
        $this->veiculo = $veiculo;
        return $this;
    }

    public function getMotoboy()
    {
        return $this->motoboy;
    }

    public function setMotoboy($motoboy)
    {
        $this->motoboy = $motoboy;
        return $this;
    }

    public function toArray(){
        return [
            'veiculo' => $this->veiculo->toArray(),
            'motoboy' => $this->motoboy->toArray()
        ];
    }


}
