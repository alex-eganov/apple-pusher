<?php

namespace bIbI4k0\ApplePusher\Curl;

use CurlHandle;

/**
 * Class-wrapper for permanent curl-connection to apns for multiple push sends
 *
 * Class CachedCurlWrapper
 * @package bIbI4k0\ApplePusher\Curl
 */
class CachedCurlWrapper extends CurlWrapper
{
    /**
     * Max requests before reconnect to apns
     */
    private const MAX_REQUESTS = 500;

    /**
     * @var int
     */
    private int $requestCounter = 0;

    /**
     * @var int
     */
    private int $lastConnectTimestamp = 0;

    /**
     * @var int
     */
    private int $secondsLifetime;

    /**
     * @param int $secondsLifetime connection lifetime in seconds
     */
    public function __construct(int $secondsLifetime)
    {
        $this->secondsLifetime = $secondsLifetime;
    }

    /**
     * @param int $minutesLifetime connection lifetime in minutes
     * @return static
     */
    public static function createWithMinutes(int $minutesLifetime): self
    {
        return new static($minutesLifetime * 60);
    }

    /**
     * Checks that connection has expired
     *
     * @return bool
     */
    private function isExpired(): bool
    {
        return $this->requestCounter >= self::MAX_REQUESTS
            || ($this->lastConnectTimestamp + $this->secondsLifetime) < time();
    }

    /**
     * Re-init curl
     */
    private function init(): void
    {
        $this->close();
        $this->handle = curl_init();
        $this->lastConnectTimestamp = time();
        $this->requestCounter = 0;
    }

    /**
     * @inheritDoc
     */
    protected function getCurl(): CurlHandle
    {
        if ($this->isExpired()) {
            $this->init();
        }

        $this->requestCounter++;

        return $this->handle;
    }
}
