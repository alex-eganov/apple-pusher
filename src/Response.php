<?php

namespace bIbI4k0\ApplePusher;

use JsonSerializable;

/**
 * Class Response
 * @package bIbI4k0\ApplePusher
 */
class Response implements JsonSerializable
{
    private int $code;
    private Push $push;
    private ?string $reason;
    private ?array $body;

    /**
     * @param int $code status code of the request
     * @param Push $push
     * @param string|null $reason text reason for the failed request
     * @param array|null $body decoded json body
     */
    public function __construct(int $code, Push $push, string $reason = null, array $body = null)
    {
        $this->code = $code;
        $this->push = $push;
        $this->reason = $reason;
        $this->body = $body;
    }

    /**
     * @param string $json
     * @param int $statusCode
     * @param Push $push
     * @return static
     */
    public static function fromJson(string $json, int $statusCode, Push $push): self
    {
        $json = json_decode($json, true, JSON_THROW_ON_ERROR);
        return new self($statusCode, $push, $json['reason'] ?? null, $json);
    }

    /**
     * @return Push
     */
    public function getPush(): Push
    {
        return $this->push;
    }

    /**
     * Return unique push id
     *
     * @return string
     */
    public function getPushId(): string
    {
        return $this->push->getUuid();
    }

    /**
     * @return bool
     */
    public function isOk(): bool
    {
        return $this->code === ResponseStatus::STATUS_OK;
    }

    /**
     * Returns text reason of the failed request to apns
     *
     * @return string|null
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * Returns decoded json body of request, or null
     *
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
            'pushId' => $this->getPushId(),
            'device' => $this->push->getDeviceToken(),
            'reason' => $this->getReason(),
            'body' => $this->getBody(),
        ];
    }
}
