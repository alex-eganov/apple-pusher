<?php

namespace bIbI4k0\ApplePusher\Auth;

use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
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
    private const TOKEN_LIFETIME_MINUTES = 59;

    /**
     * @var Token|null
     */
    private ?Token $token = null;

    /**
     * @var string
     */
    private string $apnsId;

    /**
     * @var string
     */
    private string $teamId;

    /**
     * @var Configuration
     */
    private Configuration $jwtConfig;

    /**
     * @param string $apnsId APNS key
     * @param string $teamId Apple developers team ID
     * @param string $keyFileOrContent .p8 key file content or file URI to it starts with prefix "file://"
     */
    public function __construct(string $apnsId, string $teamId, string $keyFileOrContent)
    {
        $this->apnsId = $apnsId;
        $this->teamId = $teamId;
        $this->jwtConfig = Configuration::forSymmetricSigner(new Sha256(), $this->makeKey($keyFileOrContent));
    }

    /**
     * @param string $keyFileOrContent
     * @return Key
     */
    protected function makeKey(string $keyFileOrContent): Key
    {
        if (strpos($keyFileOrContent, 'file://') === 0) {
            return Key\InMemory::file(substr($keyFileOrContent, 7));
        }

        return Key\InMemory::plainText($keyFileOrContent);
    }

    /**
     * Return actual token as string. If token was expired, this method will refresh it
     *
     * @return string
     */
    private function getTokenAsString(): string
    {
        $now = new DateTimeImmutable();
        if ($this->token === null || $this->token->isExpired($now)) {
            $this->token = $this->jwtConfig->builder()
                ->issuedBy(strtoupper($this->teamId))
                ->issuedAt($now)
                ->expiresAt($now->modify(sprintf('+%s minute', self::TOKEN_LIFETIME_MINUTES)))
                ->withHeader('kid', strtoupper($this->apnsId))
                ->getToken($this->jwtConfig->signer(), $this->jwtConfig->signingKey());
        }

        return $this->token->toString();
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
