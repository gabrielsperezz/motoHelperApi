<?php

namespace MotoHelper\Repository;

use Doctrine\ORM\EntityRepository;
use MotoHelper\Entity\AccessToken;

class AccessTokenRepository extends EntityRepository {

    public function getAccessToken($accessToken)
    {
        return $this->findOneBy(['token' => $accessToken]);
    }

    public function setAccessToken( $userId )
    {
        $data = new \DateTime();
        $salt = "quintoSemestre";
        $hash = (hash('sha256' , ($data->getTimestamp()).$salt));
        $accessToken = new AccessToken();

        $accessToken->setAccessToken($hash);
        $accessToken->setUserId($userId);

        $this->_em->persist($accessToken);
        $this->_em->flush();

        return $accessToken;
    }

}
