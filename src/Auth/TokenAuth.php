<?php

namespace bIbI4k0\ApplePusher\Auth;

/**
 * Class TokenAuth
 * @package ApplePusher\Auth
 */
class TokenAuth implements AuthInterface
{
    private $token;

    public function __construct(string $apnsKey)
    {
        $this->token = new Token;
    }

    public function getCurlOptions(): array
    {
        return [
            CURLOPT_HEADER => [
                'authorization' => 'bearer ' . (string)$this->token,
            ]
        ];
    }
}
