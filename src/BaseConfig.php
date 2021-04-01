<?php

namespace bIbI4k0\ApplePusher;

use bIbI4k0\ApplePusher\Connection\Connection;
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
    private $baseUrl;

    /**
     * @var Connection
     */
    private $connection;

    private $curlOptions = [
        CURLOPT_PORT => 443,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 3,
        CURLOPT_CONNECTTIMEOUT => 3,
    ];

    /**
     * @param bool $isDevel
     * @param Connection|null $connection
     * @throws CurlException
     */
    public function __construct(bool $isDevel, Connection $connection = null)
    {
        $this->baseUrl = $isDevel
            ? self::APNS_HOST_DEVELOPMENT
            : self::APNS_HOST_PRODUCTION;

        $this->connection = $connection ?: new Connection();

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
        return $this->baseUrl;
    }

    /**
     * @return Connection
     */
    final public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @return array
     */
    final public function getCurlOptions(): array
    {
        return $this->curlOptions;
    }
}
