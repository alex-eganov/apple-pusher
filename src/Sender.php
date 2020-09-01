<?php

namespace ApplePusher;

use ApplePusher\Auth\AuthInterface;
use ApplePusher\Exceptions\BasePusherException;
use ApplePusher\Exceptions\CurlException;

/**
 * Class Sender
 * @package ApplePusher
 */
class Sender
{
    private const BASE_URL = 'api.push.apple.com';
    private const BASE_URL_DEV = 'api.sandbox.push.apple.com';

    private string $baseUrl;
    private AuthInterface $auth;

    public function __construct(AuthInterface $auth, bool $isDevMode)
    {
        $this->baseUrl = $isDevMode
            ? self::BASE_URL_DEV
            : self::BASE_URL;
        $this->auth = $auth;
    }

    /**
     * @param string $deviceToken
     * @return string
     */
    protected function getUrl(string $deviceToken): string
    {
        return sprintf('https://%s:443/3/device/%s', $this->baseUrl, $deviceToken);
    }

    /**
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

        return $headers;
    }

    /**
     * @param Push $push
     * @return Response
     *
     * @throws BasePusherException
     */
    final public function send(Push $push): Response
    {
        $ch = curl_init($this->getUrl($push->getDevice()));

        curl_setopt_array($ch, array_merge_recursive([
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2,
            CURLOPT_HTTPHEADER => $this->prepareHeaders($push),
            CURLOPT_POSTFIELDS => json_encode($push),
            CURLOPT_RETURNTRANSFER => 1,
        ], $this->auth->getCurlOptions()));

        $responseBody = curl_exec($ch);
        if ($responseBody === false) {
            $errNo = curl_errno($ch);
            $errStr = curl_error($ch);
            throw new CurlException($errStr, $errNo);
        }
        $responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        return new Response((int)$responseCode, $responseBody, $responseBody);
    }
}