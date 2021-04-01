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
    private const APNS_HOST = 'api.push.apple.com';
    private const APNS_DEV_HOST = 'api.sandbox.push.apple.com';

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
     * @var BaseCurlConfig
     */
    private $curlConfig;

    /**
     * @param AuthInterface $auth apns auth implementation
     * @param bool $isDevMode if true, notifications will be sent to dev apns server
     * @param BaseCurlConfig|null $config
     */
    public function __construct(AuthInterface $auth, bool $isDevMode, BaseCurlConfig $config = null)
    {
        $this->baseUrl = $isDevMode
            ? self::APNS_DEV_HOST
            : self::APNS_HOST;
        $this->auth = $auth;
        $this->curlConfig = $config ?: new BaseCurlConfig();
        $this->connection = $this->curlConfig->getConnection();
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
        $headers[] = "apns-id: {$push->getUuid()}";

        $headers = array_merge($headers, $this->auth->getRequestHeaders());

        return $headers;
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
        foreach ($this->curlConfig->getOptions() as $opt => $value) {
            if (!in_array($opt, $curlOptions, true)) {
                $curlOptions[$opt] = $value;
            }
        }
        curl_setopt_array($curlHandle, $curlOptions);

        $responseBody = curl_exec($curlHandle);
        if ($responseBody === false) {
            $errNo = curl_errno($curlHandle);
            $errStr = curl_error($curlHandle);
            throw new CurlException($errStr, $errNo);
        }
        $responseCode = curl_getinfo($curlHandle, CURLINFO_RESPONSE_CODE);

        return Response::fromJson($responseBody, $responseCode, $push);
    }
}