<?php

namespace bIbI4k0\ApplePusher\Connection;

/**
 * Class Connection
 * @package bIbI4k0\ApplePusher\Connection
 */
class Connection
{
    /**
     * @var \CurlHandle|false|resource
     */
    protected $handle;

    private function __destruct()
    {
        $this->close();
    }

    protected function close(): void
    {
        if (is_resource($this->handle)) {
            curl_close($this->handle);
        }
    }

    /**
     * Returns curl handle
     *
     * @return \CurlHandle|false|resource
     */
    public function getCurl()
    {
        $this->close();
        return $this->handle = curl_init();
    }
}
