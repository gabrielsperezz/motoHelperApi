<?php

namespace MotoHelper\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LoginCliente
 * @ORM\Entity(repositoryClass="MotoHelper\Repository\LoginClienteRepository")
 * 
 */
class LoginCliente extends Login
{

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    protected $procurando_corrida;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $id_corrida_atual;

    public function isProcurandoCorrida()
    {
        return $this->procurando_corrida;
    }

    public function setProcurandoCorrida($procurando_corrida)
    {
        $this->procurando_corrida = $procurando_corrida;
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



    const TIPO_CLIENTE = 3;

    public function getTipo()
    {
        return self::TIPO_CLIENTE;
    }


    private function _toArray()
    {
        return [
            "procurando_corrida" => $this->procurando_corrida,
            "corrida_atual" => $this->id_corrida_atual
        ];
    }

    public function toArray()
    {
        return array_merge(parent::toArray(),$this->_toArray());
    }

}
