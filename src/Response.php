<?php

namespace bIbI4k0\ApplePusher;

use bIbI4k0\ApplePusher\Exception\ResponseParseException;
use JsonSerializable;

/**
 * Class Response
 * @package bIbI4k0\ApplePusher
 */
class Response implements JsonSerializable
{
    /**
     * @var int
     */
    private $code;

    /**
     * @var Push
     */
    private $push;

    /**
     * @var string|null
     */
    private $reason;

    /**
     * @param int $code status code from apns-response
     * @param Push $push
     * @param string|null $reason text reason for the failed request
     */
    public function __construct(int $code, Push $push, string $reason = null)
    {
        $this->code = $code;
        $this->push = $push;
        $this->reason = $reason;
    }

    /**
     * @param string $json
     * @param int $statusCode
     * @param Push $push
     *
     * @return static
     *
     * @throws ResponseParseException
     */
    public static function fromJson(string $json, int $statusCode, Push $push): self
    {
        $json = trim($json);
        if (!$json) {
            return new self($statusCode, $push);
        }

        $json = json_decode($json, true);
        if (($errNo = json_last_error()) !== JSON_ERROR_NONE) {
            throw new ResponseParseException(json_last_error_msg(), $errNo);
        }

        return new self($statusCode, $push, $json['reason'] ?? null);
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
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'ok' => $this->isOk(),
            'pushId' => $this->getPushId(),
            'device' => $this->push->getDeviceToken(),
            'reason' => $this->getReason(),
        ];
    }
}
