<?php

namespace ApplePusher\Auth;

/**
 * Class TokenAuth
 * @package ApplePusher\Auth
 */
class TokenAuth implements AuthInterface
{
    private $token;

    public function __construct()
    {
        $this->token = '';
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