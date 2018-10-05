<?php

namespace MotoHelper\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LoginAdministrador
 * @ORM\Entity(repositoryClass="MotoHelper\Repository\LoginAdministradorRepository")
 * 
 */
class LoginAdministrador extends Login
{

    const TIPO_ADMIN = 1;

    public function getTipo()
    {
        return self::TIPO_ADMIN;
    }

}
