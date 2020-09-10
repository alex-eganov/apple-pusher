<?php

namespace bIbI4k0\ApplePusher\Auth;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;

/**
 * Token-based stateless authentication
 *
 * Class TokenAuth
 * @package bIbI4k0\ApplePusher\Auth
 */
class TokenAuth implements AuthInterface
{
    /**
     * @var Token
     */
    private Token $token;

    /**
     * @param string $apnsId APNS key
     * @param string $teamId Apple developers team ID
     * @param string $keyFile .p8 key file content or file URI to it
     */
    public function __construct(string $apnsId, string $teamId, string $keyFile)
    {
        $key = new Key($keyFile);
        $time = time();
        $this->token = (new Builder())
            ->issuedBy(strtoupper($teamId))
            ->issuedAt($time)
            ->withHeader('kid', strtoupper($apnsId))
            ->getToken(new Sha256(), $key);
    }


    /**
     * @return string[]
     */
    public function getRequestHeaders(): array
    {
        return [
            'Authorization: Bearer ' . (string)$this->token,
        ];
    }

    /**
     * @return array
     */
    public function getCurlOptions(): array
    {
        return [];
    }
}
