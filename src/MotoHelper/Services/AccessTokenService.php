<?php

namespace MotoHelper\Services;

use MotoHelper\Repository\AccessTokenRepository;

class AccessTokenService
{
    private $tokenStorate;
    
    public function __construct(AccessTokenRepository $tokenStorate)
    {
        $this->tokenStorate = $tokenStorate;
    }
    
    public function getToken($token = null)
    {

        try {
            $accessToken = $this->tokenStorate->getAccessToken($token);
        } catch (\Exception $ex) {
            $accessToken = null;
        }

        return $accessToken;
    }

}
