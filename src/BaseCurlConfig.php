<?php

namespace bIbI4k0\ApplePusher;

use bIbI4k0\ApplePusher\Exception\CurlException;

/**
 * Class BaseCurlConfig
 * @package bIbI4k0\ApplePusher
 */
class BaseCurlConfig
{
    private const MIN_CURL_VERSION = '7.43.0';

    private $options = [
        CURLOPT_PORT => 443,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 3,
        CURLOPT_CONNECTTIMEOUT => 3,
    ];

    /**
     * @throws CurlException
     */
    public function __construct()
    {
        if (defined('CURL_HTTP_VERSION_2')) {
            $curlHttpVersionConst = CURL_HTTP_VERSION_2;
        } else {
            // Sometimes libcurl version is fresh, but the extension was compiled for the old version of it
            // and does not have a const CURL_HTTP_VERSION_2. In this case used value of this const = 3
            $curlVersion = curl_version()['version'];
            if (version_compare($curlVersion, self::MIN_CURL_VERSION) > 0) {
                $curlHttpVersionConst = 3; // same value than CURL_HTTP_VERSION_2
            }
        }

        if (!isset($curlHttpVersionConst)) {
            throw new CurlException(sprintf(
                'This version of cURL (%s) don\'t support sending via HTTP/2. Upgrade libcurl to version %s or higher.',
                curl_version()['version'],
                self::MIN_CURL_VERSION
            ));
        }

        $this->set(CURLOPT_HTTP_VERSION, $curlHttpVersionConst);
    }

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
     * @param int $port
     * @return $this
     */
    public function setPort(int $port): self
    {
        return $this->set(CURLOPT_PORT, $port);
    }

    /**
     * @return array
     */
    final public function getOptions(): array
    {
        return $this->options;
    }
}
