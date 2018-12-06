<?php

namespace MotoHelper\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LoginPosicoes
 * @ORM\Entity
 * @ORM\Table(name="login_posicoes")
 */
class LoginPosicoes
{

    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Login
     * @ORM\OneToOne(targetEntity="Login",inversedBy="posicao")
     * @ORM\JoinColumn(name="id_login",referencedColumnName="id", onDelete="CASCADE")
     */
    private $login;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $latitude;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $longitude;

    public function getId()
    {
        return $this->id;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function toArray()
    {
        return [
            "latitude" => $this->latitude,
            "longitude" => $this->longitude
        ];
    }
}
