<?php

namespace bIbI4k0\ApplePusher\Curl;

/**
 * Class CurlResponse
 * @package bIbI4k0\ApplePusher\Curl
 */
final class CurlResponse
{
    private int $code;
    private string $body;

    public function __construct(int $code, string $body)
    {
        $this->code = $code;
        $this->body = $body;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }
}
