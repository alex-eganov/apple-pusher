<?php

namespace bIbI4k0\ApplePusher;

use bIbI4k0\ApplePusher\Auth\AuthInterface;
use bIbI4k0\ApplePusher\Connection\Connection;
use bIbI4k0\ApplePusher\Exception\CurlException;
use bIbI4k0\ApplePusher\Exception\ResponseParseException;

/**
 * Class Sender
 * @package bIbI4k0\ApplePusher
 */
class Sender
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var AuthInterface
     */
    private $auth;

    /**
     * @var BaseConfig
     */
    private $config;

    /**
     * @param AuthInterface $auth apns auth implementation
     * @param bool $isDevMode if true, notifications will be sent to dev apns server
     * @param BaseConfig|null $config
     */
    public function __construct(AuthInterface $auth, bool $isDevMode, BaseConfig $config = null)
    {
        $config = $config ?: new BaseConfig($isDevMode);

        $this->auth = $auth;
        $this->config = $config;
        $this->baseUrl = $config->getBaseUrl();
        $this->connection = $this->config->getConnection();
    }

    /**
     * Returns apns endpoint for specified device token
     *
     * @param string $deviceToken
     * @return string
     */
    protected function getUrl(string $deviceToken): string
    {
        return sprintf('https://%s/3/device/%s', $this->baseUrl, $deviceToken);
    }

    /**
     * Returns http headers for the request
     *
     * @param Push $push
     * @return array
     */
    protected function prepareHeaders(Push $push): array
    {
        $headers = [];
        foreach ($push->getOptions() as $name => $value) {
            $headers[] = "apns-$name: $value";
        }

        return array_merge($headers, $this->auth->getRequestHeaders());
    }

    /**
     * Send request with push-data to apns server and returns wrapped response from it
     *
     * @param Push $push
     * @return Response wrapped response of apns server
     *
     * @throws CurlException when curl errors was happened
     * @throws ResponseParseException when an error occurred while parsing the JSON-response
     */
    final public function send(Push $push): Response
    {
        $curlHandle = $this->connection->getCurl();

        $curlOptions = [
            CURLOPT_URL => $this->getUrl($push->getDeviceToken()),
            CURLOPT_HTTPHEADER => $this->prepareHeaders($push),
            CURLOPT_POSTFIELDS => json_encode($push),
        ];
        $curlOptions = array_replace($curlOptions, $this->auth->getCurlOptions());
        foreach ($this->config->getCurlOptions() as $opt => $value) {
            if (!in_array($opt, $curlOptions, true)) {
                $curlOptions[$opt] = $value;
            }
        }
        curl_setopt_array($curlHandle, $curlOptions);

        $responseBody = curl_exec($curlHandle);
        if ($responseBody === false) {
            throw new CurlException(curl_error($curlHandle), curl_errno($curlHandle));
        }
        $responseCode = curl_getinfo($curlHandle, CURLINFO_RESPONSE_CODE);

        return Response::fromJson($responseBody, $responseCode, $push);
    }
}