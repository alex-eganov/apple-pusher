<?php

namespace bIbI4k0\ApplePusher\Connection;

/**
 * Class CachedConnection
 * @package bIbI4k0\ApplePusher\Connection
 */
class CachedConnection extends Connection
{
    /**
     * @var int
     */
    private $lastConnectTimestamp = 0;

    /**
     * @var int
     */
    private $reconnectAfterSeconds;

    /**
     * @param int $reconnectAfterSeconds
     */
    public function __construct(int $reconnectAfterSeconds)
    {
        $this->reconnectAfterSeconds = $reconnectAfterSeconds;
    }

    /**
     * Checks that connection has expired
     *
     * @return bool
     */
    private function isExpired(): bool
    {
        return $this->lastConnectTimestamp + $this->reconnectAfterSeconds >= time();
    }

    /**
     * Reconnect curl
     */
    private function reconnect(): void
    {
        $this->close();
        $this->handle = curl_init();
        $this->lastConnectTimestamp = time();
    }


    /**
     * Returns curl handle
     *
     * @return \CurlHandle|false|resource
     */
    public function getCurl()
    {
        if ($this->isExpired()) {
            $this->reconnect();
        }

        return $this->handle;
    }
}
