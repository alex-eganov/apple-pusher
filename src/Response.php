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
    private string $pushUuid;
    private ?string $reason;
    private ?array $body;

    /**
     * @param int $code
     * @param string $pushUuid
     * @param string|null $reason
     * @param array|null $body
     */
    public function __construct(int $code, string $pushUuid, string $reason = null, array $body = null)
    {
        $this->code = $code;
        $this->pushUuid = $pushUuid;
        $this->reason = $reason;
        $this->body = $body;
    }

    /**
     * @param int $code
     * @param string $pushUuid
     * @param string $json
     * @return static
     */
    public static function fromJson(int $code, string $pushUuid, string $json): self
    {
        $json = json_decode($json, true, JSON_THROW_ON_ERROR);
        return new self($code, $pushUuid, $json['reason'] ?? null, $json);
    }

    /**
     * @return string
     */
    public function getPushUuid(): string
    {
        return $this->pushUuid;
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
            'pushId' => $this->getPushUuid(),
            'reason' => $this->getReason(),
            'body' => $this->getBody(),
        ];
    }
}
