<?php

namespace bIbI4k0\ApplePusher;

use bIbI4k0\ApplePusher\Auth\AuthInterface;
use bIbI4k0\ApplePusher\Curl\CurlWrapperInterface;
use bIbI4k0\ApplePusher\Exception\CurlException;
use bIbI4k0\ApplePusher\Exception\ResponseParseException;
use bIbI4k0\ApplePusher\Response\Response;

/**
 * Class Sender
 * @package bIbI4k0\ApplePusher
 */
class Sender
{
    /**
     * @var CurlWrapperInterface
     */
    private $curlWrapper;

    /**
     * @var AuthInterface
     */
    private $auth;

    /**
     * @var BaseConfig
     */
    private $config;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @param AuthInterface $auth apns auth implementation
     * @param CurlWrapperInterface $curlWrapper
     * @param BaseConfig $config
     */
    public function __construct(AuthInterface $auth, CurlWrapperInterface $curlWrapper, BaseConfig $config)
    {
        $this->auth = $auth;
        $this->curlWrapper = $curlWrapper;
        $this->config = $config;
        $this->baseUrl = rtrim($config->getBaseUrl(), '/');
    }

    /**
     * Returns apns endpoint for specified device token
     *
     * @param string $deviceToken
     * @return string
     */
    protected function getUrl(string $deviceToken): string
    {
        return sprintf('%s/3/device/%s', $this->baseUrl, $deviceToken);
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
     * @param Push $push
     * @return array
     */
    protected function prepareCurlOptions(Push $push): array
    {
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

        return $curlOptions;
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
        $curlOptions = $this->prepareCurlOptions($push);
        $raw = $this->curlWrapper->send($curlOptions);

        return Response::fromJson($raw->getBody(), $raw->getCode(), $push);
    }
}