<?php

namespace MotoHelper\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use MotoHelper\Entity\LoginEmpresa;

class LoginEmpresaRepository extends EntityRepository {
    public function findOneByLoginOrEmail($login, $email, $diffId = null)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select(array('la'))
            ->from(LoginEmpresa::class, 'la')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->eq("la.login", ":login"), $qb->expr()->eq("la.email", ":email")
                )
            )
            ->setParameter("login", $login, Type::STRING)
            ->setParameter("email", $email, Type::STRING);

        if (is_numeric($diffId) && $diffId > 0) {
            $qb->andWhere($qb->expr()->neq("la.id", ":id"))
                ->setParameter("id", (int) $diffId, Type::INTEGER);
        }

        $q = $qb->getQuery();

        try {
            $loginCliente = $q->getResult();
        } catch (\Exception $e) {
            $loginCliente = null;
        }

        return $loginCliente;
    }

}
