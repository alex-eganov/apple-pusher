<?php

namespace bIbI4k0\ApplePusher\Curl;

/**
 * Class CachedCurlWrapper
 * @package bIbI4k0\ApplePusher\Curl
 */
class CachedCurlWrapper extends CurlWrapper
{
    /**
     * @var int
     */
    private $lastConnectTimestamp = 0;

    /**
     * @var int
     */
    private $secondsLifetime;

    /**
     * @param int $secondsLifetime connection lifetime in seconds
     */
    public function __construct(int $secondsLifetime)
    {
        $this->secondsLifetime = $secondsLifetime;
    }

    /**
     * @param int $minutesLifetime
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
        return $this->lastConnectTimestamp + $this->secondsLifetime >= time();
    }

    /**
     * Re-init curl
     */
    private function init(): void
    {
        $this->close();
        $this->handle = curl_init();
        $this->lastConnectTimestamp = time();
    }


    /**
     * @inheritDoc
     */
    protected function getCurl()
    {
        if ($this->isExpired()) {
            $this->init();
        }

        return $this->handle;
    }
}
