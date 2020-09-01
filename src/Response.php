<?php

namespace bIbI4k0\ApplePusher;

/**
 * Class Response
 * @package ApplePusher
 */
class Response
{
    private int $code;
    private string $reason;
    private string $body;

    /**
     * @param int $code
     * @param string $reason
     * @param $body
     */
    public function __construct(int $code, string $reason, $body)
    {
        $this->code = $code;
        $this->reason = $reason;
        $this->body = $body;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->code === ResponseStatus::STATUS_OK;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }
}