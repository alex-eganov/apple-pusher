<?php

namespace bIbI4k0\ApplePusher;

use JsonSerializable;

/**
 * Class Response
 * @package ApplePusher
 */
class Response implements JsonSerializable
{
    private int $code;
    private ?string $reason;
    private ?array $body;

    /**
     * @param int $code
     * @param string $reason
     * @param array|null $body
     */
    public function __construct(int $code, string $reason, array $body = null)
    {
        $this->code = $code;
        $this->reason = $reason;
        $this->body = $body;
    }

    public static function fromJson(int $code, string $json): self
    {
        $json = json_decode($json, true, JSON_THROW_ON_ERROR);
        return new self($code, $json['reason'] ?? null, $json);
    }

    /**
     * @return bool
     */
    public function isOk(): bool
    {
        return $this->code === ResponseStatus::STATUS_OK;
    }

    /**
     * @return string|null
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * @return array|null
     */
    public function getBody(): ?array
    {
        return $this->body;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'ok' => $this->isOk(),
            'reason' => $this->getReason(),
            'body' => $this->getBody(),
        ];
    }
}
