<?php

namespace bIbI4k0\ApplePusher\Curl;

use bIbI4k0\ApplePusher\Exception\CurlException;

/**
 * Interface CurlWrapperInterface
 * @package bIbI4k0\ApplePusher\Curl
 */
interface CurlWrapperInterface
{
    /**
     * @param array $curlOptions
     * @return CurlResponse
     * @throws CurlException
     */
    public function send(array $curlOptions): CurlResponse;
}