<?php

namespace MotoHelper\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Cor
 * @ORM\Entity
 * @ORM\Table(name="cor")
 */
class Cor
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
    private $descricao;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $label;


    public function getId()
    {
        return $this->id;
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


    public function toArray()
    {
        return [
            'id' => $this->id,
            'descricao' => $this->descricao,
            'label' => $this->label
        ];
    }
}
