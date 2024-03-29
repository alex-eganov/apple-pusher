<?php

namespace bIbI4k0\ApplePusher;

use bIbI4k0\ApplePusher\Exception\CurlException;

/**
 * Class BaseCurlConfig
 * @package bIbI4k0\ApplePusher
 */
class BaseConfig
{
    private const APNS_HOST_PRODUCTION = 'api.push.apple.com';
    private const APNS_HOST_DEVELOPMENT = 'api.sandbox.push.apple.com';

    private const MIN_CURL_VERSION = '7.43.0';

    /**
     * @var string
     */
    private string $baseUrl;

    /**
     * @var array
     */
    private array $curlOptions = [
        CURLOPT_PORT => 443,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 3,
        CURLOPT_CONNECTTIMEOUT => 3,
    ];

    /**
     * @param bool $isDevel
     * @throws CurlException
     */
    public function __construct(bool $isDevel)
    {
        $this->baseUrl = $isDevel
            ? self::APNS_HOST_DEVELOPMENT
            : self::APNS_HOST_PRODUCTION;

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
        $this->curlOptions[$optId] = $value;
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
     * @return string
     */
    final public function getBaseUrl(): string
    {
        return "https://{$this->baseUrl}/";
    }

    /**
     * @return array
     */
    final public function getCurlOptions(): array
    {
        return $this->curlOptions;
    }
}
