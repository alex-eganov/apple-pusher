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
    private const TOKEN_LIFETIME_SECONDS = 60 * 59;

    /**
     * @var Token
     */
    private $token;

    /**
     * @var string
     */
    private $apnsId;

    /**
     * @var string
     */
    private $teamId;

    /**
     * @var Key
     */
    private $key;

    /**
     * @param string $apnsId APNS key
     * @param string $teamId Apple developers team ID
     * @param string $keyFile .p8 key file content or file URI to it
     */
    public function __construct(string $apnsId, string $teamId, string $keyFile)
    {
        $this->apnsId = $apnsId;
        $this->teamId = $teamId;
        $this->key = new Key($keyFile);
    }

    /**
     * Return actual token as string. If token was expired, this method will refresh it
     *
     * @return string
     */
    private function getTokenAsString(): string
    {
        if ($this->token === null || $this->token->isExpired()) {
            $time = time();

            $this->token = (new Builder())
                ->issuedBy(strtoupper($this->teamId))
                ->issuedAt($time)
                ->expiresAt($time + self::TOKEN_LIFETIME_SECONDS)
                ->withHeader('kid', strtoupper($this->apnsId))
                ->getToken(new Sha256(), $this->key);
        }

        return (string)$this->token;
    }


    /**
     * @return string[]
     */
    public function getRequestHeaders(): array
    {
        return [
            'Authorization: Bearer ' . $this->getTokenAsString(),
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
