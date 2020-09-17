<?php

namespace bIbI4k0\ApplePusher;

/**
 * Class BaseCurlConfig
 * @package bIbI4k0\ApplePusher
 */
class BaseCurlConfig
{
    private $options = [
        CURLOPT_TIMEOUT => 3,
        CURLOPT_CONNECTTIMEOUT => 3,
    ];

    /**
     * @param int $optId curl option id
     * @param $value
     * @return $this
     */
    protected function set(int $optId, $value): self
    {
        $this->options[$optId] = $value;
        return $this;
    }

    /**
     * @param int $seconds
     * @return $this
     */
    public function setTimeout(int $seconds): self
    {
        return $this->set(CURLOPT_TIMEOUT, $seconds);
    }

    /**
     * @param int $seconds
     * @return $this
     */
    public function setConnectionTimeout(int $seconds): self
    {
        return $this->set(CURLOPT_CONNECTTIMEOUT, $seconds);
    }

    /**
     * @return array
     */
    final public function getOptions(): array
    {
        return $this->options;
    }
}
