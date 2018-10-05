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

    const TIPO_CLIENTE = 3;

    public function getTipo()
    {
        return self::TIPO_CLIENTE;
    }

}
